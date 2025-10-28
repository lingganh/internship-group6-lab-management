<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeControler extends Controller
{
    public function eventsCalendar(){

        return view('pages.client.event-calendar');
    }

}
