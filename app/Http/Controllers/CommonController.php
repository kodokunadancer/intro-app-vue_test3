<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class CommonController extends Controller
{
    /**
     * テンプレートを返す.
     * @return view
     */
    public function index()
    {
        $exitCode = Artisan::call('config:cache');
        return view('index');
    }

    /**
     * ログインユーザーの取得.
     * @return User
     */
    public function getUser()
    {
        return Auth::user();
    }

    /**
     * プロフィールの取得.
     * @return Profile
     */
    public function getProfile()
    {
        $user = Auth::user();

        if ($user) {
            return Profile::where('user_id', $user->id)->with('owner', 'photos')->first();
        }

        return false;
    }

    /**
     * セッションリフレッシュ.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function refreshToken(Request $request)
    {
        $request->session()->regenerateToken();
        return response()->json();
    }
}
