<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        return auth()->user()->unreadNotifications;
    }

    public function read($id)
    {
        $notification = Notification::find($id);

        if($notification) {
            $notification->read_at = now();
            $notification->save();
        }
    }
}
