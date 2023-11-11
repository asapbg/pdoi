<?php

namespace Database\Seeders;

use App\Models\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileSeeder extends Seeder
{
    private int $fileAtOnes = 50;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('files')->truncate();
        $maxFileId = DB::connection('old')->select('select max(files.id) from files');
        if( (int)$maxFileId[0]->max ) {
            $existingFileIds = File::get()->pluck('id');

            $maxFileId = (int)$maxFileId[0]->max;
            $minFileId = 0;

            DB::beginTransaction();
            try {
                while ($minFileId < $maxFileId) {
                    //Log::error($minFileId.' - '.($minFileId + $this->fileAtOnes));

                    $oldFiles = DB::connection('old')->select("
                        select
                            files.id,
                            files.id_object,
                            files.code_object,
                            replace(files.filename, ' ', '_') as filename,
                            files.content_type,
                            files.content,
                            files.description,
                            concat('upload/'
                                        || (case when files.code_object = 14 then pdoi_event.application_id else files.id_object end)
                                        || '/'
                                        || replace(files.filename, ' ', '_')) as path, -- upload/7517/zayavlenie_ZDOI_14.07.2023.pdf
                            files_text.content_text as file_text,
                            files.visible_on_site,
                            adm_users.user_id as user_reg,
                            files.user_last_mod,
                            files.date_last_mod as updated_at,
                            files.date_reg as created_at
                        from files
                        left join adm_users on adm_users.user_id = files.user_reg --  to fix missing users we set null as system if not exist
                        left join files_text on files_text.id_files = files.id
                        left join pdoi_event on pdoi_event.id = files.id_object and files.code_object = 14 -- evnet file, we need it to get application id for file directory structure
                        where
                            (
                                (files.id_object not in (1685,1686) and files.code_object = 14) -- events with not existing applications
                                or
                                (files.id_object not in (6554,6743,7016,7021,7033,6647,7020,7087,7088,7086, 328) and files.code_object = 13)  -- this applications are skipped by ApplicationSeeder because they do not have user
                            )
                            and files.id > ".$minFileId."
                            and files.id <= ".($minFileId + $this->fileAtOnes)."
                    ");

                    if( sizeof($oldFiles) ) {
                        foreach ($oldFiles as $file) {
                            if( !sizeof($existingFileIds) || !in_array($file->id, $existingFileIds) ) { //skip if already inserted
                                $appToArray = get_object_vars($file);

                                $content = $appToArray['content'];
                                unset($appToArray['content']);
                                //remove non-printed characters from string like LRM
                                $appToArray['filename'] = preg_replace('/(\x{200e}|\x{200f})/u', '', $appToArray['filename']);
                                $appToArray['path'] = preg_replace('/(\x{200e}|\x{200f})/u', '', $appToArray['path']);
                                $newItem = new File();
                                $newItem->fill($appToArray);
                                $newItem->save();
                                if( !empty($content) ) {
                                    Storage::disk('local')->put($appToArray['path'], $content);
                                }
                            }
                        }
                    }

                    $minFileId += $this->fileAtOnes;
                }

                DB::commit();
            } catch (\Exception $e){
                DB::rollBack();
                Log::error('Migration old files: '. $e);
            }
        }

        $tableToResetSeq = ['files'];
        foreach ($tableToResetSeq as $table) {
            \Illuminate\Support\Facades\DB::statement(
                "do $$
                        declare newId int;
                        begin
                            select (coalesce(max(id),0) +1)  from ".$table." into newId;
                            execute 'alter SEQUENCE ".$table."_id_seq RESTART with '|| newId;
                        end;
                        $$ language plpgsql"
            );
        }
    }
}
