<?php

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DspUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function run()
    {
        $data = json_decode(Storage::disk('dsp_data')->get('users_dsp.json'), true);

        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();

        $this->call('AdminUserSeeder');

        foreach ($data as $user) {
            DB::table('users')->insert([
                'username' => $user['username'],
                'name' => $user['username'],
                'password' => Hash::make($user['password']),
                'checkpoint' => $user['checkpoint-id'],
                'generic_password' => $user['password'],
                'import_order_number' => $user['id']
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Model::reguard();
    }
}
