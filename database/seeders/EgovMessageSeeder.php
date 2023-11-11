<?php

namespace Database\Seeders;

use App\Models\Egov\EgovMessage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EgovMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('egov_message')->truncate();

        $oldItems = DB::connection('old')->select('
            select
                egov_messages.id,
                egov_messages.msg_guid,
                egov_messages.sender_guid,
                egov_messages.sender_name,
                egov_messages.recipient_guid,
                egov_messages.recipient_name,
                egov_messages.msg_type,
                egov_messages.msg_dat as created_at,
                egov_messages.msg_status,
                egov_messages.msg_status_dat,
                egov_messages.msg_reg_dat,
                egov_messages.msg_comment,
                egov_messages.msg_urgent,
                egov_messages.msg_inout,
                egov_messages.msg_version,
                egov_messages.msg_rn,
                egov_messages.msg_rn_dat,
                egov_messages.doc_guid,
                egov_messages.doc_dat,
                egov_messages.doc_rn,
                egov_messages.doc_uri_reg,
                egov_messages.doc_uri_batch,
                egov_messages.doc_vid,
                egov_messages.doc_subject,
                egov_messages.doc_srok as doc_deadline,
                egov_messages.doc_nasochen as doc_to,
                egov_messages.parent_guid,
                egov_messages.parent_rn,
                egov_messages.parent_dat as parent_date,
                egov_messages.parent_uri_reg,
                egov_messages.parent_uri_batch,
                egov_messages.doc_comment,
                egov_messages.comm_status,
                egov_messages.comm_errror as comm_error,
                egov_messages.msg_xml,
                egov_messages.sender_eik,
                egov_messages.recipient_eik,
                egov_messages.user_created,
                egov_messages.prichina as reason,
                egov_messages.pored as ord,
                egov_messages.has_malware,
                egov_messages.source,
                egov_messages.reply_ident,
                egov_messages.recipient_type
            from egov_messages
            ');

        if( sizeof($oldItems) ) {
            DB::beginTransaction();
            try {
                foreach ($oldItems as $item) {
                    $appToArray = get_object_vars($item);
                    $newItem = new EgovMessage();
                    $newItem->fill($appToArray);
                    $newItem->save();
                }

                DB::commit();
            } catch (\Exception $e){
                Log::error('Migration old egov message: '. $e);
                DB::rollBack();
            }
        }

        $tableToResetSeq = ['egov_message'];
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
