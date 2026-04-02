<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AiAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'prompt',
        'result',
        'records_analyzed',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function ensureShareToken(int $ttlDays = 30): string
    {
        $analysisKey = "ai_analysis:share:analysis:{$this->id}";
        $token = Cache::get($analysisKey);

        if (!$token) {
            do {
                $token = Str::lower(Str::random(12));
            } while (Cache::has("ai_analysis:share:token:{$token}"));
        }

        $ttl = now()->addDays($ttlDays);
        Cache::put($analysisKey, $token, $ttl);
        Cache::put("ai_analysis:share:token:{$token}", $this->id, $ttl);

        return $token;
    }

    public function makeShareUrl(int $ttlDays = 30): string
    {
        return url('/s/' . $this->ensureShareToken($ttlDays));
    }

    public static function findByShareToken(string $token): ?self
    {
        $analysisId = Cache::get("ai_analysis:share:token:{$token}");
        if (!$analysisId) {
            return null;
        }

        return static::find($analysisId);
    }
}
