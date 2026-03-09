<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiKnowledge extends Model
{
    protected $table = 'ai_knowledge';

    protected $fillable = ['title', 'category', 'keywords', 'content', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * Cari pengetahuan yang relevan berdasarkan teks query.
     * Sistem membandingkan kata-kata dalam query dengan keywords tiap entry.
     * Semakin banyak keyword cocok, semakin tinggi skor relevansi.
     */
    public static function findRelevant(string $query, int $limit = 3): \Illuminate\Support\Collection
    {
        $words = array_filter(explode(' ', strtolower($query)), fn($w) => strlen($w) > 2);

        if (empty($words)) {
            return collect();
        }

        return static::where('is_active', true)
            ->get()
            ->map(function ($knowledge) use ($words) {
                $kw = strtolower($knowledge->keywords . ' ' . $knowledge->title . ' ' . $knowledge->category);
                $score = 0;
                foreach ($words as $word) {
                    if (str_contains($kw, $word)) {
                        $score++;
                    }
                }
                $knowledge->_score = $score;
                return $knowledge;
            })
            ->filter(fn($k) => $k->_score > 0)
            ->sortByDesc('_score')
            ->take($limit)
            ->values();
    }
}
