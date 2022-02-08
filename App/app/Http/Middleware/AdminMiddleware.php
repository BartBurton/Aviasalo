<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Maker;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        $adm = Maker::where('remember_token', '=', $token)->first();

        if(!$adm || $adm->role != 'admin' || !$adm->is_active){
            return response('Not acceptable', 406);
        }

        return $next($request);
    }
}
