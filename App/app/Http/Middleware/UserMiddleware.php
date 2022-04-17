<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        if ($token) {
            $clt = User::where('remember_token', '=', $token)->first()->clients->first();

            if (!!$clt) {
                return $next($request);
            }
        }
        return response('Not acceptable', 406);
    }
}
