<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register','otp','reset_password']]);

    }//end __construct()


    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email'    => 'required|email',
                'password' => 'required|string|min:6',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $token_validity = (24 * 60);

        $this->guard()->factory()->setTTL($token_validity);

        if (!$token = $this->guard()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);

    }//end login()


    public function register(Request $request)
    {
        $validator = Validator::make
        ($request->all(),
            [
                'name'     => 'required|string|between:2,100',
                'email'    => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [$validator->errors()],
                422
            );
        }

        $user = User::create(
            array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            )
        );

        return response()->json(['message' => 'User created successfully', 'user' => $user]);

    }//end register()


    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'User logged out successfully']);

    }//end logout()


    public function profile()
    {
        return response()->json($this->guard()->user());

    }//end profile()


    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());

    }//end refresh()


    protected function respondWithToken($token)
    {
        return response()->json(
            [
                'token'          => $token,
                'token_type'     => 'bearer',
                'token_validity' => ($this->guard()->factory()->getTTL() * 60),
            ]
        );

    }//end respondWithToken()

    protected function guard()
    {
        return Auth::guard();

    }//end guard()
    public function otp(Request $request){
        
        $validator = Validator::make
        ($request->all(),
            [
                'email'    => 'required|email',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [$validator->errors()],
                422
            );
        }
        
        $email_user = User::where('email',$request->email)->first();
        if($email_user){

            $otp = rand(1000,9999);
            
            $email_user->OTP=$otp;
            $email_user->save();
            return response()->json($otp);
        }
        else{

            return response()->json(['message'=>'No users exists with this email'], 404);
        }
        
    }

    public function reset_password(Request $request){

        $validator = Validator::make
        ($request->all(),
            [
                'OTP' => 'required',
                'password' => 'required|confirmed|min:6',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [$validator->errors()],
                422
            );
        }
        $data = User::where('OTP',$request->OTP)->first();
        if($data){
           $data->password=bcrypt($request->password);
            $data->save();
            return response()->json(['message'=>'Password changed successfully']);
            /*$user = User::create(
                array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                )
            );*/
            return response()->json(['message' => 'Password changed successfully', 'user' => $user]);
        }
        else{
            return response()->json(['message'=>'Invalid OTP']);
        }
    }
   
   

}//end class
