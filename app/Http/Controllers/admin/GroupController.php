<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(){
        return view('pages.admin.groups.index');
    }

    public function create()
    {
        return view('pages.admin.groups.create');
    }

    public function edit($id){
        return view('pages.admin.groups.edit',['id'=>$id]);
    }
}
