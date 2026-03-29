<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class AdminNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->take(10)->get();

        return response()->json([
            "unread_count" => $user->unreadNotifications()->count(),
            "items" => $notifications->map(fn (DatabaseNotification $notification) => $this->transform($notification))->values(),
        ]);
    }

    public function markAsRead(Request $request, string $notification): JsonResponse
    {
        $item = $request->user()->notifications()->findOrFail($notification);

        if ($item->read_at === null) {
            $item->markAsRead();
        }

        return response()->json([
            "success" => true,
            "unread_count" => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            "success" => true,
            "unread_count" => 0,
        ]);
    }

    protected function transform(DatabaseNotification $notification): array
    {
        return [
            "id" => $notification->id,
            "kind" => $notification->data["kind"] ?? "umum",
            "title" => $notification->data["title"] ?? "Notifikasi baru",
            "message" => $notification->data["message"] ?? "",
            "url" => $notification->data["url"] ?? route("admin.dashboard"),
            "created_at_label" => optional($notification->created_at)->translatedFormat("d M Y, H:i"),
            "is_read" => $notification->read_at !== null,
        ];
    }
}
