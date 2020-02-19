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
        "id"       => "required|integer",
        "class_no" => "required|integer",
        "class_id" => "required|integer",
        "cn_name"  => ["required","regex:/[\x{4e00}-\x{9fa5}]+/u"],
        "en_name"  => "required",
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

    public function hash()
    {
        $users = DB::table('users')->select('id','ic')->get();
        foreach ($users as $user){
            DB::table('users')
              ->where('id',$user->id)
              ->update(['ic' => Hash::make($user->ic)]);
        }
        return $this->ok();
    }

    public function flush_cache()
    {
        // User::flushCache();
        Lesson::flushCache();
        // return $this->ok();
    }

    public function login(Request $data)
    {
        $validator = Validator::make($data->all(), $this->login_rules);
        if ($validator->fails()) return $this->fail();
        $id = $data->id;
        $ic = $data->ic;
        if (Auth::attempt(['id'=>$id, 'password'=>$ic],true)) {
            return $this->get_current();
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
        $this->flush_cache();
        return $this->ok();
    }

    public function get_all()
    {
        $users = User::where('type',1)->get();
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
                $periods = $lesson->periods;
                foreach($periods as $period){
                    array_push($single_data->lessons,[$period->period => $lesson->id]);
                }
            }
            foreach($lessons_force as $lesson_force){
                $periods = $lesson_force->periods;
                foreach($periods as $period){
                    array_push($single_data->lessons,[$period->period => $lesson_force->id]);
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
        unset($data->classes);
        unset($data->lessons);
        unset($data->lessons_force);
        $class = $user->classes;
        $lessons = $user->lessons;
        $lessons_force = $user->lessons_force;
        $data->stream = '无';
        if (isset($class->cn_name)) {
            $data->class = $class->cn_name;
            if (strstr($class->cn_name,'理')) $data->stream = '理';
            else if (strstr($class->cn_name,'文')) $data->stream = '文';
            else if (strstr($class->cn_name,'商')) $data->stream = '文';
        }
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
        $current_year = (int)(($user->classes->en_name)[0]);
        $class = $user->classes->cn_name;
        $stream = '无';
        if (strstr($class,'理')) $stream = '理';
        else if (strstr($class,'文')) $stream = '文';
        $force_lesson = [];
        foreach ($user->lessons_force as $lesson_force){
            array_push($force_lesson,$lesson_force->id);
        }
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

            if ($ok) array_push($data,$single_data);
        }
        $lessons = Lesson::whereIn('id',$force_lesson)->get();
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

    public function submit(Request $data)
    {
        if (env('APP_ENV') != 'local'){
            $now = new \DateTime();
            $start = \DateTime::createFromFormat('Y-m-d H:i:s', '2020-02-18 20:00:00');   
            $end = \DateTime::createFromFormat('Y-m-d H:i:s', '2020-02-21 20:00:00');
            if ($now < $start) return $this->fail();            
            if ($now > $end) return $this->fail();
        }
        
        $validator = Validator::make($data->all(), $this->submit_rules);
        if ($validator->fails()) return $this->fail();
        //if (User::find(Auth::id())->is_submit) return response("You have already submitted",400);
        $slot = true;
        $check = [];
        $user = User::find(Auth::id());
        $lessons = $user->lessons;
        $lessons_force = $user->lessons_force;

        $class = $user->classes->cn_name;
        $stream = '无';
        if (strstr($class,'理')) $stream = '理';
        else if (strstr($class,'文')) $stream = '文';

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

        $lessons_check = Lesson::whereIn('id',$data->lessons)->get();

        foreach ($lessons_check as $lesson){
            if ($lesson->current >= $lesson->limit) $slot = false;
            $current_year = (int)(($user->classes->en_name)[0]);
            $not_allowed = true;
            if ($lesson->stream == '理' && $stream != '理') $not_allowed = false;
            if ($lesson->stream == '文' && $stream != '文' && $stream != '商') $not_allowed = false;
            if ($lesson->gender == '男' && $gender != '男') $not_allowed = false;
            if ($lesson->gender == '女' && $gender != '女') $not_allowed = false;
            foreach ($lesson->years as $year){
                if ($year->year == $current_year) $not_allowed = false; 
            }
            if ($not_allowed) $slot = false;
            foreach ($lesson->periods as $period){
                if (isset($check[$period->period])) $slot = false;
                else $check[$period->period] = 1;
            }
        }

        if(!$slot) return response("Bad Request",400);
        User::where('id',Auth::id())->update(['is_submit' => 1]);
        foreach ($data->lessons as $lesson){
            DB::table('lesson_user')->insert(
                ['user_id' => Auth::id(), 'lesson_id' => $lesson]
            );
            DB::table('lessons')->where('id',$lesson)->increment('current');
        }
        $this->flush_cache();
        return $this->get_current();
    }

    public function reselect(Request $data)
    {
        if (env('APP_ENV') != 'local'){
            $now = new \DateTime();
            $start = \DateTime::createFromFormat('Y-m-d H:i:s', '2020-02-18 20:00:00');   
            $end = \DateTime::createFromFormat('Y-m-d H:i:s', '2020-02-21 20:00:00');
            if ($now < $start) return $this->fail();            
            if ($now > $end) return $this->fail();
        }
        $validator = Validator::make($data->all(), $this->submit_rules);
        if ($validator->fails()) return $this->fail();
        $lessons_delete = $data->lessons;
        $lessons = User::find(Auth::id())->lessons;
        $unique_lesson = [];
        foreach ($lessons as $lesson){
            array_push($unique_lesson,$lesson->id);
        }
        $unique_lesson = array_unique($unique_lesson);
        foreach ($unique_lesson as $lesson){
            if (in_array($lesson,$lessons_delete)){
                DB::table('lesson_user')->where([
                    'user_id' => Auth::id(),
                    'lesson_id' => $lesson
                ])->delete();
                 DB::table('lessons')->where('id',$lesson)->decrement('current');
            }
        }
        $this->flush_cache();
        return $this->get_current();
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
        $this->flush_cache();
        return $this->ok();
    }

    public function edit_id(Request $data,$id)
    {
        $data->merge(['ic' => Hash::make($data->ic)]); 
        User::where('id', $id)
            ->update([
                "ic" => $data->ic,
            ]);
        $this->flush_cache();
        return $this->ok();
    }

    public function delete(Request $data,$id)
    {
        User::where('id', $id)->delete();
        $this->flush_cache();
        return $this->ok();
    }

}