<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('tulis_log_activity')) {
    /**
     * Helper function to log activity
     *
     * @param string $description
     * @param string|null $subjectType
     * @param int|null $subjectId
     * @return void
     */
    function tulis_log_activity($description, $subjectType = null, $subjectId = null)
    {
        try {
            ActivityLog::create([
                'description' => $description,
                'user_id' => Auth::id(), // Get current authenticated user ID
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log the error but don't break the application
            \Log::error('Failed to create activity log: ' . $e->getMessage());
        }
    }
}
