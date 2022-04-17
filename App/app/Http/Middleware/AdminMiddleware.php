<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        if ($token) {
            $emp = User::where('remember_token', '=', $token)->first()->employers->first();

            if (!!$emp && $emp->is_active && $emp->role->name == 'admin') {
                return $next($request);
            }
        }
        return response('Not acceptable', 406);
    }
}
