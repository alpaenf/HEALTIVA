<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\AiKnowledge;
use App\Models\Patient;

class AiChatController extends Controller
{
    public function index(Request $request)
    {
        $patient = Patient::findOrFail(session('patient_id'));
        $latestRecord = $patient->healthRecords()->latest('recorded_at')->first();

        $initQuestion = null;
        if ($request->query('from_analysis')) {
            $analysis = $patient->aiAnalyses()->find($request->query('from_analysis'));
            if ($analysis) {
                $plain = preg_replace('/#{1,3}\s/', '', $analysis->result ?? '');
                $plain = preg_replace('/\*\*(.+?)\*\*/', '$1', $plain);
                $plain = preg_replace('/\*(.+?)\*/', '$1', $plain);
                $plain = trim(mb_substr($plain, 0, 1500));
                $date = $analysis->created_at->format('d M Y');
                $initQuestion = "Berdasarkan hasil analisis kesehatan saya pada {$date}:\n\n{$plain}\n\nTolong berikan penjelasan lebih lanjut dan rekomendasi konkret yang bisa saya lakukan untuk memperbaiki kondisi kesehatan saya.";
            }
        }

        return Inertia::render('AiChat', [
            'latestRecord' => $latestRecord,
            'initQuestion' => $initQuestion,
        ]);
    }

