<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    public function __invoke(Request $request): void
    {
        try {
            Auth::user()->update(['notification_token' => $request->token]);
        } catch (\Exception $e) {
            Log::critical('Error storing notification token', [
                'userId' => Auth::user()->id,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
