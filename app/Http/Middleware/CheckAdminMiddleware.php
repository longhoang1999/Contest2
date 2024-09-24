<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

class CheckAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Sentinel::getUser();
        // if($user && $user->inRole('admin')){
        //     return $next($request);
        // }
        if($user ){
            return $next($request);
        }
        return redirect()->route("admin.login")->with('error', "Bạn phải đăng nhập");
        // return redirect('https://one.haui.edu.vn/');
    }
}