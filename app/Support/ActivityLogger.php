<?php

namespace App\Support;

use App\Models\ActivityLog;

class ActivityLogger
{
    public static function log(
        string $action,
        string $module,
        string $description,
        ?int $userId = null,
        ?int $adminId = null
    ): void {
        ActivityLog::create([
            'user_id' => $userId,
            'admin_id' => $adminId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()?->ip(),
        ]);
    }
}
