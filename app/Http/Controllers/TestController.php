<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\PushTestNotification;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function push()
    {
        $user = User::first();
        $user->notify(new PushTestNotification);
    }
}
