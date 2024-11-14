<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    protected $COOKIE_EXPIRE_TIME = 1 * (60 * 24 * 7);
    protected $AUTH_COOKIE_NAME;

    public function __construct()
    {
        $this->AUTH_COOKIE_NAME = env('AUTH_COOKIE_NAME');
    }

    /**
     * Logout Function
     * @param Request $request
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        $cookie = Cookie::forget($this->AUTH_COOKIE_NAME);
        $sesion_cookie = Cookie::forget($this->AUTH_COOKIE_NAME);

        return response()->json(['logout' => true], 200)->withCookie($cookie)->withCookie($sesion_cookie);
    }

    /**
     * Check Token Is Expired
     */
    public function checkToken(Request $request)
    {
        if ($request->user()) {
            return response()->json(['token' => true], 200);
        }
        return response()->json(["message" => "Unauthenticated.", "token" => false], 401);
    }

    /**
     * Remove Cookies From Browser When User Token Has Expired
     */
    public function removeCookies()
    {
        $tokenCookie = Cookie::forget($this->AUTH_COOKIE_NAME);
        return response()->json(['success' => true], 200)->withCookie($tokenCookie);
    }
}
