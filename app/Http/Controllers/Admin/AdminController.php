<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

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
        $fields = $item->getFillable();
        foreach (config('available_languages') as $locale) {
            foreach ($fields as $v => $field) {
                $field = $v."_".$locale['code'];
                if(array_key_exists($field, $validated)) {
                    $item->translateOrNew($locale['code'])->{$v} = $validated[$field];
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

}
