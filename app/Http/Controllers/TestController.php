<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function login()
    {
        return view('forms.login');
    }

    public function register(Request $request)
    {
        return view('forms.register');
    }

    public function sukses()
    {
        return view('forms.sukses');
    }
}
