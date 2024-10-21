<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request)
    {
        // Verify the email and handle the response
        if (! hash_equals((string) $request->route('id'), (string) $request->user()->getKey())) {
            return redirect($this->redirectTo)->with('error', 'Invalid verification link.');
        }

        $request->user()->markEmailAsVerified();

        return $this->verified($request);
    }

    protected function verified(Request $request)
    {
        // Flash a message to the session
        request()->session()->flash('success', 'Your email has been verified successfully!');

        return redirect($this->redirectTo);
    }
}
