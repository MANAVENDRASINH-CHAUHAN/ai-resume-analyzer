<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function unreadCount(Request $request): JsonResponse
    {
        $unreadCount = $request->user()
            ?->notifications()
            ->unread()
            ->count() ?? 0;

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()
            ?->notifications()
            ->unread()
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
