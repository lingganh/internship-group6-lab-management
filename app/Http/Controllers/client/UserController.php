<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function infoUser(){
        return view('pages.client.user.info-user');
    }

    public function changePassword(){
        return view('pages.client.user.change-password');

    }

    public function twoFactor()
    {
        return view('pages.auth.two-factor');
    }
}
