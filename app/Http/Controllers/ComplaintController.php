<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\Media;
use App\Models\MediaResponse;
use App\Models\ResponseComplaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
                ->where('complaints.cod', 'like', '%' . $request["search"] . '%')
                ->where('complaints.id_state', 'like', '%' . $request["state"] . '%')
                ->OrderBy('id', 'desc')->paginate($limit);
        } else {
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
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'name_offender' => 'required|string',
            'description' => 'required|string',
            'id_complaint_type' => 'required|integer'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 402);
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

        foreach ($complaint->ResponseComplaint as $key => $value) {
            if ($key > 0) {
                $value->user =  $value->User;
                $value->MediaResponse;
            }
        }
        //return $complaint->ResponseComplaint;
        if ($complaint) {
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

        foreach ($complaint->ResponseComplaint as $key => $value) {
            if ($key > 0) {
                $value->user =  $value->User;
                $value->MediaResponse;
            }
        }


        if ($complaint) {
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
        if ($complaint && $complaint->id_state + 1 <= 5) {
            $complaint->id_user_asigne   = $request->user_asigne;
            $complaint->id_state         = $complaint->id_state + 1;
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
        $complaint->id_state        = 5;
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

    public function destroy($id)
    {
        //
    }
}
