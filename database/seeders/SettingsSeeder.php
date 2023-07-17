<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            [
                'name' => 'session_time_limit',
                'value' => 10,
                'section' => 'general',
                'editable' => 1,
                'type' => 'number'
            ]
        );
        foreach ($data as $row) {
            $record = \App\Models\Settings::where('name', $row['name'])->first();

            if ($record) {
                $this->command->line("Setting ".$row['name']." already exists in db");
                continue;
            }

            \App\Models\Settings::create($row);

            $this->command->info("Setting with name ".$row['name']." created successfully");
        }
    }
}
