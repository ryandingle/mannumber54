<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'title' 		=> 'Create',
            'prefix' 		=> 'create',
            'description' 	=> 'Create Permission',
            'created_by' 	=> '1',
            'created_at' 	=> date('Y-m-d H:i:s'),
        ]);

        DB::table('permissions')->insert([
            'title'         => 'Read',
            'prefix'        => 'read',
            'description'   => 'Read Permission',
            'created_by'    => '1',
            'created_at' 	=> date('Y-m-d H:i:s'),
        ]);

        DB::table('permissions')->insert([
            'title'         => 'Update',
            'prefix'        => 'update',
            'description'   => 'Update Permission',
            'created_by'    => '2',
            'created_at' 	=> date('Y-m-d H:i:s'),
        ]);

        DB::table('permissions')->insert([
            'title'         => 'Delete',
            'prefix'        => 'delete',
            'description'   => 'Delete Permission',
            'created_by'    => '2',
            'created_at' 	=> date('Y-m-d H:i:s'),
        ]);
    }
}
