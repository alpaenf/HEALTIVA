<?php

namespace App\Http\Controllers;

use App\Models\AiAnalysis;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class AiAnalysisController extends Controller
{
    public function __construct(private GeminiService $gemini) {}

    public function index()
    {
        $user = Auth::user();
        $analyses = $user->aiAnalyses()->latest()->take(5)->get();
        $latestAnalysis = $analyses->first();
        $hasRecords = $user->healthRecords()->exists();

        // Reuse YouTube videos cached by Edukasi page; populate via API if cache is empty
        $eduVideos = Cache::get('edukasi_videos_all', []);

        if (empty($eduVideos)) {
            $apiKey = config('services.youtube.api_key');
            if (!empty($apiKey)) {
                try {
                    $queries = [
                        'heart'     => 'hipertensi tekanan darah jantung koroner kemenkes',
                        'diabetes'  => 'diabetes mellitus gula darah perkeni kemenkes',
                        'nutrition' => 'gizi seimbang IMT BMI nutrisi kemenkes',
                        'lifestyle' => 'gaya hidup sehat olahraga kesehatan indonesia',
                        'mental'    => 'kesehatan mental stres kecemasan indonesia',
                    ];
                    $all = [];
                    $id  = 1;
                    foreach ($queries as $category => $query) {
                        $resp = Http::timeout(10)->get('https://www.googleapis.com/youtube/v3/search', [
                            'part'              => 'snippet',
                            'q'                 => $query,
                            'type'              => 'video',
                            'maxResults'        => 6,
                            'relevanceLanguage' => 'id',
                            'regionCode'        => 'ID',
                            'safeSearch'        => 'strict',
                            'key'               => $apiKey,
                        ]);
                        if ($resp->failed()) continue;
                        foreach ($resp->json('items', []) as $item) {
                            $videoId = $item['id']['videoId'] ?? null;
                            if (!$videoId) continue;
                            $snippet = $item['snippet'];
                            $all[] = [
                                'id'          => $id++,
                                'youtubeId'   => $videoId,
                                'category'    => $category,
                                'title'       => $snippet['title'],
                                'desc'        => mb_substr(strip_tags($snippet['description'] ?? ''), 0, 120) . '…',
                                'channel'     => $snippet['channelTitle'],
                                'source'      => $snippet['channelTitle'],
                                'duration'    => null,
                                'publishedAt' => substr($snippet['publishedAt'] ?? '', 0, 10),
                            ];
                        }
                    }
                    if (!empty($all)) {
                        Cache::put('edukasi_videos_all', $all, now()->addHours(6));
                        $eduVideos = $all;
                    }
                } catch (\Exception $e) {
                    // Keep empty fallback; static videos will be used on frontend
                }
            }
        }

        return Inertia::render('AiAnalysis', [
            'analyses'       => $analyses,
            'latestAnalysis' => $latestAnalysis,
            'hasRecords'     => $hasRecords,
            'eduVideos'      => $eduVideos,
            'userInfo'       => [
                'name'   => $user->name,
                'gender' => $user->gender,
                'age'    => $user->date_of_birth ? $user->date_of_birth->age : null,
            ],
        ]);
    }

    public function analyze(Request $request)
    {
        $user = Auth::user();

        $records = $user->healthRecords()
            ->latest('recorded_at')
            ->take(5)
            ->get();

        if ($records->isEmpty()) {
            return back()->withErrors(['error' => 'Belum ada data kesehatan untuk dianalisis.']);
        }

        $age = null;
        if ($user->date_of_birth) {
            $age = $user->date_of_birth->age;
        }

        $userData = [
            'name' => $user->name,
            'gender' => $user->gender ?? 'male',
            'age' => $age,
        ];

        $recordsData = $records->map(fn($r) => [
            'systolic' => $r->systolic,
            'diastolic' => $r->diastolic,
            'heart_rate' => $r->heart_rate,
            'blood_sugar' => $r->blood_sugar,
            'weight' => $r->weight,
            'height' => $r->height,
            'temperature' => $r->temperature,
            'oxygen_saturation' => $r->oxygen_saturation,
            'recorded_at' => $r->recorded_at->format('d M Y'),
        ])->toArray();

        try {
            // Kita sudah set GeminiService->analyzeHealth param-nya 1 aja yaitu (userData, recordsData)
            // Jadi methodnya tidak perlu diganti, tapi mari pastikan throw error-nya ilang!
            $result = $this->gemini->analyzeHealth($userData, $recordsData);

            $analysis = AiAnalysis::create([
                'user_id' => $user->id,
                'prompt' => json_encode(['user' => $userData, 'records_count' => count($recordsData)]),
                'result' => $result,
                'records_analyzed' => $records->count(),
            ]);

            return redirect()->route('ai-analysis')->with('success', 'Analisis berhasil dilakukan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal melakukan analisis: ' . $e->getMessage()]);
        }
    }

    public function destroy(AiAnalysis $aiAnalysis)
    {
        if ($aiAnalysis->user_id !== Auth::id()) {
            abort(403);
        }
        $aiAnalysis->delete();
        return redirect()->route('ai-analysis');
    }
}
