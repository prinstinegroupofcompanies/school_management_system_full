<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index()
    {
        return view('transport.index');
    }

    public function routes()
    {
        return view('transport.routes');
    }

    public function vehicles()
    {
        return view('transport.vehicles');
    }

    public function schedule()
    {
        return view('transport.schedule');
    }
}