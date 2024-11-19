<?php

namespace App\Http\Controllers;

use App\Enums\PdoiApplicationStatusesEnum;
use App\Http\Resources\PdoiApplicationShortCollection;
use App\Http\Resources\PdoiApplicationShortResource;
use App\Models\MenuSection;
use App\Models\Page;
use App\Models\PdoiApplication;
use App\Models\PdoiResponseSubject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $lastApplications = 10;
        $mostAsked = 9;
        $applications = [];
        $appQ = PdoiApplication::lastApplicationsHomePage($lastApplications);
        if(sizeof($appQ)) {
            foreach ($appQ as $application) {
                $applications[] = [
                    'id' => $application->id,
                    'title' => __('custom.application_system_title',
                        [
                            'user' => ($application->names_publication ? $application->full_names : __('custom.users.legal_form.'.$application->applicant_type) ),
                            'subject' => $application->response_subject_id ? $application->subject_name : $application->not_registered_subject_name.'('.$application->not_registered_subject_eik.')',
                            'apply_date' => displayDate($application->created_at)
                        ]),
                ];
            }
        }
        $mostAskedSubjects = PdoiResponseSubject::mostAskedSubjects($mostAsked);
        $mainClass = 'container';
        $videoInstructionPage = Page::with(['translations'])
            ->where('system_name', '=', Page::VIDEO_INSTRUCTION_PAGE)
            ->first();

        $allApplicationsCnt = PdoiApplication::count();
        $registerdUsersCnt = User::External()->count();
        $pdoiSubjectsCnt = PdoiResponseSubject::CanApplyTo()->count();
        $applicationsLateAnswerCnt = PdoiApplication::where('status', '=', PdoiApplicationStatusesEnum::NO_REVIEW->value)
            ->count();
        return view('front.home', compact('applications', 'mostAskedSubjects', 'mainClass', 'videoInstructionPage', 'allApplicationsCnt', 'registerdUsersCnt', 'pdoiSubjectsCnt', 'applicationsLateAnswerCnt'));
    }

    public function section($slug) {
        $item = MenuSection::with(['translation', 'files']) //['translation', 'pages', 'pages.translation']
            ->where('slug', $slug)
            ->first();
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }
        $paginate = $filter['paginate'] ?? MenuSection::PAGINATE;
        $pages = $item->pages()->paginate($paginate);

        $this->setSeo([
            'seo_title' => $item->meta_title,
            'seo_keywords' => $item->meta_keyword,
            'seo_description' => $item->meta_description
        ]);

        $this->setTitles($item->name);
        return $this->view('front.page', compact('item', 'pages'));
    }

    public function page($sectionSlug, $slug) {
        $item = Page::with(['translation', 'section', 'section.translation', 'files'])->where('slug', $slug)->first();
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->setSeo([
            'seo_title' => $item->meta_title,
            'seo_keywords' => $item->meta_keyword,
            'seo_description' => $item->meta_description
        ]);
        $this->setTitles($item->name);

        return $this->view('front.page', compact('item'));
    }

    public function contact()
    {
        $item = Page::with(['translation', 'section', 'section.translation', 'files'])->where('slug', 'contact')->first();
        if( !$item ) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->setSeo([
            'seo_title' => $item->meta_title,
            'seo_keywords' => $item->meta_keyword,
            'seo_description' => $item->meta_description
        ]);
        $this->setTitles($item->name);

        $contacts = PdoiResponseSubject::getTree();

        return $this->view('front.page', compact('item', 'contacts'));
    }

    public function help(Request $request)
    {
        $appealPage = Page::with(['translations'])
            ->where('system_name', '=', Page::APPEAL_INFO_SYSTEM_PAGE)
            ->first();
        $videoInstructionPage = Page::with(['translations'])
            ->where('system_name', '=', Page::VIDEO_INSTRUCTION_PAGE)
            ->first();
        $guideManualPage = Page::with(['translations'])
            ->where('system_name', '=', Page::USER_MANUAL_PAGE)
            ->first();
        $faqPage = Page::with(['translations'])
            ->where('system_name', '=', Page::FAQ_PAGE)
            ->first();
        return $this->view('front.help.index', compact('appealPage', 'videoInstructionPage', 'guideManualPage', 'faqPage'));
    }

    public function helpPage(Request $request, string $slug): \Illuminate\View\View
    {
        $item = Page::with(['translations'])
            ->where('slug', '=', $slug ?? '')
            ->first();

        if( !$item ) {
            abort(\Illuminate\Http\Response::HTTP_NOT_FOUND);
        }

        $this->setSeo([
            'seo_title' => $item->meta_title,
            'seo_keywords' => $item->meta_keyword,
            'seo_description' => $item->meta_description
        ]);
        $this->setTitles($item->name);
        return $this->view('front.help.'.$item->system_name, compact('item'));
    }

    public function downloadHelpDoc($file): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filepath = public_path('help/'.$file);
        if(file_exists(public_path('help/'.$file))){
            return response()->download($filepath);
        } else{
            abort(404);
        }
    }

    public function sitemap($map = 'base'): \Illuminate\Http\Response
    {
        switch ($map) {
            case 'base':
                break;
        }

        $baseMap = DB::select('
            select
                   max(last_mod) as last_mod,
                   0 as id
            from (
                select
                    menu_section.id as id,
                    coalesce(menu_section.updated_at, menu_section.created_at) as last_mod
                from menu_section
                join menu_section_translations on menu_section_translations.menu_section_id = menu_section.id and menu_section_translations.locale = \'bg\'
                union
                    select
                    page.id as id,
                    coalesce(page.updated_at, page.created_at) as last_mod
                from page
                join page_translations on page_translations.page_id = page.id and page_translations.locale = \'bg\'
            ) as A
            ');

        $subMaps = DB::select('
            select
                pdoi_response_subject.id,
                --max(pdoi_response_subject_translations.subject_name) as name,
                max(case when pdoi_application.updated_at is null then pdoi_application.created_at else pdoi_application.updated_at end) as last_mod
            from pdoi_response_subject
            join pdoi_response_subject_translations on pdoi_response_subject_translations.pdoi_response_subject_id = pdoi_response_subject.id
            join pdoi_application on pdoi_application.response_subject_id = pdoi_response_subject.id
            where pdoi_response_subject_translations.locale = \'bg\'
            group by pdoi_response_subject.id
            ');

        return response()->view('front.sitemap.base', compact('baseMap', 'subMaps' ))
            ->header('Content-Type', 'text/xml');
    }

    public function subSitemap(int $subjectId = 0): \Illuminate\Http\Response
    {
        if( !$subjectId ) {
            $items = array(
                ['url' => route('home')],
                ['url' => route('help.index')],
                ['url' => route('application.list')],
            );
            //custom pages and sections
            $dbItems = DB::select('
                select
                    menu_section.id as id,
                    menu_section.slug,
                    \'\' as section_slug,
                    coalesce(menu_section.updated_at, menu_section.created_at) as last_mod,
                    \'section\' as type
                from menu_section
                join menu_section_translations on menu_section_translations.menu_section_id = menu_section.id and menu_section_translations.locale = \'bg\'
                union
                    select
                    page.id as id,
                    page.slug,
                    menu_section.slug as section_slug,
                    coalesce(page.updated_at, page.created_at) as last_mod,
                    \'page\' as type
                from page
                join page_translations on page_translations.page_id = page.id and page_translations.locale = \'bg\'
                join menu_section on menu_section.id = page.section_id
            ');
        } else {
            $items = [];
            $dbItems = DB::select('
            select
                pdoi_application.id,
                coalesce(pdoi_application.updated_at, pdoi_application.created_at) as last_mod,
                \'subject\' as type
            from pdoi_application
            where pdoi_application.response_subject_id = '.$subjectId.'
            ');
        }

        if(sizeof($dbItems)) {
            foreach ($dbItems as $item) {
                $url = match ($item->type) {
                    'subject' => route('application.show', ['id' => $item->id]),
                    'section' => route('section', ['slug' => $item->slug]),
                    default => route('page', ['slug' => $item->slug, 'section_slug' => $item->section_slug]),
                };
                $items[] = [
                    'url' => $url,
                    'last_mod' => Carbon::parse($item->last_mod)->toIso8601String()
                ];
            }
        }

        return response()->view('front.sitemap.items', [
            'items' => $items
        ])->header('Content-Type', 'text/xml');
    }
}
