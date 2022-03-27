<?php

namespace App\Http\Controllers;

use App\Mail\ChangeStatusMailable;
use App\Mail\ConfirmSenComplaintMailable;
use App\Mail\EmailMailable;
use App\Mail\NewComplaintMailable;
use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\Media;
use App\Models\MediaResponse;
use App\Models\ResponseComplaint;
use App\Models\StateComplaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Exports\ComplaintsExport;
use App\Mail\NotifyStateAdmin;
use App\Mail\NotifyStateFunt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class ComplaintController extends Controller
{

    public function index(Request $request)
    {
        $request["limit"] ? $limit = $request["limit"] : $limit = 10;
        $id_user = Auth::user()->id;

        if (Auth::user()->id_rol !== 1 && Auth::user()->id_profession == 2) {
            $complaints = Complaint::select(
                'complaints.id',
                'complaints.cod',
                'complaint_types.name as type_complaint',
                'users.name as informer',
                'state_complaints.name as state',
                'complaints.latitude',
                'complaints.longitude',
                'complaints.name_offender',
                'complaints.description',
                'complaints.created_at'
            )
                ->leftjoin('users', 'complaints.id_user', '=', 'users.id')
                ->join('complaint_types', 'complaints.id_complaint_type', '=', 'complaint_types.id')
                ->join('state_complaints', 'complaints.id_state', '=', 'state_complaints.id')
                ->where('id_user_asigne', $id_user)
               // ->where('complaints.city_id', Auth::user()->city_id)
                ->where('complaints.cod', 'like', '%' . $request["search"] . '%')
                ->where('complaints.id_state', 'like', '%' . $request["state"] . '%')
                ->OrderBy('id', 'desc')->paginate($limit);
        } elseif (Auth::user()->id_rol !== 1 && Auth::user()->id_profession == 3) {
            $complaints = Complaint::select(
                'complaints.id',
                'complaints.cod',
                'complaint_types.name as type_complaint',
                'users.name as informer',
                'state_complaints.name as state',
                'complaints.latitude',
                'complaints.longitude',
                'complaints.name_offender',
                'complaints.description',
                'complaints.created_at'
            )
                ->leftjoin('users', 'complaints.id_user', '=', 'users.id')
                ->join('complaint_types', 'complaints.id_complaint_type', '=', 'complaint_types.id')
                ->join('state_complaints', 'complaints.id_state', '=', 'state_complaints.id')
                ->where('id_user_inquest', $id_user)
               // ->where('complaints.city_id', Auth::user()->city_id)
                ->where('complaints.cod', 'like', '%' . $request["search"] . '%')
                ->where('complaints.id_state', 'like', '%' . $request["state"] . '%')
                ->OrderBy('id', 'desc')->paginate($limit);
        }elseif (Auth::user()->id_rol !== 1 && Auth::user()->id_profession == 4) {
            $complaints = Complaint::select(
                'complaints.id',
                'complaints.cod',
                'complaint_types.name as type_complaint',
                'users.name as informer',
                'state_complaints.name as state',
                'complaints.latitude',
                'complaints.longitude',
                'complaints.name_offender',
                'complaints.description',
                'complaints.created_at'
            )
                ->leftjoin('users', 'complaints.id_user', '=', 'users.id')
                ->join('complaint_types', 'complaints.id_complaint_type', '=', 'complaint_types.id')
                ->join('state_complaints', 'complaints.id_state', '=', 'state_complaints.id')
                ->where('id_user_inquest', $id_user)
               // ->where('complaints.city_id', Auth::user()->city_id)
                ->where('complaints.cod', 'like', '%' . $request["search"] . '%')
                ->where('complaints.id_state', 'like', '%' . $request["state"] . '%')
                ->OrderBy('id', 'desc')->paginate($limit);
        }
         else {
            $complaints = Complaint::select(
                'complaints.id',
                'complaints.cod',
                'complaint_types.name as type_complaint',
                'users.name as informer',
                'state_complaints.name as state',
                'complaints.latitude',
                'complaints.longitude',
                'complaints.name_offender',
                'complaints.description',
                'complaints.created_at'
            )
                ->leftjoin('users', 'complaints.id_user', '=', 'users.id')
                ->join('complaint_types', 'complaints.id_complaint_type', '=', 'complaint_types.id')
                ->join('state_complaints', 'complaints.id_state', '=', 'state_complaints.id')
                ->where('complaints.cod', 'like', '%' . $request["search"] . '%')
                ->where('complaints.id_state', 'like', '%' . $request["state"] . '%')
                ->OrderBy('id', 'desc')->paginate($limit);
        }



        return response()->json([
            'res' => true,
            'message' => 'ok',
            'data' => $complaints,
        ], 200);
    }

    public function export()
    {
        $complaints = Complaint::select(
            'complaints.cod',
            'complaints.address',
            'complaints.latitude',
            'complaints.longitude',
            'complaints.description',
            'complaints.name_offender',
            DB::raw('DATE_FORMAT(complaints.created_at, "%d-%M-%Y") as date_create'),
            'complaint_types.name as type_complaint',
            DB::raw('CONCAT(users.name, " ", users.last_name) AS informer'),
            DB::raw('CONCAT(user_asigne.name, " ", user_asigne.last_name) AS user_asigne'),
            DB::raw('CONCAT(user_inquest.name, " ", user_inquest.last_name) AS user_inquest'),
            'state_complaints.name as state'
        )
            ->join('complaint_types', 'complaints.id_complaint_type', 'complaint_types.id')
            ->leftjoin('users', 'complaints.id_user', 'users.id')
            ->leftjoin('users as user_asigne', 'complaints.id_user_asigne', 'user_asigne.id')
            ->leftjoin('users as user_inquest', 'complaints.id_user_inquest', 'user_inquest.id')
            ->join('state_complaints', 'complaints.id_state', 'state_complaints.id')
            ->get();


        return $complaints;
    }

    public function listByUser(Request $request)
    {
        $request["limit"] ? $limit = $request["limit"] : $limit = 10;

        $complaints = Complaint::select(
            'complaints.id',
            'complaints.cod',
            'complaint_types.name as type_complaint',
            'users.name as informer',
            'state_complaints.name as state',
            'complaints.latitude',
            'complaints.longitude',
            'complaints.name_offender',
            'complaints.description',
            'complaints.created_at'
        )
            ->leftjoin('users', 'complaints.id_user', '=', 'users.id')
            ->join('complaint_types', 'complaints.id_complaint_type', '=', 'complaint_types.id')
            ->join('state_complaints', 'complaints.id_state', '=', 'state_complaints.id')
            ->where('complaints.id_user', Auth::user()->id)
            ->where('complaints.city_id', Auth::user()->city_id)
            ->orWhere('complaints.cod', 'like', '%' . $request["search"] . '%')
            ->OrderBy('id', 'desc')->paginate($limit);

        return response()->json([
            'res' => true,
            'message' => 'ok',
            'data' => $complaints,
        ], 200);
    }

    public function store(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user()->id;
        } else {
            $user = null;
        }

        $rules = [
            'description' => 'required|string',
            'id_complaint_type' => 'required|integer'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if (ComplaintType::where('id', $request->id_complaint_type)->where('state', 1)->first()) {

            $newComplaint = new Complaint();
            $newComplaint->latitude             = $request->latitude;
            $newComplaint->longitude            = $request->longitude;
            $newComplaint->address              = $request->address;
            $newComplaint->name_offender        = $request->name_offender;
            $newComplaint->description          = $request->description;
            $newComplaint->id_complaint_type    = $request->id_complaint_type;
            $newComplaint->id_user              = $user;
            $newComplaint->id_state             = 1;
            $newComplaint->city_id              = 1; //Por ahora

            if ($newComplaint->save()) {
                $lasComplaint = Complaint::latest('id')->first();
                $lasComplaint->cod = "APP-" . $lasComplaint->id;
                if ($lasComplaint->update()) {
                    if ($request->media && count($request->media) > 0) {
                        foreach ($request->media as $value) {
                            $newMedia = new Media();
                            $newMedia->type = $value["type"];
                            $newMedia->url  = $value["url"];
                            $newMedia->id_complaint = $newComplaint->id;

                            if (!$newMedia->save()) {
                                return response()->json([
                                    "res" => false,
                                    "message" => 'Error al guardar las evidencias'
                                ], 400);
                            }
                        }
                        $newResponse = new ResponseComplaint();
                        $newResponse->description           = $lasComplaint->description;
                        $newResponse->id_complaint          = $lasComplaint->id;
                        $newResponse->id_state_complaint    = $lasComplaint->id_state;
                        $newResponse->id_user               = $lasComplaint->id_user;
                        $newResponse->save();
                    }
                } else {
                    return response()->json([
                        "res" => false,
                        "message" => 'Error al guardar el registro'
                    ], 400);
                }
                if (Auth::check()) {
                    $msg = [
                        "name" => Auth::user()->name . " " . Auth::user()->last_name,
                        "cod" => $lasComplaint->cod,
                    ];
                    Mail::to(Auth::user()->email)->send(new ConfirmSenComplaintMailable($msg));
                }
                $admins = User::where('id_rol', 1)->get();
                foreach ($admins as $value) {
                    $msg = [
                        "name" => $value->name . " " . $value->last_name,
                        "cod" => $lasComplaint->cod,
                    ];
                    Mail::to($value->email)->send(new NewComplaintMailable($msg));
                }
                // return $admins;
                return response()->json([
                    "res" => true,
                    "data" => ["complaint" => $lasComplaint->cod],
                    "message" => 'Denuncia creada con éxito'
                ], 200);
            } else {
                return response()->json([
                    "res" => false,
                    "message" => 'Error al registrar la Denuncia'
                ], 400);
            }
        } else {
            return response()->json([
                "res" => false,
                "message" => 'El tipo de denuncia no existe'
            ], 400);
        };
    }

    public function show($id)
    {
        $complaint = Complaint::select(
            'complaints.id',
            'complaints.cod',
            'complaint_types.name as type_complaint',
            'users.name as informer',
            'users.last_name as last_name_informer',
            'uf.name as user_asigne',
            'uf.last_name as last_name_user_asigne',
            'us.name as name_user_inquest',
            'us.last_name as last_name_user_inquest',
            'state_complaints.name as state',
            'complaints.latitude',
            'complaints.longitude',
            'complaints.name_offender',
            'complaints.description',
            'complaints.created_at',
            'complaints.address',
            'complaints.id_user_asigne',
            'complaints.id_user_inquest'
        )
            ->leftjoin('users', 'complaints.id_user', '=', 'users.id')
            ->leftjoin('users as uf', 'complaints.id_user_asigne', '=', 'uf.id')
            ->leftjoin('users as us', 'complaints.id_user_inquest', '=', 'us.id')
            ->join('complaint_types', 'complaints.id_complaint_type', '=', 'complaint_types.id')
            ->join('state_complaints', 'complaints.id_state', '=', 'state_complaints.id')
            ->where('complaints.id', $id)
            ->with('media')
            ->with('ResponseComplaint')
            //->with('MediaResponse')
            ->first();

        //return $complaint->ResponseComplaint;
        if ($complaint) {
            foreach ($complaint->ResponseComplaint as $key => $value) {
                if ($key > 0) {
                    $value->user =  $value->User;
                    $value->MediaResponse;
                }
            }
            return response()->json([
                'res' => true,
                'message' => 'ok',
                'data' => $complaint,
            ], 200);
        } else {
            return response()->json([
                'res' => false,
                'message' => 'La denuncia no existe',
            ], 400);
        }
    }

    public function filterByCode(Request $request)
    {
        $complaint = Complaint::select(
            'complaints.id',
            'complaints.cod',
            'complaint_types.name as type_complaint',
            'users.name as informer',
            'users.last_name as last_name_informer',
            'uf.name as user_asigne',
            'uf.last_name as last_name_user_asigne',
            'us.name as name_user_inquest',
            'us.last_name as last_name_user_inquest',
            'state_complaints.name as state',
            'complaints.latitude',
            'complaints.longitude',
            'complaints.name_offender',
            'complaints.description',
            'complaints.created_at',
            'complaints.address',
            'complaints.id_user_asigne',
            'complaints.id_user_inquest'
        )
            ->leftjoin('users', 'complaints.id_user', '=', 'users.id')
            ->leftjoin('users as uf', 'complaints.id_user_asigne', '=', 'uf.id')
            ->leftjoin('users as us', 'complaints.id_user_inquest', '=', 'us.id')
            ->join('complaint_types', 'complaints.id_complaint_type', '=', 'complaint_types.id')
            ->join('state_complaints', 'complaints.id_state', '=', 'state_complaints.id')
            ->where('complaints.cod', $request["cod"])
            ->with('media')
            ->with('ResponseComplaint')
            //->with('MediaResponse')
            ->first();

        if ($complaint) {
            foreach ($complaint->ResponseComplaint as $key => $value) {
                if ($key > 0) {
                    $value->user =  $value->User;
                    $value->MediaResponse;
                }
            }
            return response()->json([
                'res' => true,
                'message' => 'ok',
                'data' => $complaint,
            ], 200);
        } else {
            return response()->json([
                'res' => false,
                'message' => 'La denuncia no existe',
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $complaint = Complaint::find($id);
        if ($complaint && $complaint->id_state + 1 <= 6) {
            $complaint->id_user_asigne   = $request->user_asigne;
            $complaint->id_state         = 2;
            $complaint->ComplaintType;
            $complaint->userAsigne;

            if ($complaint->update()) {
                $newResponse = new ResponseComplaint();
                $newResponse->description        = $request->description;
                $newResponse->id_complaint       = $complaint->id;
                $newResponse->id_state_complaint = $complaint->id_state;
                $newResponse->id_user            = $request->user_asigne;
                if ($newResponse->save() && $request->media_response) {
                    foreach ($request->media_response as $value) {

                        $newMediaResponse       = new MediaResponse;
                        $newMediaResponse->url  = $value["url"];
                        $newMediaResponse->type = $value["type"];
                        $newMediaResponse->id_response = $newResponse->id;
                        $newMediaResponse->save();
                    }
                }
                //return $complaint;
                $userEmail = User::find($request->user_asigne);
                //return $userEmail->email;
                Mail::to($userEmail->email)->send(new EmailMailable($complaint));
                if ($complaint->id_user) {
                    $state = StateComplaint::find($complaint->id_state);
                    $msg = [
                        "name" => $complaint->user->name . " " . $complaint->user->last_name,
                        "cod" => $complaint->cod,
                        "state" => $state->name
                    ];

                    Mail::to($complaint->user->email)->send(new ChangeStatusMailable($msg));
                }

                return response()->json([
                    'res' => true,
                    'message' => 'Registro exitoso'
                ]);
            }
        } else {
            return response()->json([
                "res" => false,
                'message' => 'Esta denuncia no existe o ya fue cerrada'
            ], 400);
        }
    }

    public function updateProccess(Request $request, $id)
    {
        $complaint = Complaint::where('id', $id)->first();


        if ($complaint) {
            $newResponse = new ResponseComplaint();
            $newResponse->description        = $request->description;
            $newResponse->id_complaint       = $complaint->id;
            $newResponse->id_state_complaint = $complaint->id_state;
            $newResponse->id_user            = Auth::user()->id;
            if ($newResponse->save() && $request->media_response) {
                foreach ($request->media_response as $value) {
                    $newMediaResponse       = new MediaResponse;
                    $newMediaResponse->url  = $value["url"];
                    $newMediaResponse->type = $value["type"];
                    $newMediaResponse->id_response = $newResponse->id;
                    $newMediaResponse->save();
                }
            }
            return response()->json([
                'res' => true,
                'message' => 'Registro exitoso'
            ]);
        } else {
            return response()->json([
                "res" => false,
                'message' => 'Esta denuncia no existe o ya fue cerrada'
            ], 400);
        }
    }

    public function asigneLawyer(Request $request, $id)
    {

        $complaint = Complaint::find($id);
        $complaint->id_user_inquest = $request->lawyer;
        $complaint->id_state        = 3;


        if ($complaint->update()) {
            $newResponse = new ResponseComplaint();
            $newResponse->description        = $request->description;
            $newResponse->id_complaint       = $complaint->id;
            $newResponse->id_user            = Auth::user()->id;
            $newResponse->id_state_complaint = $complaint->id_state;
            if ($newResponse->save()) {
                if ($complaint->id_user) {
                    $state = StateComplaint::find($complaint->id_state);
                    $msg = [
                        "name" => $complaint->user->name . " " . $complaint->user->last_name,
                        "cod" => $complaint->cod,
                        "state" => $state->name
                    ];

                    Mail::to($complaint->user->email)->send(new ChangeStatusMailable($msg));
                }
                $userEmail = User::find($complaint->id_user_inquest);
                $complaint->userAsigne = $userEmail;
                //return $complaint;
                Mail::to($userEmail->email)->send(new EmailMailable($complaint));
                return response()->json([
                    'res' => true,
                    'message' => 'Asignación exitosa'
                ], 200);
            }
        } else {
            return response()->json([
                'res' => false,
                'message' => 'Error al asignar el abogado'
            ], 400);
        }
    }

    public function asigneNotify(Request $request, $id)
    {

        $complaint = Complaint::find($id);
        $complaint->id_user_inquest = $request->user_asigne;
        $complaint->id_state        = 4;


        if ($complaint->update()) {
            $newResponse = new ResponseComplaint();
            $newResponse->description        = $request->description;
            $newResponse->id_complaint       = $complaint->id;
            $newResponse->id_user            = Auth::user()->id;
            $newResponse->id_state_complaint = $complaint->id_state;
            if ($newResponse->save()) {
                if ($complaint->id_user) {
                    $state = StateComplaint::find($complaint->id_state);
                    $msg = [
                        "name" => $complaint->user->name . " " . $complaint->user->last_name,
                        "cod" => $complaint->cod,
                        "state" => $state->name
                    ];

                    Mail::to($complaint->user->email)->send(new ChangeStatusMailable($msg));
                }
                $userEmail = User::find($complaint->id_user_inquest);
                $complaint->userAsigne = $userEmail;
                //return $complaint;
                Mail::to($userEmail->email)->send(new EmailMailable($complaint));
                return response()->json([
                    'res' => true,
                    'message' => 'Asignación exitosa'
                ], 200);
            }
        } else {
            return response()->json([
                'res' => false,
                'message' => 'Error al asignar el abogado'
            ], 400);
        }
    }


    public function closed(Request $request, $id)
    {
        $complaint = Complaint::find($id);
        $complaint->id_state        = 6;
        if ($complaint->update()) {
            $newResponse = new ResponseComplaint();
            $newResponse->description        = $request->description;
            $newResponse->id_complaint       = $complaint->id;
            $newResponse->id_user            = Auth::user()->id;
            $newResponse->id_state_complaint = $complaint->id_state;
            if ($newResponse->save() && $request->media_response) {
                foreach ($request->media_response as $value) {
                    $newMediaResponse       = new MediaResponse;
                    $newMediaResponse->url  = $value["url"];
                    $newMediaResponse->type = $value["type"];
                    $newMediaResponse->id_response = $newResponse->id;
                    $newMediaResponse->save();
                }
                if ($complaint->id_user) {
                    $state = StateComplaint::find($complaint->id_state);
                    $msg = [
                        "name" => $complaint->user->name . " " . $complaint->user->last_name,
                        "cod" => $complaint->cod,
                        "state" => $state->name
                    ];

                    Mail::to($complaint->user->email)->send(new ChangeStatusMailable($msg));
                }
                return response()->json([
                    'res' => true,
                    'message' => 'Denuncia cerrada con éxito'
                ], 200);
            }
        } else {
            return response()->json([
                'res' => false,
                'message' => 'Error al cerrar la denuncia'
            ], 400);
        }
    }

    public function cancel(Request $request, $id)
    {
        $complaint = Complaint::find($id);
        $complaint->id_state        = 7;
        if ($complaint->update()) {
            $newResponse = new ResponseComplaint();
            $newResponse->description        = $request->description;
            $newResponse->id_complaint       = $complaint->id;
            $newResponse->id_user            = Auth::user()->id;
            $newResponse->id_state_complaint = $complaint->id_state;
            if ($newResponse->save()) {
                return response()->json([
                    'res' => true,
                    'message' => 'Denuncia cancelada con éxito'
                ], 200);
            }
        } else {
            return response()->json([
                'res' => false,
                'message' => 'Error al cerrar la denuncia'
            ], 400);
        }
    }

    public function destroy($id)
    {
        //
    }

    public function notifyStateAdmin()
    {

        $complaints = Complaint::where('id_user_asigne', null)->get();
        foreach ($complaints as $complaint) {
            $userAdmin = User::where('id_rol', 1)->get();

            if (Carbon::parse($complaint->created_at) >= Carbon::now()->addHour(12)) {
                $state = StateComplaint::find($complaint->id_state);
                foreach ($userAdmin as $user) {
                    $msg = [
                        "name" => $user->name . " " . $user->last_name,
                        "cod" => $complaint->cod,
                        "state" => $state->name
                    ];

                    Mail::to($user->email)->send(new NotifyStateAdmin($msg));
                }
            }
        }
    }

    public function notifyStateFunt()
    {

        $complaints = Complaint::withCount('ResponseComplaint')->get();

        foreach ($complaints as $complaint) {

            if ($complaint->id_user_asigne && $complaint->response_complaint_count <= 2) {

                foreach ($complaint->ResponseComplaint as $value) {

                    /*  echo Carbon::parse($value->created_at) . "<br/>"; */
                    /*  echo Carbon::now()->addHour(24) . "<br/>"; */

                    if (Carbon::parse($value->created_at) <= Carbon::now()->addHour(24) && $value->id_state_complaint > 1) {
                        //return $value;
                        $user = User::find($complaint->id_user_asigne);
                        $state = StateComplaint::find($complaint->id_state);
                        $msg = [
                            "name" => $user->name . " " . $user->last_name,
                            "cod" => $complaint->cod,
                            "state" => $state->name
                        ];
                        Mail::to($user->email)->send(new NotifyStateFunt($msg));
                    }
                }
            }
        }
    }
}
