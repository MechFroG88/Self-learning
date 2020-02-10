<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Validator;	
use App\User;
use App\Lesson;
use Auth;
use DB;

class UserController extends Controller
{

    private $rules = [
        "id"       => "required|integer|unique:users,id",
        "class_id" => "required|integer",
        "cn_name"  => ["required","regex:/[\x{4e00}-\x{9fa5}]+/u"],
        "ic"       => "required",
        "type"     => "required|integer|between:0,1",
        "gender"   => "required|in:男,女",
    ];

    private $login_rules = [
        "id" => "required|integer",
        "ic" => "required"
    ];

    private $submit_rules = [
        "lessons" => "required|array",
        "lessons.*" => "integer"
    ];

    public function login(Request $data)
    {
        $validator = Validator::make($data->all(), $this->login_rules);
        if ($validator->fails()) return $this->fail();
        $id = $data->id;
        $ic = $data->ic;
        if (Auth::attempt(['id'=>$id, 'password'=>$ic],true)) {
            return $this->ok();
        } else {
            return response("Unauthorized",401);
        }
    }

    public function logout()
    {
        Auth::logout();
        return $this->ok();
    }

    public function create(Request $data)
    {
        $validator = Validator::make($data->all(), $this->rules);
        if ($validator->fails()) return $this->fail();
        $data->merge(['ic' => Hash::make($data->ic)]);
        User::create($data->all());
        return $this->ok();
    }

    public function get_all()
    {
        $users = User::with('classes','lessons','lessons_force')->where('type',1)->get();
        $data = [];
        foreach ($users as $user){
            $single_data = json_decode($user->toJson());
            unset($single_data->classes);
            unset($single_data->lessons);
            unset($single_data->lessons_force);
            $class = $user->classes;
            $lessons = $user->lessons;
            $lessons_force = $user->lessons_force;

            if (isset($class->cn_name)) $single_data->class = $class->cn_name;
            $single_data->lessons = [];
            $single_data->forced_lessons = [];
            foreach($lessons as $lesson){
                $periods = $lesson->period;
                foreach($periods as $period){
                    $single_data[lessons[$lesson->period]] = $lesson->id;
                }
            }
            foreach($lessons_force as $lesson_force){
                $periods = $lesson_force->period;
                foreach($periods as $period){
                    $single_data[forced_lessons[$lesson_force->period]] = $lesson_force->id;
                }
            }
            array_push($data,$single_data);
        }
        return response($data,200);
    }

    public function get_current()
    {
        $user = User::find(Auth::id());
        $data = json_decode($user->toJson());
        $class = $user->classes;
        $lessons = $user->lessons;
        $lessons_force = $user->lessons_force;
        
        if (isset($class->cn_name)) $data->class = $class->cn_name;
        $data->lessons = [];
        $data->forced_lessons = [];
        foreach($lessons as $lesson){
            $periods = $lesson->periods;
            foreach($periods as $period){
                array_push($data->lessons,[$period->period => $lesson->id]);
            }
        }

        foreach($lessons_force as $lesson_force){
            $periods = $lesson_force->periods;
            foreach($periods as $period){
                array_push($data->forced_lessons,[$period->period => $lesson_force->id]);
            }
        }
        return response((array) $data,200);
    }

    public function get_lesson()
    {
        $user = User::find(Auth::id());
        $current_year = (int)(((int)date('y'))-intdiv(Auth::id(),10000)+1);
        $class = $user->classes->cn_name;
        $stream;
        if (substr($class,1,1) == '理') $stream = '理';
        else if (substr($class,1,1) == '文') $stream = '文';
        else $stream = '无';
        $lessons = Lesson::whereIn('gender',[$user->gender,'无'])
                         ->whereIn('stream',[$stream,'无'])
                         ->get();
        $data = [];
        foreach($lessons as $lesson){
            $single_data = json_decode($lesson->toJson());
            unset($single_data->periods);
            unset($single_data->years);
            $periods = $lesson->periods;
            $years = $lesson->years;
            $single_data->period = [];
            $single_data->year = [];
            $ok = false;
            foreach ($years as $year){
                if ($year->year == $current_year) $ok = true;
                array_push($single_data->year,$year->year);
            }

            foreach ($periods as $period){
                array_push($single_data->period,$period->period);
            }

            if (ok) array_push($data,$single_data);
        }
        return response($data,200);
    }

    public function submit(Request $data)
    {
        $validator = Validator::make($data->all(), $this->submit_rules);
        if ($validator->fails()) return $this->fail();
        //if (User::find(Auth::id())->is_submit) return response("You have already submitted",400);
        $slot = true;
        $check = [];
        $user = User::find(Auth::id());
        $lessons = $user->lessons;
        $lessons_force = $user->lessons_force;

        $class = $user->classes->cn_name;
        $stream;
        if (substr($class,1,1) == '理') $stream = '理';
        else if (substr($class,1,1) == '文') $stream = '文';
        else $stream = '无';

        $gender = $user->gender;

        foreach ($lessons_force as $lesson_force){
            foreach ($lesson_force->periods as $period){
                if (isset($check[$period->period])) $slot = false;
                else $check[$period->period] = 1;
            }
        }

        foreach ($lessons as $lesson){
            foreach ($lesson->periods as $period){
                if (isset($check[$period->period])) $slot = false;
                else $check[$period->period] = 1;
            }
        }

        $lessons = $data->lessons;

        foreach ($lessons as $lesson){
            $single = Lesson::findOrFail($lesson);
            if ($single->current >= $single->limit) $slot = false;
            $current_year = (int)date('y')-intdiv(Auth::id(),10000)+1;
            $not_allowed = true;
            if ($single->stream == '理' && $stream != '理') $not_allowed = false;
            if ($single->stream == '文' && $stream != '文') $not_allowed = false;
            if ($single->gender == '男' && $gender != '男') $not_allowed = false;
            if ($single->gender == '女' && $gender != '女') $not_allowed = false;
            foreach ($single->years as $year){
                if ($year->year == $current_year) $not_allowed = false; 
            }
            if ($not_allowed) $slot = false;
            foreach ($single->periods as $period){
                if (isset($check[$period->period])) $slot = false;
                else $check[$period->period] = 1;
            }
        }

       
        if(!$slot) return response("Bad Request",400);
        User::where('id',Auth::id())->update(['is_submit' => 1]);
        foreach ($lessons as $lesson){
            DB::table('lesson_user')->insert(
                ['user_id' => Auth::id(), 'lesson_id' => $lesson]
            );
            $temp = Lesson::find($lesson);
            $temp->current++;
            $temp->save();
        }
        return $this->ok();
    }

    public function edit(Request $data,$id)
    {
        $validator = Validator::make($data->all(),$this->rules);
        if ($validator->fails()) return $this->fail();
        if ($id != $data->id) return $this->fail();
        $data->merge(['ic' => Hash::make($data->ic)]); 
        User::where('id', $id)
            ->update([
                "cn_name" => $data->cn_name,
                "ic" => $data->ic,
                "class_id" => $data->class_id,
                "type" => $data->type,
            ]);
        return $this->ok();
    }

    public function delete(Request $data,$id)
    {
        User::where('id', $id)->delete();
        return $this->ok();
    }

}