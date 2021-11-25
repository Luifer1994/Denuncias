<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $user = User::whereEmail($request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('Laravel')->accessToken;
            return response()->json([
                'res' => true,
                'user' => $user,
                'token' => $token,
                'message' => 'Bienvenido al sistema',
            ], 200);
        } else {
            return response()->json([
                'res' => false,
                'message' => 'Email o password incorrecto',
            ], 400);
        }
    }
}
