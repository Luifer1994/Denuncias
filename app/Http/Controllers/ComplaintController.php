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
            'users.name as user_asigne',
            'state_complaints.name as state',
            'complaints.latitude',
            'complaints.longitude',
            'complaints.name_offender',
            'complaints.description',
            'complaints.created_at',
            'complaints.address',
            'complaints.id_user_asigne'
        )
            ->leftjoin('users', 'complaints.id_user', '=', 'users.id')
            ->join('complaint_types', 'complaints.id_complaint_type', '=', 'complaint_types.id')
            ->join('state_complaints', 'complaints.id_state', '=', 'state_complaints.id')
            ->where('complaints.id', $id)
            ->with('media')
            ->with('ResponseComplaint')
            //->with('MediaResponse')
            ->first();

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
            'complaints.address',
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
            ->where('complaints.cod', $request["cod"])
            ->with('media')
            ->with('ResponseComplaint')
            ->first();


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
        $rules = [
            'description' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 402);
        }
        $complaint = Complaint::find($id);
        if ($complaint && $complaint->id_state + 1 <= 3) {
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

    public function destroy($id)
    {
        //
    }
}
