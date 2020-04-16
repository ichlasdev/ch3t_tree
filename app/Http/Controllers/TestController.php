<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function login()
    {
        return view('forms.login');
    }

    public function register()
    {
        return view('forms.register');
    }
}
