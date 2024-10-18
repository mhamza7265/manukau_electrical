<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Socialite;
use App\User;
use Auth;
use Laravel\Socialite\Facades\Socialite as FacadesSocialite;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function credentials(Request $request){
        return ['email'=>$request->email,'password'=>$request->password,'status'=>'active','role'=>'admin'];
    }
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     $user = User::where('email', $request->email)->first();

    //     if ($user) {
    //         if (Hash::check($request->password, $user->password)) {
    //             if ($user->status === 'active') {
    //                 Auth::login($user);
    //                 request()->session()->flash('success', 'Successfully logged in');
    //                 return redirect()->route('admin');
    //             } else {
    //                 request()->session()->flash('error', 'Your account has been deactivated. Please contact the admin.');
    //                 return redirect()->route('login');
    //             }
    //         } else {
    //             request()->session()->flash('error', 'Invalid password');
    //             return redirect()->route('login');
    //         }
    //     } else {
    //         request()->session()->flash('error', 'Invalid email or password');
    //         return redirect()->route('login.form');
    //     }
    // }

    public function redirect($provider)
    {
        // dd($provider);
     return FacadesSocialite::driver($provider)->redirect();
    }
 
    public function Callback($provider)
    {
        $userSocial =   FacadesSocialite::driver($provider)->stateless()->user();
        $users      =   User::where(['email' => $userSocial->getEmail()])->first();
        // dd($users);
        if($users){
            Auth::login($users);
            return redirect('/')->with('success','You are login from '.$provider);
        }else{
            $user = User::create([
                'name'          => $userSocial->getName(),
                'email'         => $userSocial->getEmail(),
                'image'         => $userSocial->getAvatar(),
                'provider_id'   => $userSocial->getId(),
                'provider'      => $provider,
            ]);
         return redirect()->route('home');
        }
    }
}
