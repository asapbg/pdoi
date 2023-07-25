<?php

namespace App\Http\Controllers\Admin;

use App\Exports\NomenclatureExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    use ValidatesRequests;
    /**
     * @param $fields  //example $item->getFillable();
     * @param $item   //model;
     * @param $validated //request validated
     */
    protected function storeTranslateOrNew($fields, $item, $validated)
    {
        foreach (config('available_languages') as $locale) {
            foreach ($fields as $field) {
                $fieldName = $field."_".$locale['code'];
//                dd($fields, $field, $fieldName, $validated);
                if(array_key_exists($fieldName, $validated)) {
                    $item->translateOrNew($locale['code'])->{$field} = $validated[$fieldName];
                }
            }
        }

        $item->save();
    }


    /**
     * Retiurn only fillable fields from validated request data
     * @param $validated
     * @param $item
     * @return mixed
     */
    protected function getFillableValidated($validated, $item)
    {
        $modelFillable = $item->getFillable();
        $validatedFillable = $validated;
        foreach ($validatedFillable as $field => $value) {
            if( !in_array($field, $modelFillable) ) {
                unset($validatedFillable[$field]);
            }
        }
        return $validatedFillable;
    }


    protected function getData($q,  $exportType, $extraData = []): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        $items = $q->get();
        try {
            return Excel::download(new NomenclatureExport($items, $exportType, $this->title_plural, $extraData), 'nomenclature_'.$exportType.'_'.Carbon::now()->format('Y_m_d_H_i_s').'.xlsx');
        } catch (\Exception $e) {
            logError('Export statistic (type '.$exportType.')', $e->getMessage());
            return redirect()->back()->with('warning', "Възникна грешка при експортирането, моля опитайте отново");
        }
    }

}
