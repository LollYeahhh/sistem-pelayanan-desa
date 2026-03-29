<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

abstract class Controller
{
    protected function dispatchAdminNotification(Notification $notification): void
    {
        NotificationFacade::send(
            User::where("role", "admin")->get(),
            $notification,
        );
    }
}
