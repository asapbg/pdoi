<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{

    /**
     * @params entityId required
     *         model required
     *         booleanType required (Ex: active, status)
     *         status required
     * Toggle Model's active database field
     * @param Request $request
     */
    public function toggleBoolean(Request $request)
    {
        if (
            !$request->filled('entityId')
            || !$request->filled('model')
            || !$request->filled('booleanType')
            || !$request->filled('status')
        ) {
            return back();
        }
        $entityId = $request->get('entityId');
        $booleanType = $request->get('booleanType');
        $model = "\App\Models\\".$request->get('model');
        if (!class_exists($model)) {
            $model = "\App\\".$request->get('model'); //Spatie\Permission\Models
            if (!class_exists($model)) {
                $model = "Spatie\Permission\Models\\".$request->get('model');
                if (!class_exists($model)) {
                    return back();
                }
            }
        }
        $status = $request->get('status');

        $entity = $model::find($entityId);
        $entity->$booleanType = $status;
        $entity->save();
    }

    /**
     * @params entityId required
     *         model required
     *         permission required
     *         status required
     * Toggle Model's permissions
     * @param Request $request
     */
    public function togglePermissions(Request $request)
    {
        if (
            !$request->filled('entityId')
            || !$request->filled('model')
            || !$request->filled('permission')
            || !$request->filled('status')
        ) {
            return back();
        }
        $entityId = request()->get('entityId');
        $permission = request()->get('permission');
        $model = "\App\Models\\".request()->get('model');
        if (!class_exists($model)) {
            $model = "\App\\".request()->get('model');
            if (!class_exists($model)) {
                return back();
            }
        }
        $status = request()->get('status');
        $entity = $model::find($entityId);

        if ($status == 0) {
            $entity->revokePermissionTo($permission);
        }
        else {
            $entity->givePermissionTo($permission);
        }
    }

    /**
     * Fix the primary key sequence for a given table
     *
     * @param $table
     */
    public static function fixSequence($table)
    {
        $primary_key_info = DB::select(DB::raw("SELECT a.attname AS name, format_type(a.atttypid, a.atttypmod) AS type FROM pg_class AS c JOIN pg_index AS i ON c.oid = i.indrelid AND i.indisprimary JOIN pg_attribute AS a ON c.oid = a.attrelid AND a.attnum = ANY(i.indkey) WHERE c.oid = '" . $table . "'::regclass"));
        $primary_key_type = 'number';
        $primary_key_name = 'id';
        if (array_key_exists('0', $primary_key_info)) {
            $primary_key_type = $primary_key_info[0]->type;
            $primary_key_name = $primary_key_info[0]->name;
        }
        if (strpos($primary_key_type, 'character') === false) {
            $max_id = DB::table($table)->max($primary_key_name);
            $next_id = $max_id + 1;
            $sequence_key_name = $table . '_' . $primary_key_name . '_seq';
            DB::statement("ALTER SEQUENCE $sequence_key_name RESTART WITH $next_id");
        }
    }
}
