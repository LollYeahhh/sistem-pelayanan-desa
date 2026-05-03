<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Str;

abstract class Controller
{
    protected function dispatchAdminNotification(Notification $notification): void
    {
        NotificationFacade::send(
            User::where("role", "admin")->get(),
            $notification,
        );
    }

    protected function dispatchDatabaseNotification(
        User $user,
        array $data,
        string $type = "App\\Notifications\\InlineDatabaseNotification",
    ): void {
        $user->notifications()->create([
            "id" => (string) Str::uuid(),
            "type" => $type,
            "data" => $data,
        ]);
    }
}
