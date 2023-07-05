<?php

namespace App\Http\Controllers;

use App\Models\MenuSection;
use App\Models\Page;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('front.home');
    }

    public function section($slug) {
        $item = MenuSection::with(['translation']) //['translation', 'pages', 'pages.translation']
            ->where('slug', $slug)
            ->first();
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        $paginate = $filter['paginate'] ?? MenuSection::PAGINATE;
        $pages = $item->pages()->paginate($paginate);
        return view('front.page', compact('item', 'pages'));
    }

    public function page($sectionSlug, $slug) {
        $item = Page::with(['translation', 'section', 'section.translation'])->where('slug', $slug)->first();
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        return view('front.page', compact('item'));
    }
}