    public function message(Request $request)
    {
        $request->validate([
            'messages' => 'required|array|min:1',
            'messages.*.role' => 'required|string|in:user,model',
            'messages.*.text' => 'required|string|max:4000',
        ]);

        $patient = Patient::findOrFail(session('patient_id'));
        $latestRecord = $patient->healthRecords()->latest('recorded_at')->first();
        $systemPrompt = "Kamu adalah dr. HEALTIVA, asisten virtual medis di aplikasi HEALTIVA berbasis AI. Jawab dengan sangat ramah, hangat, penuh empati, dan logis. Kamu bukan cuma tempat bertanya soal penyakit fisik, tapi juga teman yang siap mendengarkan curhat (keluh kesah/kecemasan pasien). Berikan kata-kata penyemangat dan validasi perasaan mereka, apalagi orang sakit seringkali butuh dukungan mental. DILARANG KERAS MENGGUNAKAN EMOJI ATAU EMOTIKON APAPUN dalam balasanmu. Jika ditanya hal umum atau diajak curhat, jawablah dengan sabar layaknya sahabat, namun tetap arahkan pelan-pelan ke kesehatan jika relevan. Beri peringatan singkat di akhir setiap sesi awal bahwa kamu bukan dokter sungguhan.\n\nSANGAT PENTING: Jika kamu memberikan penjelasan medis dan merasa video visual bisa membantu (seperti senam, cara pakai insulin, terapi), di akhir jawaban WAJIB tawarkan kepada pengguna apakah ia ingin diputarkan video edukasi terkait (Contoh: 'Apakah kamu mau aku carikan video tentang hal ini?').\n\nJika pengguna membalas setuju (misal: 'ya', 'mau', 'boleh', 'carikan video'), jawablah dengan singkat bahwa kamu akan menampilkan videonya, lalu WAJIB tambahkan kata kunci '[TAMPILKAN_VIDEO]' di akhir pesanmu agar sistem bisa memunculkan video.";
        $systemPrompt .= "\nIdentitas Pasien: " . $patient->name . " (" . ($patient->gender ?? 'Tidak diketahui') . ", " . ($patient->age ?? '?') . " tahun).";

        if ($latestRecord) {
            $systemPrompt .= "\nData Vital Terakhir: Tekanan Darah {$latestRecord->systolic}/{$latestRecord->diastolic} mmHg, Gula Darah {$latestRecord->blood_sugar} mg/dL, HR {$latestRecord->heart_rate} bpm, BB/TB {$latestRecord->weight}kg/{$latestRecord->height}cm.";
        }

        // 1b. Inject relevant knowledge from admin Knowledge Base
        $lastUserText = collect($request->messages)->last()['text'] ?? '';
        $relevantKnowledge = AiKnowledge::findRelevant($lastUserText, 3);
        if ($relevantKnowledge->isNotEmpty()) {
            $systemPrompt .= "\n\n--- PENGETAHUAN PENTING (dari Knowledge Base HEALTIVA) ---";
            foreach ($relevantKnowledge as $k) {
                $systemPrompt .= "\n[{$k->title}]: {$k->content}";
            }
            $systemPrompt .= "\n--- Gunakan pengetahuan di atas untuk menjawab dengan lebih tepat dan pintar. ---";
        }

        $contents = [];
        $lastUserText = '';
        foreach ($request->messages as $msg) {
            $contents[] = [
                'role' => $msg['role'],
                'parts' => [
                    ['text' => $msg['text']]
                ]
            ];
            if ($msg['role'] === 'user') {
                $lastUserText = $msg['text'];
            }
        }

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ],
            'contents' => $contents,
        ];

        $geminiKey = env('GEMINI_API_KEY', '');
        $geminiModel = env('GEMINI_MODEL', 'gemini-3.0-flash');
        $reply = null;

        if ($geminiKey) {
            try {
                // -- HIT GEMINI API --
                $response = Http::timeout(20)->post("https://generativelanguage.googleapis.com/v1beta/models/{$geminiModel}:generateContent?key={$geminiKey}", $payload);

                if ($response->successful()) {
                    $geminiData = $response->json();
                    if (isset($geminiData['candidates'][0]['content']['parts'][0]['text'])) {
                        $reply = $geminiData['candidates'][0]['content']['parts'][0]['text'];
                    }
                } else {
                    Log::error('API Gemini Error: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('Chatbot External Error: ' . $e->getMessage());
            }
        }

        // FALLBACK: Rule-based local reply jika Gemini gagal/habis limit/error
        if (!$reply) {
            $reply = $this->ruleBasedReply($lastUserText, $latestRecord, $patient);
        }

        // -- SEARCH YOUTUBE VIDEOS HANYA JIKA DIBUTUHKAN --
        $videos = [];
        $wantVideo = str_contains($reply, '[TAMPILKAN_VIDEO]') || preg_match('/(^|\b)(ya|mau|boleh|tampilin|lihat|tampilkan|carikan|silakan|gas|oke|ayo|y)(\b|$)/i', trim($lastUserText));
        $youtubeKey = env('YOUTUBE_API_KEY', '');
        
        // Buang tag khusus dari respons balasan
        $reply = trim(str_replace('[TAMPILKAN_VIDEO]', '', $reply));
        
        if ($wantVideo && $youtubeKey) {
            // Analisis obrolan dari yang paling baru untuk tahu topik yang dibicarakan
            $textsToCheck = [$reply];
            foreach (array_reverse($request->messages) as $m) {
                $textsToCheck[] = $m['text'];
            }
            $ytQuery = $this->detectYoutubeQuery($textsToCheck);
            if ($ytQuery) {
                $searchQuery = urlencode($ytQuery);
                try {
                    $ytRes = Http::timeout(10)->get("https://www.googleapis.com/youtube/v3/search", [
                        'part' => 'snippet',
                        'q' => urldecode($searchQuery),
                        'type' => 'video',
                        'key' => $youtubeKey,
                        'maxResults' => 2,
                        'relevanceLanguage' => 'id',
                        'videoDuration' => 'short'
                    ]);

                    if ($ytRes->successful()) {
                        foreach (($ytRes->json()['items'] ?? []) as $item) {
                            $videos[] = [
                                'video_id' => $item['id']['videoId'],
                                'title' => $item['snippet']['title'],
                                'thumbnail' => $item['snippet']['thumbnails']['medium']['url'] ?? '',
                            ];
                        }
                    }
                } catch (\Exception $e) {}
            }
        }

        return response()->json([
            'reply'  => $reply,
            'videos' => $videos,
        ]);
    }

    private function detectYoutubeQuery(array $texts): ?string
    {
        foreach ($texts as $text) {
            $t = strtolower($text);
            if (preg_match('/(stroke|serangan otak)/', $t)) return "pencegahan stroke indonesia kemenkes";
            if (preg_match('/(jantung|kardio|koroner|angina)/', $t)) return "penyakit jantung koroner pencegahan perki indonesia";
            if (preg_match('/(hipertensi|tekanan darah|tensi|sistolik|diastolik)/', $t)) return "hipertensi tekanan darah pencegahan kemenkes";
            if (preg_match('/(diabetes|gula darah|glukosa|pradiabetes)/', $t)) return "diabetes mellitus gula darah perkeni indonesia";
            if (preg_match('/(imt|bmi|berat badan|obesitas|overweight|kurus|kegemukan|diet|kalori)/', $t)) return "diet defisit kalori tips panduan cara menurunkan berat badan obesitas pemula";
            if (preg_match('/(kolesterol|ldl|hdl|trigliserida)/', $t)) return "kolesterol tinggi pencegahan makanan sehat";
            if (preg_match('/(ginjal|cuci darah|kreatinin)/', $t)) return "penyakit ginjal kronis pencegahan indonesia";
            if (preg_match('/(asam urat|gout)/', $t)) return "asam urat gout pencegahan makanan indonesia";
            if (preg_match('/(maag|lambung|gerd|refluks|asam lambung)/', $t)) return "asam lambung maag gerd cara mengatasi";
            if (preg_match('/(olahraga|latihan|gym|lari|aerobik)/', $t)) return "olahraga sehat untuk jantung pemula indonesia";
            if (preg_match('/(tidur|insomnia|susah tidur)/', $t)) return "tips tidur berkualitas kesehatan indonesia";
            if (preg_match('/(stres|stress|kecemasan|anxiety|mental|burnout)/', $t)) return "kelola stres kesehatan mental indonesia";
            if (preg_match('/(spo2|saturasi|oksigen|oximeter|sesak)/', $t)) return "saturasi oksigen spo2 pernapasan kesehatan";
            if (preg_match('/(demam|suhu|panas|meriang)/', $t)) return "demam cara menurunkan penanganan kemenkes";
            if (preg_match('/(anemia|kurang darah|hemoglobin)/', $t)) return "anemia kurang darah pencegahan makanan kemenkes";
            if (preg_match('/(vitamin|suplemen|nutrisi|gizi|makan sehat)/', $t)) return "vitamin suplemen gizi seimbang indonesia kemenkes";
            if (preg_match('/(rokok|merokok|nikotin)/', $t)) return "bahaya rokok dan cara berhenti merokok kemenkes";
        }
        
        // Fallback: Jika tidak ada kata kunci spesifik, cari apa saja yang dibicarakan user
        return "tips kesehatan medis terpercaya dokter";
    }

    private function ruleBasedReply(string $lastMsg, $record, $user): string
    {
        $name = explode(' ', $user->name ?? 'kamu')[0];
        $t = strtolower($lastMsg);

        $d_bp = ($record && $record->systolic && $record->diastolic) ? " *(data kamu: {$record->systolic}/{$record->diastolic} mmHg)*" : "";
        $d_sugar = ($record && $record->blood_sugar) ? " *(data kamu: {$record->blood_sugar} mg/dL)*" : "";
        $d_hr = ($record && $record->heart_rate) ? " *(detak kamu: {$record->heart_rate} bpm)*" : "";
        $d_spo2 = ($record && $record->oxygen_saturation) ? " *(SpO2 kamu: {$record->oxygen_saturation}%)*" : "";
        $d_temp = ($record && $record->temperature) ? " *(suhu kamu: {$record->temperature}°C)*" : "";

        if (preg_match('/(^|\b)(ya|mau|boleh|tampilin|lihat|tampilkan|carikan|silakan|gas|oke|ayo|y)(\b|$)/i', trim($t)) || preg_match('/(ya|mau|boleh|tampilin|lihat|tampilkan|carikan|silakan).*video/i', $t)) {
            return "Baik, ini dia video edukasi yang bisa kamu tonton. Semoga bermanfaat ya! [TAMPILKAN_VIDEO]";
        }

        if (preg_match('/(tekanan darah|hipertensi|sistolik|diastolik|tensi|blood pressure)/', $t)) {
            return "Tekanan darah normal itu **di bawah 120/80 mmHg**{$d_bp}.\n\nKlasifikasi Kemenkes RI:\n- **Normal:** <120/<80\n- **Elevated (Meningkat):** 120-129/<80\n- **Hipertensi Tahap 1:** 130-139/80-89\n- **Hipertensi Tahap 2:** >=140/>=90\n- **Krisis:** >180/>120\n\nCara menurunkannya: kurangi garam (<5g/hari atau cukup 1 sendok teh), olahraga aerobik ringan 30 menit 5x/minggu, dan hindari stres. Kalau konsisten di atas 140/90, mending minum obat resep dokter ya.\n\nApakah kamu mau aku carikan video edukasi terkait hal ini?";
        }

        if (preg_match('/(gula darah|diabetes|glukosa|pradiabetes|blood sugar|hiperglikemia)/', $t)) {
            return "Gula darah puasa normal: **70-99 mg/dL**{$d_sugar}.\n\nKlasifikasi PERKENI 2021:\n- **Normal:** 70-99 mg/dL\n- **Pradiabetes:** 100-125 mg/dL\n- **Diabetes:** >=126 mg/dL (perlu 2x tes pastinya)\n\nTips menjaga tetap stabil: kurangi karbohidrat sederhana (Nasi putih berlebih, gula tepung), perbanyak makanan kaya serat, dan hindari begadang.\n\nApakah kamu mau aku carikan video terkait hal ini?";
        }

        if (preg_match('/(detak|bpm|nadi|denyut|heart rate|aritmia|berdebar)/', $t)) {
            return "Detak jantung normal saat istirahat: **60-100 bpm**{$d_hr}.\n\n- **<60 bpm** Bradikardia (Normal kalau kamu pelari/atlet)\n- **>100 bpm** Takikardia\n\nBisa dipengaruhi hal seperti kafein kopi, stres pikiran, kurang cairan (dehidrasi) atau efek obat. Kalau dada sering berdebar kuat apalagi dibarengi sesak, jangan ditunda langsung periksa aja.\n\nApakah kamu mau lihat video penjelasannya?";
        }
        
        if (preg_match('/(spo2|oksigen|saturasi|oximeter|sesak napas)/', $t)) {
            return "Saturasi oksigen yang sehat itu **>= 95%**{$d_spo2}.\n\n- **95-100%:** Aman dan Normal\n- **90-94%:** Hipoksia ringan (bisa bahaya)\n- **<90%:** Segera ke IGD/RS terdekat!\n\nTips ukur pakai oximeter yang benar: Jari dalam keadaan hangat, jangan pas bergerak, dan pastikan kuku tidak memakai kuteks.\n\nMau cari video edukasinya?";
        }

        if (preg_match('/(suhu|demam|panas|meriang|fever)/', $t)) {
            return "Suhu tubuh manusia normal ada di kisaran **36.1 - 37.5°C**{$d_temp}.\n\n- **>37.5°C:** Demam (bisa karena peradangan/infeksi)\n- **>38.5°C:** Demam tinggi\n- **>40°C:** Gawat darurat!\n\nPertolongan pertama: Banyak minum air putih agar tidak dehidrasi, pakai baju tipis yang menyerap keringat, kompres lipatan pakai air hangat (jangan air dingin/es). Kalau sesudah 3 hari demam nggak turun, segera cek darah Tipes/DBD.\n\nMau lihat penjelasan videonya?";
        }

        if (preg_match('/(kolesterol|ldl|hdl|trigliserida|lemak darah)/', $t)) {
            return "Nilai ideal kolesterol total biasanya <200 mg/dL.\n\n- **LDL (Lemak Jahat):** Idealnya <130 mg/dL \n- **HDL (Lemak Baik):** Baiknya >40 mg/dL\n\nKurangi lemak jenuh seperti gorengan minyak bekas, jeroan, maupun santan kental yang dipanaskan ulang. Perbanyak sayur, oat, dan kacang-kacangan asalkan tidak asam urat.\n\nApakah mau diputarkan video tentang diet kolesterol?";
        }
        
        if (preg_match('/(stroke|serangan otak|bicara pelo|wajah mencong)/', $t)) {
            return "Waspada Stroke! Pakai metode **FAST**:\n\n- **F (Face):** Apakah wajah tiba-tiba mencong sebelah saat tersenyum?\n- **A (Arms):** Apakah salah satu tangan/kaki mendadak lemah tak bertenaga?\n- **S (Speech):** Apakah kata-katanya jadi pelo (cadel) atau gagap tiba-tiba?\n- **T (Time):** Segera bawa ke IGD RS terdekat. Semakin cepat tertangani, bisa selamat dari kelumpuhan!\n\nMau saya carikan video ciri-ciri stroke?";
        }

        if (preg_match('/(ginjal|cuci darah|kreatinin|batu ginjal)/', $t)) {
            return "Tanda waspada ginjal bermasalah:\n- Kencing selalu berbusa banyak (tanda protein bocor)\n- Kaki atau wajah membengkak tiba-tiba\n- Sering mules/mual parah setiap pagi dan gusi gampang berdarah.\n\nSelalu penuhi kebutuhan cairan 2-3 Liter sehari, jangan terlalu sering menahan kencing, dan JANGAN sembarangan minum suplemen / obat anti nyeri warung setiap hari tanpa resep karena sangat jahat bagi ginjal.\n\nApakah kamu mau aku carikan video tentang jaga ginjal?";
        }
        
        if (preg_match('/(asam urat|gout|uric acid|sendi nyeri)/', $t)) {
            return "Asam Urat tinggi terjadi saat zat kristal menumpuk di area sendi (biasanya kaki & lutut).\n\n**Hindari / Stop:** Jeroan (usus, paru, babat), aneka hidangan laut/Seafood (udang, kerang, kepiting), dan Makanan / Minuman yang sangat amat manis buatan.\n**Boleh:** Daging ayam/sapi rebus secukupnya, sayuran hijau.\nMinum banyak air membantu kencing membuang zat purin berlebih.\n\nMau video edukasinya?";
        }

        if (preg_match('/(maag|lambung|gerd|refluks|asam lambung|heartburn)/', $t)) {
            return "Penyakit lambung (GERD/Maag) memang nggak enak.\n\nTips paling ampuh mengatasinya:\n- **Jangan pernah langsung rebahan/tiduran sehabis makan berat**. Tunggu 2 jam.\n- Saat tidur, angkat sedikit posisi leher agar asam tak naik ke kerongkongan.\n- Makanlah porsi sedikit, tapi sering. Bukan sehari sekali tapi langsung brutal.\n- Hindari sambal menyengat, kopi kental, dan minuman soda / berkarbonasi.\n\nApakah kamu mau aku carikan video tentang GERD?";
        }

        if (preg_match('/(stres|stress|kecemasan|anxiety|mental|burnout|depresi)/', $t)) {
            return "Tenangkan pikiranmu dulu ya {$name}! Stres itu normal.\n\nCara redakan secara cepat (teknik 4-7-8):\n1. Tarik napas pelan 4 detik\n2. Tahan napasmu 7 detik\n3. Hembuskan dari mulut pelan 8 detik\nLakukan rutin. Ingat, kesehatan mentalku sepenting kesehatan fisik! Kurangi memikirkan omongan orang, fokus istirahat dan luangkan waktu untuk dirimu sendiri.\n\nMau saya carikan video teknik relaksasi?";
        }

        if (preg_match('/(olahraga|latihan|gym|lari|aerobik|exercise)/', $t)) {
            return "Standar Kemenkes dan WHO:\nUsahakan berolahraga **150 menit per minggu**. Alias 30 menit sehari selama 5 hari saja.\n\nKalau kamu pemula, mulai saja dari sekedar jalan santai sore atau peregangan ringan di rumah pakai YouTube. Olahraga bisa melancarkan jantung, nurunin kolesterol, dan lepas endorfin agar gak gampang sedih/stres.\n\nApakah kamu mau rekomendasi video olahraga di rumah?";
        }

        if (preg_match('/(tidur|insomnia|susah tidur|sleep|ngantuk)/', $t)) {
            return "Orang dewasa butuh tidur nyenyak **7 - 8 jam full** setiap malam.\n\nTubuh kita membakar lemak dan dandan ulang alias regenerasi saat kita pulas. \nBiar cepat tidur: STOP scroll Tiktok/IG 1 jam sebelum naik kasur (cahaya hp nipu otak kirain masih siang). Gelapkan lampu, dan pasang kipas/AC biar dingin.\n\nApakah kamu mau aku carikan video atasi sulit tidur?";
        }
        
        if (preg_match('/(vitamin|suplemen|nutrisi|gizi|makan sehat)/', $t)) {
            return "Nutrisi utama itu harus dari makanan asli (Sayur, Lauk, Buah), bukan obat pil vitamin lho.\n\nTapi kalau merasa kurang bugar, pertimbangkan:\n- Vitamin C (buat imun)\n- Vitamin D & Kalsium (untuk tulang â€” seringlah berjemur jam 8 pagi)\n- Minyak Ikan / Omega 3 (bagus ngebersihin lemak di jantung).\n\nApakah mau lihat video soal gizi seimbang?";
        }

        if (preg_match('/(imt|bmi|berat badan|obesitas|overweight|kurus|kegemukan|diet|kalori)/', $t)) {
            return "Untuk menjaga berat badan ideal (IMT Normal: 18.5 - 24.9), kuncinya ada pada pola makan (Defisit kalori jika ingin kurus, Surplus kalori jika ingin gemuk).\n\nHindari **Gula dan Tepung berlebih**. Perbanyak Protein (ayam, ikan, telur) supaya kamu kenyang lebih lama, dan barengi olahraga 30 menit sehari agar otot terbentuk dan tidak gelambir.\n\nApakah kamu mau aku carikan video edukasi diet yang aman?";
        }

        if (preg_match('/(siapa kamu|kamu itu apa|kamu robot|kamu ai|kamu manusia|kamu dokter)/', $t)) {
            return "Aku dr. HEALTIVA, asisten kesehatan virtual berbasis AI di aplikasi HEALTIVA. Aku dirancang untuk membantu memantau dan memahami kondisi kesehatanmu. Walaupun namanya dr. HEALTIVA, aku bukan dokter sungguhan, ya. Anggap saja aku seperti teman yang paham soal kesehatan dan siap dengerin keluhanmu kapan saja.";
        }

        if (preg_match('/(kabar|gimana kabar|apa kabar|lagi apa|lagi ngapain|sedang apa)/', $t)) {
            $replies = [
                "Baik, siap membantu {$name}! Kamu sendiri gimana kabarnya? Kalau ada yang terasa kurang enak di badan, cerita aja.",
                "Alhamdulillah baik. Kamu lagi gimana, {$name}? Ada keluhan kesehatan hari ini?",
                "Aku standby terus nih buat {$name}. Ada yang mau diceritain soal kesehatan?",
            ];
            return $replies[array_rand($replies)];
        }

        if (preg_match('/(bosen|bosan|gabut|ga ada kerjaan|ngga ada kerjaan|nganggur|santai)/', $t)) {
            $replies = [
                "Wah sama dong {$name}, aku juga nunggu pertanyaan dari kamu terus. Kalau bosen, mending gerak ya! Bahkan 10 menit stretching ringan sudah lumayan bagus buat tubuh.",
                "Kalau lagi gabut, itu waktu yang bagus buat cek kesehatan atau input data vital terbaru ke HEALTIVA. Biar kebiasaan pantau kesehatannya terjaga.",
            ];
            return $replies[array_rand($replies)];
        }

        if (preg_match('/(lapar|laper|makan apa|mau makan|belum makan)/', $t)) {
            $replies = [
                "Haha sudah waktunya makan ya {$name}. Pilih makanan yang ada proteinnya dulu biar kenyang lebih lama â€” telur, dada ayam, atau ikan. Hindari yang terlalu banyak karbohidrat sederhana kalau mau jaga berat badan.",
                "Usahakan makan makanan lengkap ya, {$name}. Karbohidrat, protein, sayur, sedikit lemak baik. Jangan skip makan kalau mau metabolisme tetap stabil.",
            ];
            return $replies[array_rand($replies)];
        }

        if (preg_match('/(capek|lelah|kelelahan|ngantuk|lemes|loyo|exhausted)/', $t)) {
            $replies = [
                "Istirahat dulu ya {$name}, tubuh memang perlu recovered. Kalau capeknya sudah beberapa hari dan terasa tidak wajar, bisa jadi itu sinyal anemia atau kurang vitamin B12. Coba cek darah kalau sempat.",
                "Capek itu wajar asal jangan dibiarkan terus-terusan. Tidur 7-8 jam, cukup minum air putih, dan kurangi begadang. Kalau capek sampai kepala berat atau jantung berdebar, itu perlu lebih diperhatikan.",
            ];
            return $replies[array_rand($replies)];
        }

        if (preg_match('/(sedih|galau|down|murung|nangis|depresi ringan|kesepian|ngerasa sendirian)/', $t)) {
            return "Aku denger kamu, {$name}. Perasaan seperti itu wajar dialami siapa saja. Tidak perlu merasa sendirian. Kalau mau cerita, aku di sini. Tapi kalau rasa sedih itu sudah sangat mengganggu aktivitas harian atau berlangsung lebih dari 2 minggu, ada baiknya bicara langsung ke psikolog ya.";
        }

        if (preg_match('/(halo|hai|hi|hey|hei|pagi|siang|sore|malam|selamat)/', $t)) {
            return "Hei {$name}! Ada yang bisa aku bantu hari ini? Mau tanya soal diet berat badan, tensi, asam urat, masalah lambung, atau curhat soal kesehatan juga boleh!";
        }

        if (preg_match('/(makasih|thanks|terima kasih|thx|tq)/', $t)) {
            return "Sama-sama {$name}! Ingat, investasi terbaik itu kesehatan fisikmu. Kalau ada keluhan aneh, kabari lagi ya.";
        }
        
        $randomFallback = [
            "Hmm, pertanyaan yang menarik {$name}! Sayangnya untuk detail medis spesifik soal itu aku belum bisa memastikan. Boleh coba tanyakan topik lain seperti Tensi, Gula Darah, Kolesterol, Diet, atau Asam Urat?",
            "Maaf {$name}, aku agak kurang menangkap maksud lengkapmu. Bisa coba tanyakan ulang dengan kata kunci spesifik seperti: penanganan maag, cara menurunkan kolesterol, pola diet, atau tensi normal?",
            "Pertanyaan yang bagus! Tapi dr. HEALTIVA butuh spesifikasi lebih. Kamu bisa bertanya soal tekanan darah, obesitas/diet, masalah lambung, tanda penyakit jantung atau organ ginjal ya."
        ];

        return $randomFallback[array_rand($randomFallback)];
    }
}
