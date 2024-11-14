<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    protected $COOKIE_EXPIRE_TIME = 1 * (60 * 24 * 7);
    protected $AUTH_COOKIE_NAME;

    public function __construct()
    {
        $this->AUTH_COOKIE_NAME = env('AUTH_COOKIE_NAME');
    }

    public function __invoke(Request $request)
    {
        if (RateLimiter::tooManyAttempts('login:' . $request->ip(), 5)) {
            return response()->json(['message' => 'Too many login attempts. Please try again in 1 minute.'], 429);
        }

        $request->validate([
            'email' => 'email|required',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->with('userProfile')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::increment('login:' . $request->ip());

            return response()->json(["message" => "The provided credentials do not match our records"], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;
        $encToken = Crypt::encryptString($token);
        $response = [
            'user' => $user,
            'token' => $encToken,
        ];

        $cookie = Cookie::make($this->AUTH_COOKIE_NAME, $encToken, $this->COOKIE_EXPIRE_TIME);

        return response()->json($response, 201)->withCookie($cookie);
    }
}
