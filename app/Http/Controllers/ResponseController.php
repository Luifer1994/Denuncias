<?php

namespace App\Http\Controllers;

use App\Models\MediaResponse;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function getMedia($id)
    {
        $media = MediaResponse::where('id_response', $id)->get();
        return response()->json([
            "res" => true,
            "data" => $media
        ], 200);
    }
}
