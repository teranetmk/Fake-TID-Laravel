<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\ReCaptchaRule;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Redirect;



class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    protected $username = 'username';

    public function __construct()
    {

        $this->middleware( 'guest' )->except( 'logout' );

        $this->username = $this->findUsername();
    }


    public function findUsername()
    {
        $login = request()->input( 'email' );

        $fieldType = filter_var( $login, FILTER_VALIDATE_EMAIL ) ? 'email' : 'username';

        request()->merge( [ $fieldType => $login ] );

        return $fieldType;
    }


    public function username()
    {
        return $this->username;
    }


    public function redirectTo()
    {
        return route('home_page');
    }

    // protected function credentials( Request $request )
    // {
    //     $field = $this->field( $request );
    //
    //     return [
    //         $field     => $request->get( $this->username() ),
    //         'password' => $request->get( 'password' ),
    //     ];
    // }
    //
    // public function field( Request $request )
    // {
    //     $email = $this->username();
    //
    //     return 'username';
    //     //return filter_var($request->get($email), FILTER_VALIDATE_EMAIL) ? $email : 'username';
    // }


    protected function validateLogin( Request $request )
    {

        $messages = [
            "{$this->username()}.exists" => __( 'auth.not_exists' ),
            // 'hcaptcha'                    => __( 'frontend/main.captcha_failed' )
        ];

        $request->validate( [
            $this->username()    => 'required|string', // |exists:users,email',
            'password'           => 'required|string',
            'h-captcha-response' => 'required|hcaptcha',
            // 'recaptcha_token' => 'required|recaptcha',
            // 'captcha'         => 'required|captcha',
        ], $messages );
    }


    // protected function validateLogin( Request $request )
    // {
    //     $field = $this->field( $request );
    //
    //     $messages = [
    //         "{$this->username()}.exists" => __( 'auth.not_exists' ),
    //         'captcha'                    => __( 'frontend/main.captcha_failed' )
    //     ];
    //
    //     $this->validate( $request, [
    //         $this->username() => "required|exists:users,{$field}",
    //         'password'        => 'required',
    //         'captcha'         => 'required|captcha'
    //     ], $messages );
    // }


    public function showLoginForm()
    {
        return view( 'frontend.auth.login' );
    }

    public function userLogin( Request $request )
    {

         $messages = [
            "{$this->username()}.exists" => __( 'auth.not_exists' ),
            // 'hcaptcha'                    => __( 'frontend/main.captcha_failed' )
        ];

         $request->validate( [
            $this->username()    => 'required|string', // |exists:users,email',
            'password'           => 'required|string',
            'h-captcha-response' => 'required|hcaptcha',
            // 'recaptcha_token' => 'required|recaptcha',
            // 'captcha'         => 'required|captcha',
        ], $messages );


        if( !Auth::attempt($request->only([$this->username(), 'password']) )){
           return Redirect::back()->withErrors($messages);
        }

        // $user = User::where('email', $request->email)->first();

        // return response()->json([
        //     'status' => true,
        //     'message' => 'User Logged In Successfully',

        // ], 200);

        return Redirect::back();


    }






}
