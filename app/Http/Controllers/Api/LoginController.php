<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * user login api.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 404);
        }
        try {
            $email = $request->email;
            $password = $request->password;
            $user = User::where('email',$email)->first();
            if($user){
                if(Hash::check($password, $user->password)){
                    if(Auth::attempt(['email' => $email,'password' => $password])){
                        $role_name = $user->role ? $user->role->name : '';
                        $token = $user->createToken('API Token',[$role_name]);

                        return response()->json([
                            'token' => $token->plainTextToken,
                            'token type' => 'Bearer',
                            'message' => 'Login Success',
                            'success' => true
                        ],200);
                    }else{
                        return response()->json([
                            'message' => 'Something went wrong in login user, please try after sometime.',
                            'success' => false
                        ],500);
                    }
                }else{
                    return response()->json([
                        'message' => 'Password is wrong!',
                        'success' => false
                    ],500);
                }
            }else{
                return response()->json([
                    'message' => 'User not found!',
                    'success' => false
                ],500);
            }
        } catch (\Throwable $th) {
            Log::error('Error from login user : '. $th);
            return response()->json([
                'message' => $th->getMessage(),
                'success' => false
            ],500);
        }
    }
}
