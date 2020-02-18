<?php

namespace App;

use \Watson\Rememberable\Rememberable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;


class User extends Authenticatable
{
    use Rememberable;
    public $rememberCacheTag = 'user_queries';

    protected $table = 'users';
    public $rememberFor = 1;

    protected $with = ['classes','lessons','lessons_force'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','cn_name', 'class_id', 'ic','type','is_submit','gender','en_name','class_no'
    ];

    public function getAuthPassword() {
        return $this->ic;
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'ic', 'remember_token','class_id'
    ];

    public function classes()
    {
        return $this->belongsTo('App\_Class','class_id');
    }

    public function lessons()
    {
        return $this->belongstoMany('App\Lesson');
    }

    public function lessons_force()
    {
        return $this->belongstoMany('App\Lesson','lesson_user_force');
    }
}
