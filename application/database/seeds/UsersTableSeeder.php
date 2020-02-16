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
            'ic' => Hash::make('010801-10-0351'),
            'gender' => "男",
            'class_id' => 0,
        ],
        [
            'id' => 140204,
            'type' => 0,
            'cn_name' => '陈伟辰',
            'ic' => Hash::make('010527-14-0579'),
            'gender' => "男",
            'class_id' => 0,
        ],
        [
            'id' => 119999,
            'type' => 0,
            'cn_name' => '教务处',
            'ic' => Hash::make('780326009682'),
            'gender' => "男",
            'class_id' => 0
        ],
        [
            'id' => 111111,
            'type' => 1,
            'cn_name' => '初一',
            'ic' => Hash::make('111111'),
            'gender' => "男",
            'class_id' => 1
        ],
        [
            'id' => 222222,
            'type' => 1,
            'cn_name' => '初二',
            'ic' => Hash::make('222222'),
            'gender' => "男",
            'class_id' => 20
        ],
        [
            'id' => 333333,
            'type' => 1,
            'cn_name' => '初三',
            'ic' => Hash::make('333333'),
            'gender' => "男",
            'class_id' => 37
        ],
        [
            'id' => 444441,
            'type' => 1,
            'cn_name' => '高一理',
            'ic' => Hash::make('444441'),
            'gender' => "男",
            'class_id' => 80
        ],
        [
            'id' => 444442,
            'type' => 1,
            'cn_name' => '高一文',
            'ic' => Hash::make('444442'),
            'gender' => "男",
            'class_id' => 52
        ],
        [
            'id' => 555551,
            'type' => 1,
            'cn_name' => '高二理',
            'ic' => Hash::make('555552'),
            'gender' => "男",
            'class_id' => 86
        ],
        [
            'id' => 555552,
            'type' => 1,
            'cn_name' => '高二文',
            'ic' => Hash::make('555552'),
            'gender' => "男",
            'class_id' => 63
        ],
        [
            'id' => 666661,
            'type' => 1,
            'cn_name' => '高三理',
            'ic' => Hash::make('666662'),
            'gender' => "男",
            'class_id' => 93
        ],
        [
            'id' => 666662,
            'type' => 1,
            'cn_name' => '高三文',
            'ic' => Hash::make('666662'),
            'gender' => "男",
            'class_id' => 73
        ],
        ]);
    }
}