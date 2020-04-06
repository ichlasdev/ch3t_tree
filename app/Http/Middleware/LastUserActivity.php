<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LastUserActivity
{
    public function handle($request, Closure $next)
    {
        if(Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(1);
            Cache::put('user-is-online-' . Auth::user()->id, true, $expiresAt);

            //last seen
            User::where('id', Auth::user()->id)->update(['last_seen' => (new \DateTime())->format("Y-m-d H:i:s")]);
        }
        return $next($request);
    }
}
