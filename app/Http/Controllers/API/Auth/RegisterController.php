<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    protected $COOKIE_EXPIRE_TIME = 1 * (60 * 24 * 7);
    protected $AUTH_COOKIE_NAME;

    public function __construct()
    {
        $this->AUTH_COOKIE_NAME = env('AUTH_COOKIE_NAME');
    }

    /**
     * Register User Function
     * @param Request $request user Data
     * @return $user $token
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:200',
            'email' => 'required|email|max:191|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $result = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            UserProfile::create([
                'user_id' => $user->id,
            ]);

            if (!$user) {
                return response()->json(['error' => 'Registration Faild.'], 500);
            }

            $user->markEmailAsVerified();
            return $user->load('userProfile');
        });

        $token = $result->createToken('authToken')->plainTextToken;
        $encToken = Crypt::encryptString($token);
        $response = [
            'user' => $result,
            'token' => $encToken,
        ];

        $cookie = cookie($this->AUTH_COOKIE_NAME, $encToken, $this->COOKIE_EXPIRE_TIME);

        return response()->json($response, 201)->withCookie($cookie);
    }
}
