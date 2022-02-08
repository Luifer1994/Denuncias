<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypePeople;

class TypePeopleController extends Controller
{
    public function index()
    {
        $type_people = TypePeople::all();
        return response()->json([
            'res' => true,
            'data' => $type_people,
        ]);
    }
}
