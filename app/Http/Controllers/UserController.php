<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $token = $user->createToken('Laravel')->accessToken;
            return response()->json([
                'res' => true,
                'data' => ["user" => $user, "token" => $token, "message" => "Bienvenido al sistema"],
            ], 200);
        } else {
            return response()->json([
                'res' => false,
                'message' => 'Email o password incorrecto',
            ], 400);
        }
    }

    public function registerUserInformer(Request $request)
    {
        //Regla de validación
        $rules = [
            'type_people'   => 'required|integer',
            'type_document' => 'required|integer',
            'document'      => 'required|integer',
            'name'          =>  'required|string',
            'email'         =>  'required|email|unique:users,email',
            'password'      =>  'required'
        ];
        //Validamos
        $validator = Validator::make($request->all(), $rules);
        //Retorna si falla la validación
        if ($validator->fails()) {
            return response()->json($validator->errors(), 402);
        }

        $newUser = new User();
        $newUser->id_type_people    = $request["type_people"];
        $newUser->id_type_document  = $request["type_document"];
        $newUser->document          = $request["document"];
        $newUser->name              = $request["name"];
        $newUser->last_name         = $request["last_name"];
        $newUser->email             = $request["email"];
        $newUser->phone             = $request["phone"];
        $newUser->password          = Hash::make($request["password"]);
        $newUser->id_rol            = 2; //Denunciante
        $newUser->id_profession     = 2; //denunciante

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
        $user->tokens->each(function ($token) {
            $token->delete();
        });
        return response()->json([
            'res' => true,
            'message' => 'Hasta pronto',
        ], 200);
    }

    public function ListUserInformers(Request $request)
    {
        //return $request;
        $request["limit"] ? $limit = $request["limit"] : $limit = 10;

        $users = User::where('id_rol', 2)
            ->where('id', 'like', '%' . $request["search"] . '%')
            ->withCount('complaint')->orderBy('created_at', 'desc')->paginate($limit);


        return response()->json([
            'res' => true,
            'message' => 'ok',
            'data' => $users,
        ], 200);
    }

    public function ListUserOfficial(Request $request)
    {
        //return $request;
        $request["limit"] ? $limit = $request["limit"] : $limit = 10;

        $users = User::where('id_rol', 1)->orwhere('id_rol', 3)
            ->where('id', 'like', '%' . $request["search"] . '%')
            ->withCount('complaint')->orderBy('created_at', 'desc')->paginate($limit);


        return response()->json([
            'res' => true,
            'message' => 'ok',
            'data' => $users,
        ], 200);
    }

    public function RegisterOfficial(Request $request)
    {
        if (Auth::user()->rol->id !== 1) {
            return response()->json([
                "res" => false,
                'message' => 'No tienes permisos',
            ], 401);
        }
        //Regla de validación
        $rules = [
            'type_people'   => 'required|integer',
            'type_document' => 'required|integer',
            'document'      => 'required|integer',
            'rol'           => 'required|integer',
            'name'          => 'required|string',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required'
        ];
        //Validamos
        $validator = Validator::make($request->all(), $rules);
        //Retorna si falla la validación
        if ($validator->fails()) {
            return response()->json($validator->errors(), 402);
        }
        $newUser = new User();
        $newUser->id_type_people  = $request->type_people;
        $newUser->id_type_document  = $request->type_document;
        $newUser->document          = $request->document;
        $newUser->name              = $request->name;
        $newUser->last_name         = $request->last_name;
        $newUser->email             = $request->email;
        $newUser->phone             = $request->phone;
        $newUser->password          = Hash::make($request->document);
        $newUser->id_rol            = $request->rol;
        $newUser->id_profession     = $request->profession;
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

    public function ListOfficial()
    {
        $users = User::select('id', 'name')->where('id_rol', 3)->get();

        return response()->json([
            'res' => true,
            "data" => $users
        ], 200);
    }

    public function filterById($id)
    {
        $users = User::select('id', 'name', 'email')->where('id', $id)->first();

        return response()->json([
            'res' => true,
            "data" => $users
        ], 200);
    }

    public function userAuth()
    {
        return Auth::user();
    }
}
