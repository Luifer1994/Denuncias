<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMailable;
use App\Mail\WellcomeMailable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
            return response()->json($validator->errors(), 400);
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

        $msg = [
            "name" => $newUser->name . " " . $newUser->last_name,
            "email" => $newUser->email,
            "password" => $request->password
        ];

        Mail::to($newUser->email)->send(new WellcomeMailable($msg));

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
            ->where('users.document', 'like', '%' . $request["search"] . '%')
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

        $users = User::select('users.*', 'rols.name as rol', 'professions.name as profession')
            ->leftjoin('rols', 'users.id_rol', 'rols.id')
            ->leftjoin('professions', 'users.id_profession', 'professions.id')
            ->where('users.id_rol', '<>', 2)
            ->where('users.document', 'like', '%' . $request["search"] . '%')
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
            'last_name'     => 'required|string',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required'
        ];
        //Validamos
        $validator = Validator::make($request->all(), $rules);
        //Retorna si falla la validación
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
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
        $newUser->number_contract   = $request->number_contract;
         if ($newUser->save()) {
            $msg = [
                "name" => $newUser->name . " " . $newUser->last_name,
                "email" => $newUser->email,
                "password" => $request->password
            ];

            Mail::to($newUser->email)->send(new WellcomeMailable($msg));
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
        $users = User::select('id', 'name', 'last_name')
            ->where('id_rol', 3)
            ->where('id_profession', 2)
            ->orwhere('id_profession', 4)
            ->orderBy('name')->get();

        return response()->json([
            'res' => true,
            "data" => $users
        ], 200);
    }

    public function ListLawyer()
    {
        $users = User::select('id', 'name', 'last_name')
            ->where('id_rol', 3)
            ->where('id_profession', 3)
            ->orderBy('name')->get();

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

    public function updateAuth(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $user->name         = $request["name"];
        $user->last_name    = $request["last_name"];
        $user->phone        = $request["phone"];
        if ($user->update()) {
            $user->rol = $user->rol;
            return response()->json([
                'res' => true,
                'data' => $user,
                'message' => 'Datos actualizados con éxito'
            ], 200);
        } else {
            return response()->json([
                'res' => false,
                'message' => 'Error al actualizar'
            ], 400);
        }
    }

    public function changePassword(Request $request)
    {
        $user = User::find(Auth::user()->id);

        if (Hash::check($request->current_password, $user->password)) {
            $user->password     = Hash::make($request["password"]);
            if ($user->update()) {
                return response()->json([
                    'res' => true,
                    'message' => 'Contraseña actualizada con éxito'
                ], 200);
            } else {
                return response()->json([
                    'res' => false,
                    'message' => 'Error al actualizar contraseña'
                ], 400);
            }
        } else {
            return response()->json([
                'res' => false,
                'message' => 'Contraseña actual incorrecta'
            ], 400);
        }
    }

    public function sendTokenResetPassword(Request $request)
    {
        //Regla de validación
        $rules = [
            'email'         =>  'required|email'
        ];
        //Validamos
        $validator = Validator::make($request->all(), $rules);
        //Retorna si falla la validación
        if ($validator->fails()) {
            return response()->json($validator->errors(), 402);
        }

        $user = User::whereEmail($request->email)->first();
        if ($user) {
            $user->token_reset_password = Str::random(10);
            if ($user->update()) {
                $msg = ["name" => $user->name . " " . $user->last_name, "cod" => $user->token_reset_password];
                Mail::to($user->email)->send(new ResetPasswordMailable($msg));

                return response()->json([
                    "res" => true,
                    "message" => "Se ha enviado un código de recuperación a tu correo"
                ], 200);
            }
        } else {
            return response()->json([
                "res" => false,
                "message" => "EL correo ingresado no está registrado en nuestro sistema"
            ], 400);
        }
    }

    public function recoveryPassword(Request $request)
    {
        //Regla de validación
        $rules = [
            'cod'         =>  'required',
            'password'    =>  'required'
        ];
        //Validamos
        $validator = Validator::make($request->all(), $rules);
        //Retorna si falla la validación
        if ($validator->fails()) {
            return response()->json($validator->errors(), 402);
        }
        $user = User::where('token_reset_password', $request["cod"])->first();
        if ($user) {
            $user->password = Hash::make($request["password"]);
            $user->token_reset_password = null;
            if ($user->update()) {
                return response()->json([
                    "res" => true,
                    "message" => "Restablecimiento de contraseña éxitoso puedes inicar sesión"
                ], 200);
            } else {
                return response()->json([
                    "res" => false,
                    "message" => "Error al restablecer contraseña"
                ], 400);
            }
        } else {
            return response()->json([
                "res" => false,
                "message" => "Código incorrecto"
            ], 400);
        }
    }
    public function userAuth()
    {
        return Auth::user();
    }
}
