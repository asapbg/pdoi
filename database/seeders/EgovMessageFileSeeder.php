<?php

namespace Database\Seeders;

use App\Models\Egov\EgovMessageFile;
use App\Models\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EgovMessageFileSeeder extends Seeder
{
    private int $fileAtOnes = 50;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('egov_message_file')->truncate();

        $maxFileId = DB::connection('old')->select('select max(egov_messages_files.id) from egov_messages_files');
        if ((int)$maxFileId[0]->max) {
            $maxFileId = (int)$maxFileId[0]->max;
            $minFileId = 0;

            DB::beginTransaction();
            try {
                while ($minFileId < $maxFileId) {
                    Log::error($minFileId . ' - ' . ($minFileId + $this->fileAtOnes));

                    $oldItems = DB::connection('old')->select('
                        select
                            egov_messages_files.id,
                            egov_messages_files.id_message,
                            egov_messages_files.filename,
                            egov_messages_files.mime,
                            egov_messages_files.blobcontent as blob_content,
                            egov_messages_files.result,
                            egov_messages_files.has_malware,
                            egov_messages_files.status,
                            egov_messages_files.pored as ord
                        from egov_messages_files
                        where egov_messages_files.id > '.$minFileId.'
                            and egov_messages_files.id <= '.($minFileId + $this->fileAtOnes).'
                    ');

                    if (sizeof($oldItems)) {
                        foreach ($oldItems as $item) {
                            if(is_null($item->id_message)) {
                                dd($item);
                            }
                            $itemToArray = get_object_vars($item);
                            $content = $itemToArray['blob_content'];
                            $name = preg_replace('/(\x{200e}|\x{200f})/u', '', $itemToArray['filename']);
                            $name = preg_replace('/[[:^print:]]/', '', $name);
                            $name = str_replace(' ', '', $name);
                            $path = 'upload/message_files/'.(round(microtime(true))).'_'.$name;
                            unset($itemToArray['blob_content']);

                            $newFile = new File();
                            $newFile->id_object = $itemToArray['id_message'];
                            $newFile->code_object = File::CODE_OBJ_MESSAGE;
                            $newFile->filename = $name;
                            $newFile->content_type = $itemToArray['mime'];
                            $newFile->path = $path;
                            $newFile->visible_on_site = 0;
                            $newFile->user_reg = null;
                            $newFile->user_last_mod = null;
                            $newFile->save();

                            if( !empty($content) ) {
                                Storage::disk('local')->put($path, $content);
                            }

                            if($newFile->id) {
                                //remove non-printed characters from string like LRM
                                $itemToArray['filename'] = $name;
                                $itemToArray['path'] = $path;
                                $newMessageFile = new EgovMessageFile();
                                $newMessageFile->fill($itemToArray);
                                $newMessageFile->save();
                            }
                        }
                    }

                    $minFileId += $this->fileAtOnes;
                }

                DB::commit();
            } catch (\Exception $e){
                DB::rollBack();
                Log::error('Migration old egov message files: '. $e);
            }
        }

        $tableToResetSeq = ['egov_message_file'];
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
