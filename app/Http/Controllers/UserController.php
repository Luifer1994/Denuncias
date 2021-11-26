<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $user["rol"]        = $user->rol;
            $user["profession"] = $user->profession;
            $token = $user->createToken('Laravel')->accessToken;
            return response()->json([
                'res' => true,
                'data' => ["user" => $user, "token" => $token],
            ], 200);
        } else {
            return response()->json([
                'res' => false,
                'message' => 'Email o password incorrecto',
            ], 400);
        }
    }

    public function registerUserWhistleblower(Request $request)
    {
        //Regla de validación
        $rules = [
            'name'      =>  'required|string',
            'email'     =>  'required|email|unique:users,email',
            'password'  =>  'required|min:8'
        ];
        //Validamos
        $validator = Validator::make($request->all(), $rules);
        //Retorna si falla la validación
        if ($validator->fails()) {
            return $validator->errors();
        }

        $newUser = new User();
        $newUser->name          = $request["name"];
        $newUser->email         = $request["email"];
        $newUser->phone         = $request["phone"];
        $newUser->password      = Hash::make($request["password"]);
        $newUser->id_rol        = 2; //Denunciante
        $newUser->id_profession = 2; //denunciante

        if ($newUser->save()) {
            return response()->json([
                "res" => true,
                "data" => $newUser,
                'message' => 'Registro exitoso',
            ], 200);
        } else {
            return response()->json([
                "res" => false,
                'message' => 'Error al guardar el registro',
            ], 400);
        }
    }

    public function registerUserProfessional(Request $request)
    {
        if (Auth::user()->rol->id !== 1) {
            return response()->json([
                "res" => false,
                'message' => 'No tienes permisos',
            ], 401);
        }
        //Regla de validación
        $rules = [
            'name'          =>  'required|string',
            'email'         =>  'required|email|unique:users,email',
            'password'      =>  'required|min:8',
            'id_rol'        =>  'required|integer',
            'id_profession' =>  'required|integer'
        ];
        //Validamos
        $validator = Validator::make($request->all(), $rules);
        //Retorna si falla la validación
        if ($validator->fails()) {
            return $validator->errors();
        }

        $newUser = new User();
        $newUser->name          = $request["name"];
        $newUser->email         = $request["email"];
        $newUser->phone         = $request["phone"];
        $newUser->password      = Hash::make($request["password"]);
        $newUser->id_rol        = $request["id_rol"];
        $newUser->id_profession = $request["id_profession"];

        if ($newUser->save()) {
            return response()->json([
                "res" => true,
                "data" => $newUser,
                'message' => 'Registro exitoso',
            ], 200);
        } else {
            return response()->json([
                "res" => false,
                'message' => 'Error al guardar el registro',
            ], 400);
        }
    }

    public function logout()
    {
        //Obtenemos usuario logeado
        $user = Auth::user();
        //Busca todos los token del usuario en la base de datos y los eliminamos;
        $user->tokens->each(function($token){
           $token->delete();
        });
        return response()->json([
            'res' => true,
            'message'=> 'Hasta pronto',
        ],200);
    }
}
