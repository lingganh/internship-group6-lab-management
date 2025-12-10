<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        return view('pages.admin.users.index');
    }

    public function edit(int $id){
        return view('pages.admin.users.edit', ['id' => $id]);
    }

    public function create()
    {
        return view('pages.admin.users.create');
    }
}
