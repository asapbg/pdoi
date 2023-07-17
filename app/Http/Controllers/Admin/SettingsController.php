<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingsStoreRequest;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingsController extends Controller
{
    public function index($section = 'general'): \Illuminate\View\View
    {
        $settings = Settings::Editable()->where('section', '=', $section)->get();
        $sections = Settings::Editable()->get()->unique('section')->pluck('section')->toArray();
        return $this->view('admin.settings.index', compact('settings', 'section', 'sections'));
    }

    public function store(SettingsStoreRequest $request): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $section = $validated['section'];
        unset($validated['section']);

        foreach ($validated as $name => $value) {
            $setting = Settings::Editable()->where('name', '=', $name)->first();
            if( $request->user()->cannot('update', $setting) ) {
                dd('ok');
                abort(Response::HTTP_FORBIDDEN);
            }
            $setting->value = $value;
            $setting->update();
        }
        return redirect(route('admin.settings', ['section' => $section]))->with('success', __('messages.updated_successfully_n'));
    }
}
