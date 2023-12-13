<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
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

    public function guide()
    {
        return view('admin.help.guide');
    }

    public function faq()
    {
        $page = Page::where('slug', '=', Page::ADMIN_PAGE)->first();
        return view('admin.help.faq', compact('page'));
    }
}
