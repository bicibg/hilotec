<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::published()->ordered()->get();

        return view('pages.services.index', compact('services'));
    }

    public function show(string $slug)
    {
        $service = Service::published()->where('slug', $slug)->firstOrFail();
        $services = Service::published()->ordered()->get();

        return view('pages.services.show', compact('service', 'services'));
    }
}
