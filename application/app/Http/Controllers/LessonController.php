<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lesson;
use App\Period;
use App\Year;
use Validator;
use DB;

class LessonController extends Controller
{

    private $rules = [
        "name"  => "required",
        "location"  => "",
        "subject" => "",
        "description" => "",
        "limit" => "required|integer",
        "period" => "required|array",
        "gender" => "required|in:无,男,女",
        "stream" => "required|in:无,文,理",
        "year" => "required|array",
        "period.*" => "required|integer",
        "year.*" => "required|integer",
    ];

    public function recount()
    {
        $lesson_users = DB::table('lesson_user')->get();
        foreach ($lesson_users as $lesson_user){
            $lesson = Lesson::find($lesson_user->lesson_id);
            $lesson->current++;
            $lesson->save();
        }
        // User::flushCache();
        Lesson::flushCache();
        return $this->ok();
    }


    public function create(Request $data)
    {
        $validator = Validator::make($data->all(), $this->rules);
        if ($validator->fails()) return $this->fail();
        $id = Lesson::create($data->all())->id;
        foreach ($data->period as $period){
            DB::table('periods')->insert(
                ['lesson_id' => $id, 'period' => $period]
            );
        }
        foreach ($data->year as $year){
            DB::table('years')->insert(
                ['lesson_id' => $id, 'year' => $year]
            );
        }
        // User::flushCache();
        Lesson::flushCache();
        return $this->ok();
    }

    public function get()
    {
        $lessons = Lesson::all();
        $data = [];
        foreach($lessons as $lesson){
            $single_data = json_decode($lesson->toJson());
            unset($single_data->periods);
            unset($single_data->years);
            $periods = $lesson->periods;
            $years = $lesson->years;
            $single_data->period = [];
            $single_data->year = [];

            foreach ($years as $year){
                array_push($single_data->year,$year->year);
            }

            foreach ($periods as $period){
                array_push($single_data->period,$period->period);
            }

            array_push($data,$single_data);
        }
        return response($data,200);
    }

    public function get_single($id)
    {
        $lesson = Lesson::where('id',$id)->with('users','users_force')->first();
        $single_data = [];
        foreach ($lesson->users as $user){
            $temp = json_decode($user->toJson());
            unset($temp->pivot);
            $temp->class = $user->classes->cn_name;
            array_push($single_data,$temp);
        }
        foreach ($lesson->users_force as $user_force){
            $temp = json_decode($user_force->toJson());
            unset($temp->pivot);
            $temp->class = $user_force->classes->cn_name;
            array_push($single_data,$temp);
        }
        return response($single_data,200);
    }

    public function get_all()
    {
        $lessons = Lesson::with('users','users_force')->get();
        $data = [];
        foreach($lessons as $lesson){
            $single_data = json_decode($lesson->toJson());
            unset($single_data->periods);
            unset($single_data->years);
            $periods = $lesson->periods;
            $years = $lesson->years;
            $single_data->period = [];
            $single_data->year = [];

            foreach ($years as $year){
                array_push($single_data->year,$year->year);
            }

            foreach ($periods as $period){
                array_push($single_data->period,$period->period);
            }
            
            unset($single_data->users);
            unset($single_data->users_force);
            $single_data->user = [];
            foreach ($lesson->users as $user){
                $temp = json_decode($user->toJson());
                unset($temp->pivot);
                $temp->class = $user->classes->cn_name;
                array_push($single_data->user,$temp);
            }
            foreach ($lesson->users_force as $user_force){
                $temp = json_decode($user_force->toJson());
                unset($temp->pivot);
                $temp->class = $user_force->classes->cn_name;
                array_push($single_data->user,$temp);
            }
            array_push($data,$single_data);
        }
        return response($data,200);
    }

    public function edit(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->rules);
        if ($validator->fails()) return $this->fail();
        Lesson::where('id', $id)
            ->update([
                'name' => $data->name,
                'location' => $data->location,
                'subject' => $data->subject,
                'limit' => $data->limit,
            ]);
        Year::where('lesson_id',$id)->delete();
        Period::where('lesson_id',$id)->delete();
        foreach ($data->period as $period){
            DB::table('periods')->insert(
                ['lesson_id' => $id, 'period' => $period]
            );
        }
        foreach ($data->year as $year){
            DB::table('years')->insert(
                ['lesson_id' => $id, 'year' => $year]
            );
        }
        // User::flushCache();
        Lesson::flushCache();
        return $this->ok();
    }

    public function delete(Request $data,$id)
    {
        Lesson::where('id', $id)->delete();
        // User::flushCache();
        Lesson::flushCache();
        return $this->ok();
    }

}