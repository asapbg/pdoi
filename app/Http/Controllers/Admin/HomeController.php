<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{

    /**
     * Show the Admin's dashboard.
     *
     * @return View
     */
    public function index()
    {
        return view('admin.home');
    }
}
