<?php

namespace App\Services;

use App\Models\ActivityLog;
use Carbon\Carbon;

class ActivityLogService
{
    /**
     * Get recent activities
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentActivities($limit = 5)
    {
        return ActivityLog::with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Log an activity
     *
     * @param string $description
     * @param int|null $userId
     * @param string|null $subjectType
     * @param int|null $subjectId
     * @return void
     */
    public function logActivity($description, $userId = null, $subjectType = null, $subjectId = null)
    {
        ActivityLog::create([
            'description' => $description,
            'user_id' => $userId,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}