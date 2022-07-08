<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AclUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!is_null(Auth::id())) {
            // Helper::loggingApp($request->ip(), Auth::id(), 'kode transaksi : '.$request->code ?? '' );
            $status = Auth::user()->status;
            $users_acl_id = Auth::user()->users_acls_id;
            $sudo = Auth::user()->sudo;
            Session::put('_status', $status);
            Session::put('_users_acl_id', $users_acl_id);
            Session::put('_sudo', $sudo);
            $users_acl = DB::table('users_acls')->where('id', $users_acl_id)->first();
            if ($sudo == 1) {
                $users_acl = DB::table('users_acls')->where('id', 0)->first();
            }
            Session::put('_users_acl', $users_acl);
            // set last_login
            DB::table('users')->where('id', Auth::id())->update(['last_login' => Carbon::now()]);
            if ($status == 0) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('dashboard');
            }
        }

        return $next($request);
    }
}
