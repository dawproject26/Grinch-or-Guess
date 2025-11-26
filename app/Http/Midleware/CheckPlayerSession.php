<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPlayerSession
{
    public function handle(Request $request, Closure $next)
    {
        
        if (!session()->has('player_id')) {
            return redirect('/');
        }
        return $next($request);
    }
}