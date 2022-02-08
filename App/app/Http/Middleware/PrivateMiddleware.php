<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Maker;

class PrivateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        $mkr = Maker::where('remember_token', '=', $token)->first();

        if(!$mkr || !$mkr->is_active){
            return response('Unauthorized', 401);
        }

        return $next($request);
    }
}
