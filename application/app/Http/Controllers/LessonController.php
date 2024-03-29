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

    private $submit_rules = [
        "students" => "required|array",
        "students.*" => "integer"
    ];

    public function recount()
    {
        $lesson_users = DB::table('lesson_user')->get();
        foreach ($lesson_users as $lesson_user){
            DB::table('lessons')->where('id',$lesson_user->lesson_id)->increment('current');
        }
        // User::flushCache();
        Lesson::flushCache();
        return $this->ok();
    }


    public function create(Request $data)
    {
        $validator = Validator::make($data->all(), $this->rules);
        if ($validator->fails()) return $this->fail();
        $temp = $data->all();
        var_dump($temp);
        if (!isset($temp['description'])) $temp['description'] = "";
        $id = Lesson::create($temp)->id;
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
        if (!isset($data->description)) $data->description = "";
        Lesson::where('id', $id)
            ->update([
                'name' => $data->name,
                'location' => $data->location,
                'subject' => $data->subject,
                'limit' => $data->limit,
                'description' => $data->description
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
        DB::table('periods')->where('lesson_id',$id)->delete();
        DB::table('years')->where('lesson_id',$id)->delete();
        DB::table('lesson_user')->where('lesson_id',$id)->delete();
        DB::table('lesson_user_force')->where('lesson_id',$id)->delete();
        // User::flushCache();
        Lesson::flushCache();
        return $this->ok();
    }

    public function add(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->submit_rules);
        if ($validator->fails()) return $this->fail();
        $students = $data->students;
        $periods = DB::table('periods')->where('lesson_id',$id)->get();
        $add_period = [];
        foreach ($periods as $period){
            array_push($add_period,$period->period);
        }
        foreach($students as $student){
            $lesson_user = DB::table('lesson_user')
                                ->where('user_id',$student)->select('lesson_id')->get();
            foreach ($lesson_user as $single){
                $periods = DB::table('periods')->where('lesson_id',$single->lesson_id)->get();
                $used_period = [];
                foreach ($periods as $period){
                    array_push($used_period,$period->period);
                }
                if (array_intersect($add_period, $used_period)){
                    if(DB::table('lesson_user')->where([
                        'user_id' => $student,
                        'lesson_id' => $single->lesson_id
                    ])->count()){
                        DB::table('lesson_user')->where([
                            'user_id' => $student,
                            'lesson_id' => $single->lesson_id
                        ])->delete();
                        DB::table('lessons')->where('id',$single->lesson_id)->decrement('current');
                    }
                }
            }
            if(DB::table('lesson_user')->where(
                ['user_id' => $student, 'lesson_id' => $id]
            )->count() == 0){
                DB::table('lesson_user')->insert(
                    ['user_id' => $student, 'lesson_id' => $id]
                );
                DB::table('lessons')->where('id',$id)->increment('current');
            }
        }
        return $this->ok();
    }

    public function add_force(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->submit_rules);
        if ($validator->fails()) return $this->fail();
        $students = $data->students;
        $periods = DB::table('periods')->where('lesson_id',$id)->get();
        $add_period = [];
        foreach ($periods as $period){
            array_push($add_period,$period->period);
        }
        foreach($students as $student){
            $lesson_user = DB::table('lesson_user')
                                ->where('user_id',$student)->select('lesson_id')->get();
            $lesson_user_force = DB::table('lesson_user_force')
                                    ->where('user_id',$student)->select('lesson_id')->get();
            foreach ($lesson_user as $single){
                $periods = DB::table('periods')->where('lesson_id',$single->lesson_id)->get();
                $used_period = [];
                foreach ($periods as $period){
                    array_push($used_period,$period->period);
                }
                if (array_intersect($add_period, $used_period)){
                    if(DB::table('lesson_user')->where([
                        'user_id' => $student,
                        'lesson_id' => $single->lesson_id
                    ])->count()){
                        DB::table('lesson_user')->where([
                            'user_id' => $student,
                            'lesson_id' => $single->lesson_id
                        ])->delete();
                        DB::table('lessons')->where('id',$single->lesson_id)->decrement('current');
                    }
                }
            }
            foreach ($lesson_user_force as $single){
                $periods = DB::table('periods')->where('lesson_id',$single->lesson_id)->get();
                $used_period = [];
                foreach ($periods as $period){
                    array_push($used_period,$period->period);
                }
                if (array_intersect($add_period, $used_period)){
                    if(DB::table('lesson_user_force')->where([
                        'user_id' => $student,
                        'lesson_id' => $single->lesson_id
                    ])->count()){
                        DB::table('lesson_user_force')->where([
                            'user_id' => $student,
                            'lesson_id' => $single->lesson_id
                        ])->delete();
                    }
                }
            }
            if(DB::table('lesson_user_force')->where(
                ['user_id' => $student, 'lesson_id' => $id]
            )->count() == 0){
                DB::table('lesson_user_force')->insert(
                    ['user_id' => $student, 'lesson_id' => $id]
                );
            }
        }
        return $this->ok();
    }

    public function remove_force(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->submit_rules);
        if ($validator->fails()) return $this->fail();
        $students = $data->students;
        foreach($students as $student){
            if(DB::table('lesson_user_force')->where([
                'user_id' => $student,
                'lesson_id' => $id
            ])->count()){
                DB::table('lesson_user_force')->where([
                    'user_id' => $student,
                    'lesson_id' => $id
                ])->delete();
            }
        }
        return $this->ok();
    }

}