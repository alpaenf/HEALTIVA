<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
        $this->model = env('GEMINI_MODEL', 'gemini-3.0-flash');
    }

    /**
     * Send health data to Gemini API directly and return the analysis result.
     */
    public function analyzeHealth(array $userData, array $records): string
    {
        $prompt = $this->buildHealthPrompt($userData, $records);
        return $this->generateContent($prompt, $userData, $records);
    }

    /**
     * Send a direct chat prompt to Gemini API.
     */
    public function chat(string $prompt, array $history = []): string
    {
        // Untuk saat ini kita pakai simple completion, bisa dikembangkan menjadi chat history model
        return $this->generateContent($prompt);
    }

    private function generateContent(string $prompt, ?array $fallbackUserData = null, ?array $fallbackRecords = null): string
    {
        if (empty($this->apiKey)) {
            // Fallback langung kalau no api key
            return $this->generateFallbackAnalysis($fallbackUserData, $fallbackRecords);
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        try {
            $response = Http::timeout(20)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                }
            } else {
                Log::error('Gemini API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Gemini Connection Error: ' . $e->getMessage());
        }

        // --- OFFLINE FALLBACK ---
        // Jika API error, timeout, limit 429, atau apapun, kita pakai otak cadangan lokal
        return $this->generateFallbackAnalysis($fallbackUserData, $fallbackRecords);
    }

    private function generateFallbackAnalysis(?array $userData, ?array $records): string
    {
        if (!$records || count($records) === 0) {
            return "Belum ada data kesehatan yang bisa dianalisis. Silakan input data terlebih dahulu melalui menu Input Data.";
        }

        $name      = explode(' ', $userData['name'] ?? 'kamu')[0];
        $gender    = $userData['gender'] ?? 'male';
        $age       = isset($userData['age']) && is_numeric($userData['age']) ? (int) $userData['age'] : null;
        $latest    = $records[0];
        $prev      = $records[1] ?? null;

        $fragments     = [];
        $recs          = [];
        $riskScore     = 0;   // 0 = aman, makin besar makin berisiko
        $combinedRisks = [];

        // â”€â”€ 1. TEKANAN DARAH â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (!empty($latest['systolic']) && !empty($latest['diastolic'])) {
            $sys = $latest['systolic'];
            $dia = $latest['diastolic'];

            // Tren
            $trend = '';
            if ($prev && !empty($prev['systolic'])) {
                $diff = $sys - $prev['systolic'];
                if ($diff > 5)       $trend = " (naik {$diff} poin dari pengukuran sebelumnya)";
                elseif ($diff < -5)  $trend = " (turun " . abs($diff) . " poin, membaik)";
                else                 $trend = " (stabil)";
            }

            $cat = '';
            if ($sys >= 180 || $dia >= 110) {
                $cat = 'Hipertensi Derajat 3';
                $riskScore += 4;
                $combinedRisks[] = 'hipertensi';
            } elseif ($sys >= 160 || $dia >= 100) {
                $cat = 'Hipertensi Derajat 2';
                $riskScore += 3;
                $combinedRisks[] = 'hipertensi';
            } elseif ($sys >= 140 && $dia < 90) {
                $cat = 'Hipertensi Sistolik';
                $riskScore += 2;
                $combinedRisks[] = 'hipertensi';
            } elseif ($sys >= 140 || $dia >= 90) {
                $cat = 'Hipertensi Derajat 1';
                $riskScore += 2;
                $combinedRisks[] = 'hipertensi';
            } elseif ($sys >= 130 || $dia >= 85) {
                $cat = 'Normal-tinggi';
                $riskScore += 1;
            } elseif ($sys >= 120 || $dia >= 80) {
                $cat = 'Normal';
            } else {
                $cat = 'Optimal';
            }

            if (str_contains($cat, 'Hipertensi')) {
                $fragments[] = "- **Tekanan Darah ({$sys}/{$dia} mmHg){$trend}:** $cat. Berisiko tinggi.";
                $recs[]      = "Segera konsultasi ke dokter untuk penanganan medis. Kurangi asupan garam harian maksimal 1 sendok teh, dan hindari stres.";
            } elseif ($cat === 'Normal-tinggi') {
                $fragments[] = "- **Tekanan Darah ({$sys}/{$dia} mmHg){$trend}:** $cat. Belum hipertensi, namun harus sangat waspada.";
                $recs[]      = "Kurangi asupan garam, batasi kafein, dan rutin olahraga ringan 30 menit sehari.";
            } else {
                $fragments[] = "- **Tekanan Darah ({$sys}/{$dia} mmHg){$trend}:** $cat.";
                $recs[]      = "Pertahankan pola hidup sehat bebas rokok, rendah garam, dan aktif bergerak setiap hari.";
            }
        }

        // â”€â”€ 2. GULA DARAH â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (!empty($latest['blood_sugar'])) {
            $bs = $latest['blood_sugar'];

            $trend = '';
            if ($prev && !empty($prev['blood_sugar'])) {
                $diff = $bs - $prev['blood_sugar'];
                if ($diff > 10)       $trend = " (naik {$diff} mg/dL)";
                elseif ($diff < -10)  $trend = " (turun " . abs($diff) . " mg/dL, membaik)";
                else                  $trend = " (stabil)";
            }

            if ($bs < 100) {
                $fragments[] = "- **Gula Darah ({$bs} mg/dL){$trend}:** Normal.";
                $recs[]      = "Tetap batasi konsumsi gula tambahan dan minuman manis kemasan.";
            } elseif ($bs < 126) {
                $fragments[] = "- **Gula Darah ({$bs} mg/dL){$trend}:** Pradiabetes. Ini sinyal awal yang harus diwaspadai.";
                $recs[]      = "Kurangi karbohidrat sederhana (nasi putih, roti tawar, mie instan). Tambahkan serat dari sayur dan kacang-kacangan. Lakukan cek darah ulang dalam 3 bulan.";
                $riskScore  += 2;
                $combinedRisks[] = 'pradiabetes';
            } else {
                $fragments[] = "- **Gula Darah ({$bs} mg/dL){$trend}:** Termasuk kategori Diabetes. Memerlukan penanganan medis.";
                $recs[]      = "Konsultasikan ke dokter untuk penanganan diabetes. Hentikan gula putih, mulai konsumsi makanan dengan indeks glikemik rendah (oat, beras merah, ubi).";
                $riskScore  += 4;
                $combinedRisks[] = 'diabetes';
            }
        }

        // â”€â”€ 3. DETAK JANTUNG â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (!empty($latest['heart_rate'])) {
            $hr = $latest['heart_rate'];
            if ($hr >= 60 && $hr <= 100) {
                $fragments[] = "- **Detak Jantung ({$hr} bpm):** Normal dan stabil.";
            } elseif ($hr < 60) {
                $isAthlete   = ($age && $age < 40 && $gender === 'male');
                $note        = $isAthlete ? " Ini bisa normal jika Anda seorang atlet aktif." : " Perlu dipantau, bisa indikasi bradikardia.";
                $fragments[] = "- **Detak Jantung ({$hr} bpm):** Di bawah normal (Bradikardia).{$note}";
                if (!$isAthlete) { $riskScore += 1; }
            } else {
                $fragments[] = "- **Detak Jantung ({$hr} bpm):** Di atas normal (Takikardia). Bisa dipicu stres, dehidrasi, atau kafein.";
                $recs[]      = "Cukupi cairan (2 liter air/hari), kurangi kafein, dan lakukan teknik relaksasi pernapasan dalam.";
                $riskScore  += 2;
            }
        }

        // ── 4. IMT ──────────────────────────────────────────────────
        if (!empty($latest['weight']) && !empty($latest['height'])) {
            $bmi = round($latest['weight'] / pow($latest['height'] / 100, 2), 1);
            if ($bmi < 18.5) {
                $fragments[] = "- **IMT ({$bmi}):** Berat badan kurang (Underweight).";
                $recs[]      = "Tingkatkan asupan kalori berkualitas: protein hewani (telur, ayam, ikan) dan lemak baik (alpukat, kacang). Target kenaikan 0.5 kg/minggu.";
                $riskScore  += 1;
            } elseif ($bmi <= 22.9) {
                $fragments[] = "- **IMT ({$bmi}):** Berat badan normal.";
            } elseif ($bmi <= 24.9) {
                $fragments[] = "- **IMT ({$bmi}):** Beresiko menjadi obes (Kelebihan berat badan). Perlu mulai mengontrol asupan kalori.";
                $recs[]      = "Terapkan manajemen kalori. Kurangi porsi makan berlebih dan mulai rutinitas olahraga secara teratur.";
                $riskScore  += 1;
                $combinedRisks[] = 'overweight';
            } elseif ($bmi <= 29.9) {
                $fragments[] = "- **IMT ({$bmi}):** Obes I. Meningkatkan risiko jantung, diabetes, dan masalah sendi secara progresif.";
                $recs[]      = "Terapkan defisit kalori sedang secara konsisten. Tambahkan porsi serat dan protein agar tidak mudah lapar.";
                $riskScore  += 2;
                $combinedRisks[] = 'obesitas';
            } else {
                $fragments[] = "- **IMT ({$bmi}):** Obes II. Sangat berisiko memicu penyakit komplikasi serius.";
                $recs[]      = "Konsultasikan program penurunan berat badan ke dokter atau ahli gizi. Prioritaskan makanan sehat dan olahraga low-impact.";
                $riskScore  += 3;
                $combinedRisks[] = 'obesitas';
            }
        }

        // â”€â”€ 5. SUHU TUBUH â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (!empty($latest['temperature'])) {
            $temp = $latest['temperature'];
            if ($temp < 36.0) {
                $fragments[] = "- **Suhu Tubuh ({$temp}°C):** Sedikit di bawah normal. Pastikan tubuh cukup hangat.";
            } elseif ($temp <= 37.5) {
                $fragments[] = "- **Suhu Tubuh ({$temp}°C):** Normal.";
            } elseif ($temp <= 38.4) {
                $fragments[] = "- **Suhu Tubuh ({$temp}°C):** Demam ringan. Perbanyak minum air putih.";
                $recs[]      = "Istirahat cukup, kompres hangat di lipatan, minum paracetamol jika perlu. Pantau selama 24 jam.";
                $riskScore  += 1;
            } else {
                $fragments[] = "- **Suhu Tubuh ({$temp}°C):** Demam tinggi. Segera tangani.";
                $recs[]      = "Segera periksakan ke fasilitas kesehatan jika demam tidak turun dalam 2 hari, disertai ruam, atau kejang.";
                $riskScore  += 2;
            }
        }

        // â”€â”€ 6. SpO2 â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (!empty($latest['oxygen_saturation'])) {
            $spo2 = $latest['oxygen_saturation'];
            if ($spo2 >= 95) {
                $fragments[] = "- **Saturasi Oksigen ({$spo2}%):** Normal dan aman.";
            } elseif ($spo2 >= 90) {
                $fragments[] = "- **Saturasi Oksigen ({$spo2}%):** Di bawah normal (Hipoksia Ringan). Perlu diperhatikan.";
                $recs[]      = "Coba latihan pernapasan dalam. Jika sesak napas menyertai, segera ke dokter.";
                $riskScore  += 2;
            } else {
                $fragments[] = "- **Saturasi Oksigen ({$spo2}%):** Sangat rendah. Kondisi darurat.";
                $recs[]      = "Segera cari bantuan medis. SpO2 di bawah 90% memerlukan penanganan oksigen segera.";
                $riskScore  += 5;
            }
        }

        // â”€â”€ DETEKSI RISIKO KOMBINASI (seperti ML rule-based) â”€â”€â”€â”€â”€â”€â”€
        $comboNotes = [];
        if (in_array('hipertensi', $combinedRisks) && (in_array('diabetes', $combinedRisks) || in_array('pradiabetes', $combinedRisks))) {
            $comboNotes[] = "**Perhatian Khusus:** Kombinasi hipertensi dan gula darah tinggi meningkatkan risiko penyakit jantung dan ginjal secara signifikan. Evaluasi menyeluruh ke dokter sangat disarankan.";
            $riskScore   += 2;
        }
        if (in_array('obesitas', $combinedRisks) && in_array('hipertensi', $combinedRisks)) {
            $comboNotes[] = "**Perhatian Khusus:** Obesitas + hipertensi adalah faktor risiko utama stroke. Penurunan berat badan bahkan 5-10% saja sudah bisa menurunkan tekanan darah secara signifikan.";
            $riskScore   += 2;
        }
        if (in_array('obesitas', $combinedRisks) && (in_array('diabetes', $combinedRisks) || in_array('pradiabetes', $combinedRisks))) {
            $comboNotes[] = "**Perhatian Khusus:** Kombinasi obesitas dan gula darah tinggi adalah ciri khas sindrom metabolik. Perubahan gaya hidup agresif (diet + olahraga) sangat krusial sekarang.";
            $riskScore   += 2;
        }

        // â”€â”€ SKOR RISIKO â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if ($riskScore === 0)       $riskLabel = "Sangat Baik";
        elseif ($riskScore <= 2)    $riskLabel = "Baik (ada beberapa hal yang perlu dijaga)";
        elseif ($riskScore <= 5)    $riskLabel = "Perlu Perhatian";
        elseif ($riskScore <= 9)    $riskLabel = "Berisiko Tinggi";
        else                        $riskLabel = "Kritis - Segera Tanggap";

        // â”€â”€ PERSONALISASI BERDASARKAN USIA & GENDER â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $personalNote = "";
        if ($age && $age >= 40 && in_array('hipertensi', $combinedRisks)) {
            $personalNote = "\n\nUsia 40 tahun ke atas dengan hipertensi: risiko stroke dan serangan jantung meningkat 2-3x lipat. Pemeriksaan EKG dan profil lipid tahunan sangat direkomendasikan.";
        } elseif ($gender === 'female' && $age && $age >= 45 && !empty($latest['systolic'])) {
            $personalNote = "\n\nWanita memasuki usia perimenopause (45+) umumnya mengalami peningkatan risiko kardiovaskular. Pemantauan tekanan darah rutin sangat penting di fase ini.";
        }

        // â”€â”€ KOMPILASI OUTPUT â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        $analisaTeks    = count($fragments) > 0 ? implode("\n", $fragments) : "- Data belum cukup lengkap untuk dianalisis rinci.";
        $rekomendasiTeks = count($recs) > 0
            ? implode("\n", array_map(fn($i, $r) => ($i + 1) . ". " . $r, array_keys($recs), $recs))
            : "Pertahankan pola hidup sehat: makan bergizi seimbang, tidur 7-8 jam, dan aktif bergerak setiap hari.";
        $comboTeks = count($comboNotes) > 0 ? "\n\n" . implode("\n\n", $comboNotes) : "";
        $kesimpulan = $riskScore >= 4
            ? "Kondisi {$name} saat ini memerlukan perhatian medis lebih lanjut. Mohon luangkan waktu untuk berkonsultasi dengan dokter secepatnya."
            : "Secara keseluruhan kondisi {$name} terpantau dalam batas yang dapat dikelola. Tetap pertahankan gaya hidup sehat dan pantau secara berkala.";

        return "**1. Ringkasan Kesehatan**\nBerdasarkan data terakhir {$name}, sistem menghasilkan **Skor Risiko: {$riskLabel}**.{$personalNote}\n\n**2. Analisis Detail Parameter**\n{$analisaTeks}{$comboTeks}\n\n**3. Rekomendasi Gaya Hidup**\n{$rekomendasiTeks}\n\n**4. Kesimpulan**\n{$kesimpulan}\n\n---\n*Analisis ini dihasilkan oleh sistem cadangan lokal berbasis aturan klinis. Tidak menggantikan diagnosis dokter.*";
    }

    public function buildHealthPrompt(array $userData, array $records): string
    {
        $name = $userData['name'] ?? 'Pasien';
        $gender = ($userData['gender'] ?? 'male') === 'male' ? 'Laki-laki' : 'Perempuan';
        $age = $userData['age'] ?? 'tidak diketahui';

        $recordsSummary = collect($records)->map(function ($record, $index) {
            $parts = [];
            if (!empty($record['systolic']) && !empty($record['diastolic'])) {
                $parts[] = "Tekanan darah: {$record['systolic']}/{$record['diastolic']} mmHg";
            }
            if (!empty($record['heart_rate'])) {
                $parts[] = "Detak jantung: {$record['heart_rate']} bpm";
            }
            if (!empty($record['blood_sugar'])) {
                $parts[] = "Gula darah: {$record['blood_sugar']} mg/dL";
            }
            if (!empty($record['weight']) && !empty($record['height'])) {
                $bmi = round($record['weight'] / pow($record['height'] / 100, 2), 1);
                $parts[] = "Berat: {$record['weight']} kg, Tinggi: {$record['height']} cm (IMT: {$bmi})";
            }
            if (!empty($record['temperature'])) {
                $parts[] = "Suhu tubuh: {$record['temperature']}°C";
            }
            if (!empty($record['oxygen_saturation'])) {
                $parts[] = "SpO2: {$record['oxygen_saturation']}%";
            }
            $date = $record['recorded_at'] ?? 'tanggal tidak diketahui';
            return "Pengukuran " . ($index + 1) . " ({$date}):\n- " . implode("\n- ", $parts);
        })->join("\n\n");

        return "Analisis data kesehatan berikut dalam Bahasa Indonesia secara singkat, jelas, dan menggunakan format poin. Jangan gunakan emoji atau emotikon sama sekali. Jangan berbasa-basi berlebihan, langsung ke inti analisis. Gunakan gaya bahasa yang hangat, ramah, dan mudah dipahami orang awam.\n\nPasien: {$name}, {$gender}, {$age} tahun.\n\nData kesehatan:\n{$recordsSummary}\n\nBerikan:\n1. **Ringkasan Kesehatan Anda** kondisi saat ini\n2. **Analisis Detail** tiap parameter yang diukur\n3. **Rekomendasi Gaya Hidup** yang spesifik dan actionable\n4. **Kesimpulan & Saran** apakah perlu ke dokter\n\n*Anda adalah asisten medis AI yang bertujuan memberikan informasi, bukan pengganti dokter.*";
    }
}
