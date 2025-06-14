<?php

namespace App\Http\Controllers;

use App\Models\Allergy;
use Illuminate\Http\Request;

class AllergyController extends Controller
{
    public function index()
    {
        return Allergy::all();
    }
}
