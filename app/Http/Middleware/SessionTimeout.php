<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('web')->check()) {
            $lastActivity = session('last_activity');

            $expirationTime = config('session.timeout', 10) * 60;
            $currentTime = time();

            if ($lastActivity && ($currentTime - $lastActivity > $expirationTime)) {
                Auth::guard('web')->logout();
                session()->invalidate();
                return redirect()->route('welcome')->with('error', 'Umetolewa nje ya mfumo kwa sababu umekaa kwa dakika ' . config('session.timeout', 10) . ' bila kufanya chochote.');
            }
        }

        session(['last_activity' => time()]);

        return $next($request);
    }
}
