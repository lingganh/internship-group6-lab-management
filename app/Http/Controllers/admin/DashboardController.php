<?php

namespace App\Http\Controllers\admin;

use App\Models\LabEvent;
use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Query;
 
class DashboardController extends Controller
{
    
    public function render() {
    
    
   
    return view('pages.admin.dashboard');
}
} 
