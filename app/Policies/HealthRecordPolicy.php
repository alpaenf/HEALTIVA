<?php

namespace App\Policies;

use App\Models\HealthRecord;
use App\Models\User;

class HealthRecordPolicy
{
    public function view(User $user, HealthRecord $healthRecord): bool
    {
        return $user->id === $healthRecord->user_id || $user->isAdmin();
    }

    public function delete(User $user, HealthRecord $healthRecord): bool
    {
        return $user->id === $healthRecord->user_id;
    }
}
