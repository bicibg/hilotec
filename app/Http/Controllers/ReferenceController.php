<?php

namespace App\Http\Controllers;

use App\Models\ReferenceCategory;

class ReferenceController extends Controller
{
    public function index()
    {
        $categories = ReferenceCategory::ordered()
            ->with(['references' => fn ($q) => $q->published()->ordered()])
            ->get();

        return view('pages.references', compact('categories'));
    }
}
