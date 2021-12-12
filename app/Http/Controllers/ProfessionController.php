<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    public function index()
    {
        $professions = Profession::where('state', 1)->where('id', '!=', 2)->get();

        return response()->json([
            'message' => 'ok',
            'data' => $professions
        ], 200);
    }
}
