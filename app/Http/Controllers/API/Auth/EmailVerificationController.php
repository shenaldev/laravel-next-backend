<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationMail;
use App\Models\EmailVerification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class EmailVerificationController extends Controller
{
    private function generateCode()
    {
        $six_digit_random_number = random_int(100000, 999999);
        return $six_digit_random_number;
    }

    /**
     * Send Verification Email Code
     * @param Request $request
     */
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'email|required|string|unique:users,email',
        ]);

        //check is Email Already In Database (email_verification table)
        $email = $request->email;
        $isInDatabase = EmailVerification::where('email', '=', $email)->first();
        $code = $this->generateCode();

        if ($isInDatabase) {
            $isInDatabase->code = $code;
            $isInDatabase->update();
        } else {
            EmailVerification::create([
                'email' => $email,
                'code' => $code,
            ]);
        }

        try {
            Mail::to($email)->send(new EmailVerificationMail($code));
            return response()->json(['error' => false, 'isSuccess' => true], 200);
        } catch (Exception $error) {
            return response()->json(['error' => true, 'isSuccess' => false], 500);
        }
    }

    /**
     * Validate user input code with database code
     * @param Request $request
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|string',
            'code' => 'required',
        ]);

        $email = $request->email;
        $code = $request->code;
        $emailInDB = EmailVerification::where('email', '=', $email)->first();

        if ($emailInDB) {
            if ($emailInDB->code == $code) {
                $emailInDB->delete();
                return response()->json(['valid' => true, 'message' => 'Validaion Success'], 200);
            }
        }

        throw ValidationException::withMessages(['code' => 'Invalid Code']);
    }
}
