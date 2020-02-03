<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\_Class;
use Validator;

class _ClassController extends Controller
{

    private $rules = [
        "cn_name"  => "required",
        "en_name"  => "required",
    ];


    public function create(Request $data)
    {
        $validator = Validator::make($data->all(), $this->rules);
        if ($validator->fails()) return $this->fail();
        _Class::create($data->all());
        return $this->ok();
    }

    public function get()
    {
        $user = json_decode(_Class::all()->toJson());
        return response((array)$user,200);
    }

    public function edit(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->rules);
        if ($validator->fails()) return $this->fail();
        _Class::where('id', $id)
            ->update([
                "cn_name" => $data->cn_name,
                "en_name" => $data->en_name,
            ]);
        return $this->ok();
    }

    public function delete(Request $data,$id)
    {
        _Class::where('id', $id)->delete();
        return $this->ok();
    }

}