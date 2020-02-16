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
            'en_name' => 'Tan Kel Zin',
            'ic' => Hash::make('010801-10-0351'),
            'gender' => "男",
            'class_id' => 0,
        ],
        [
            'id' => 140204,
            'type' => 0,
            'cn_name' => '陈伟辰',
            'en_name' => 'Tan Weiu Cheng',
            'ic' => Hash::make('010527-14-0579'),
            'gender' => "男",
            'class_id' => 0,
        ],
        [
            'id' => 119999,
            'type' => 0,
            'cn_name' => '教务处',
            'en_name' => 'jiaowuchu',
            'ic' => Hash::make('780326-00-9682'),
            'gender' => "男",
            'class_id' => 0
        ],
        // [
        //     'id' => 201111,
        //     'type' => 1,
        //     'cn_name' => '初一',
        //     'ic' => Hash::make('201111'),
        //     'gender' => "男",
        //     'class_id' => 1
        // ],
        // [
        //     'id' => 192222,
        //     'type' => 1,
        //     'cn_name' => '初二',
        //     'ic' => Hash::make('192222'),
        //     'gender' => "男",
        //     'class_id' => 20
        // ],
        // [
        //     'id' => 183333,
        //     'type' => 1,
        //     'cn_name' => '初三',
        //     'ic' => Hash::make('183333'),
        //     'gender' => "男",
        //     'class_id' => 37
        // ],
        // [
        //     'id' => 174441,
        //     'type' => 1,
        //     'cn_name' => '高一理',
        //     'ic' => Hash::make('174441'),
        //     'gender' => "男",
        //     'class_id' => 80
        // ],
        // [
        //     'id' => 174442,
        //     'type' => 1,
        //     'cn_name' => '高一文',
        //     'ic' => Hash::make('174442'),
        //     'gender' => "男",
        //     'class_id' => 52
        // ],
        // [
        //     'id' => 165551,
        //     'type' => 1,
        //     'cn_name' => '高二理',
        //     'ic' => Hash::make('165552'),
        //     'gender' => "男",
        //     'class_id' => 86
        // ],
        // [
        //     'id' => 165552,
        //     'type' => 1,
        //     'cn_name' => '高二文',
        //     'ic' => Hash::make('165552'),
        //     'gender' => "男",
        //     'class_id' => 63
        // ],
        // [
        //     'id' => 156661,
        //     'type' => 1,
        //     'cn_name' => '高三理',
        //     'ic' => Hash::make('156662'),
        //     'gender' => "男",
        //     'class_id' => 93
        // ],
        // [
        //     'id' => 156662,
        //     'type' => 1,
        //     'cn_name' => '高三文',
        //     'ic' => Hash::make('156662'),
        //     'gender' => "男",
        //     'class_id' => 73
        // ],
        ]);
    }
}