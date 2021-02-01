<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class CommonController extends Controller
{
    //ホームページを返す
    public function index()
    {
        $exitCode = Artisan::call('config:cache');
        return view('index');
    }

    //ログインユーザーの取得
    public function getUser()
    {
        return Auth::user();
    }

    //ログインユーザーのプロフィールを取得
    public function getProfile()
    {
        $user = Auth::user();

        if ($user) {
            return $user->profiles()->with('photos')->first();
        }
    }

    //トークンのリフレッシュ
    public function refreshToken(Request $request)
    {
        $request->session()->regenerateToken();
        return response()->json();
    }
}
