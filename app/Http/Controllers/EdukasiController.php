<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class EdukasiController extends Controller
{
    // Search queries per category (Bahasa Indonesia â†’ hasil lebih relevan)
    private array $categoryQueries = [
        'heart'     => 'hipertensi tekanan darah jantung koroner kemenkes',
        'diabetes'  => 'diabetes mellitus gula darah perkeni kemenkes',
        'nutrition' => 'gizi seimbang IMT BMI nutrisi kemenkes',
        'lifestyle' => 'gaya hidup sehat olahraga kesehatan indonesia',
        'mental'    => 'kesehatan mental stres kecemasan indonesia',
    ];

    // Trusted channel IDs (opsional â€” untuk filter jika ingin strict)
    // 'all' fetches a general health mix
    private string $generalQuery = 'edukasi kesehatan kemenkes WHO indonesia';

    public function index()
    {
        $apiKey = config('services.youtube.api_key');

        if (empty($apiKey)) {
            // No API key â†’ use static fallback
            return Inertia::render('Edukasi', [
                'videos'      => $this->staticVideos(),
                'youtubeReady' => false,
            ]);
        }

        try {
            $videos = Cache::remember('edukasi_videos_all', now()->addHours(6), function () use ($apiKey) {
                $all = [];
                $id  = 1;

                foreach ($this->categoryQueries as $category => $query) {
                    $items = $this->searchYoutube($apiKey, $query, 6);
                    foreach ($items as $item) {
                        $videoId = $item['id']['videoId'] ?? null;
                        if (!$videoId) continue;

                        $snippet = $item['snippet'];
                        $all[] = [
                            'id'          => $id++,
                            'youtubeId'   => $videoId,
                            'category'    => $category,
                            'title'       => $snippet['title'],
                            'desc'        => mb_substr(strip_tags($snippet['description'] ?? ''), 0, 120) . 'â€¦',
                            'channel'     => $snippet['channelTitle'],
                            'source'      => $snippet['channelTitle'],
                            'duration'    => null,
                            'publishedAt' => substr($snippet['publishedAt'] ?? '', 0, 10),
                        ];
                    }
                }

                return $all;
            });

            return Inertia::render('Edukasi', [
                'videos'      => $videos,
                'youtubeReady' => true,
            ]);
        } catch (\Exception $e) {
            // API error â†’ fallback to static
            return Inertia::render('Edukasi', [
                'videos'      => $this->staticVideos(),
                'youtubeReady' => false,
                'error'        => 'Gagal memuat video dari YouTube.',
            ]);
        }
    }

    private function searchYoutube(string $apiKey, string $query, int $maxResults = 6): array
    {
        $response = Http::timeout(10)->get('https://www.googleapis.com/youtube/v3/search', [
            'part'             => 'snippet',
            'q'                => $query,
            'type'             => 'video',
            'maxResults'       => $maxResults,
            'relevanceLanguage'=> 'id',
            'regionCode'       => 'ID',
            'safeSearch'       => 'strict',
            'key'              => $apiKey,
        ]);

        if ($response->failed()) {
            throw new \Exception('YouTube API error: ' . $response->status());
        }

        return $response->json('items', []);
    }

    /** Fallback static video list (shown when no API key is set) */
    private function staticVideos(): array
    {
        return [
            ['id'=>1,'youtubeId'=>'b2iSH4VKpXo','category'=>'heart','title'=>'Hipertensi: Penyebab, Gejala dan Penanganannya','desc'=>'Penjelasan lengkap tentang tekanan darah tinggi, penyebab, gejala, dan cara pencegahan berdasarkan pedoman Kemenkes RI.','channel'=>'Kemenkes RI','source'=>'Kemenkes','duration'=>'6:14'],
            ['id'=>2,'youtubeId'=>'l0C_GsEINHs','category'=>'heart','title'=>'Penyakit Jantung Koroner: Kenali dan Cegah Sejak Dini','desc'=>'Faktor risiko penyakit jantung koroner dan langkah pencegahan menurut dokter spesialis kardiovaskular.','channel'=>'PERKI Indonesia','source'=>'PERKI','duration'=>'8:22'],
            ['id'=>3,'youtubeId'=>'fIAB4vdqYaQ','category'=>'diabetes','title'=>'Diabetes Melitus Tipe 2: Gejala dan Pencegahan','desc'=>'Penjelasan diabetes tipe 2, faktor risiko, dan cara pencegahan menurut rekomendasi PERKENI.','channel'=>'Kemenkes RI','source'=>'Kemenkes','duration'=>'7:05'],
            ['id'=>4,'youtubeId'=>'wuU1TGqV6IU','category'=>'diabetes','title'=>'Pola Makan Sehat untuk Penderita Diabetes','desc'=>'Panduan pola makan dan pilihan makanan yang direkomendasikan untuk menjaga kadar gula darah tetap stabil.','channel'=>'PERKENI','source'=>'PERKENI','duration'=>'9:50'],
            ['id'=>5,'youtubeId'=>'tFnFDFNITKs','category'=>'nutrition','title'=>'Cara Menghitung IMT dan Kategori Berat Badan','desc'=>'Pelajari cara menghitung Indeks Massa Tubuh dan interpretasi kategorinya menurut WHO Asia-Pasifik.','channel'=>'Kemenkes RI','source'=>'Kemenkes','duration'=>'4:30'],
            ['id'=>6,'youtubeId'=>'Yt59GKQ7oN8','category'=>'nutrition','title'=>'Pedoman Gizi Seimbang Isi Piringku','desc'=>'Panduan lengkap komposisi makanan sehari-hari sesuai pedoman gizi seimbang Kemenkes RI.','channel'=>'Kemenkes RI','source'=>'Kemenkes','duration'=>'5:10'],
            ['id'=>7,'youtubeId'=>'gHbYJfwFgOU','category'=>'lifestyle','title'=>'Manfaat Olahraga Rutin bagi Kesehatan Jantung','desc'=>'Rekomendasi jenis dan durasi olahraga yang baik untuk kesehatan kardiovaskular menurut WHO.','channel'=>'WHO Indonesia','source'=>'WHO','duration'=>'6:38'],
            ['id'=>8,'youtubeId'=>'MXuSCKuHe7I','category'=>'lifestyle','title'=>'Bahaya Rokok bagi Kesehatan Jantung dan Paru','desc'=>'Dampak merokok pada tekanan darah, jantung, dan paru-paru serta program berhenti merokok dari Kemenkes.','channel'=>'Kemenkes RI','source'=>'Kemenkes','duration'=>'7:15'],
            ['id'=>9,'youtubeId'=>'vgVGKyLpxeU','category'=>'mental','title'=>'Cara Mengelola Stres untuk Kesehatan Optimal','desc'=>'Teknik manajemen stres yang terbukti secara ilmiah dapat menurunkan tekanan darah dan meningkatkan kualitas tidur.','channel'=>'Kemenkes RI','source'=>'Kemenkes','duration'=>'8:00'],
            ['id'=>10,'youtubeId'=>'wcHHSRMVHQE','category'=>'heart','title'=>'Pertolongan Pertama pada Serangan Jantung','desc'=>'Kenali tanda-tanda serangan jantung sejak dini dan langkah pertolongan pertama yang tepat.','channel'=>'PERKI Indonesia','source'=>'PERKI','duration'=>'6:45'],
            ['id'=>11,'youtubeId'=>'VHxpfgfonSk','category'=>'diabetes','title'=>'Olahraga Aman dan Efektif untuk Penderita Diabetes','desc'=>'Jenis dan intensitas olahraga yang direkomendasikan untuk mengontrol gula darah pada penderita diabetes.','channel'=>'PERKENI','source'=>'PERKENI','duration'=>'6:20'],
            ['id'=>12,'youtubeId'=>'nm1TxQj9IsQ','category'=>'lifestyle','title'=>'Tips Tidur Berkualitas untuk Kesehatan Optimal','desc'=>'Hubungan antara kualitas tidur dengan tekanan darah, gula darah, dan kesehatan jantung.','channel'=>'Kemenkes RI','source'=>'Kemenkes','duration'=>'5:45'],
        ];
    }
}
