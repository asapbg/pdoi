<?php

namespace App\Http\Controllers;

use App\Models\MenuSection;
use App\Models\Page;
use Illuminate\Http\Request;
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

    public function help(Request $request)
    {
        $appealPage = Page::with(['translations'])
            ->where('system_name', '=', Page::APPEAL_INFO_PAGE)
            ->first();
        return $this->view('front.help.index', compact('appealPage'));
    }

    public function helpPage(Request $request, string $slug): \Illuminate\View\View
    {
        $item = Page::with(['translations'])
            ->where('slug', '=', $slug ?? '')
            ->first();

        if( !$item ) {
            abort(\Illuminate\Http\Response::HTTP_NOT_FOUND);
        }
        return $this->view('front.help.'.$item->system_name, compact('item'));
    }

    public function downloadHelpDoc($file): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filepath = public_path('help/'.$file);
        return response()->download($filepath);
    }
}
