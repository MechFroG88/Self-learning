<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
        [
            'id' => 140145,
            'type' => 0,
            'cn_name' => '陈科锦',
            'ic' => Hash::make('010801100351'),
            'class_id' => 0,
        ],
        [
            'id' => 140204,
            'type' => 0,
            'cn_name' => '陈伟辰',
            'ic' => Hash::make('010527140579'),
            'class_id' => 0,
        ],
        [
            'id' => 119999,
            'type' => 0,
            'cn_name' => '教务处',
            'ic' => Hash::make('780326009682'),
            'class_id' => 0
        ]
        ]);
    }
}