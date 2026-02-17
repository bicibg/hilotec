<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\TeamMember;

class AboutController extends Controller
{
    public function index()
    {
        $page = Page::where('slug', 'ueber-uns')->firstOrFail();
        $teamMembers = TeamMember::published()->ordered()->get();

        return view('pages.about', compact('page', 'teamMembers'));
    }
}
