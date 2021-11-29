<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /* return $request["state"]; */
        $request["limit"] ? $limit = $request["limit"] : $limit = 10;

        $complaints = Complaint::select(
                'complaints.id',
                'complaint_types.name as type_complaint',
                'users.name as informer',
                'state_complaints.name as state',
                'complaints.latitude',
                'complaints.longitude',
                'complaints.name_offender',
                'complaints.description',
                'complaints.created_at'
            )
            ->join('users', 'complaints.id_user', '=', 'users.id')
            ->join('complaint_types', 'complaints.id_complaint_type', '=', 'complaint_types.id')
            ->join('state_complaints', 'complaints.id_state', '=', 'state_complaints.id')
            ->where('complaints.id', 'like', '%' . $request["search"] . '%')
            ->where('complaints.id_state', 'like', '%' . $request["state"] . '%')
            ->with('media')
            ->OrderBy('id', 'desc')->paginate($limit);

        return response()->json([
            'res' => true,
            'message' => 'ok',
            'data' => $complaints,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'name_offender' => 'required|string',
            'description' => 'required|string|max:255',
            'id_complaint_type' => 'required|integer'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $validator->errors();
        }
        if (ComplaintType::where('id', $request->id_complaint_type)->where('state', 1)->first()) {

            $newComplaint = new Complaint();
            $newComplaint->latitude             = $request->latitude;
            $newComplaint->longitude            = $request->longitude;
            $newComplaint->address              = $request->address;
            $newComplaint->name_offender        = $request->name_offender;
            $newComplaint->description          = $request->description;
            $newComplaint->id_complaint_type    = $request->id_complaint_type;
            $newComplaint->id_user              = Auth::user()->id;
            $newComplaint->id_state             = 1;

            if ($newComplaint->save()) {
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
                }

                return response()->json([
                    "res" => true,
                    "message" => 'Denuncia creada con exito'
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $complaint = Complaint::select(
            'complaints.id',
            'complaint_types.name as type_complaint',
            'users.name as informer',
            'state_complaints.name as state',
            'complaints.latitude',
            'complaints.longitude',
            'complaints.name_offender',
            'complaints.description',
            'complaints.created_at'
        )
            ->join('users', 'complaints.id_user', '=', 'users.id')
            ->join('complaint_types', 'complaints.id_complaint_type', '=', 'complaint_types.id')
            ->join('state_complaints', 'complaints.id_state', '=', 'state_complaints.id')
            ->where('complaints.id', $id)
            ->first();

        if ($complaint) {
            $complaint["media"] = $complaint->media;
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
