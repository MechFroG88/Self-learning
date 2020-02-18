<?php

namespace App;

//use Watson\Rememberable\Rememberable;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    //use Rememberable;
    //public $rememberCacheTag = 'lesson_queries';
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $table = 'lessons';
    //public $rememberFor = 1;

    protected $with = ['years','periods'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','location','subject','limit','current','gender','stream','description'
    ];

    public function years()
    {
        return $this->hasMany('App\Year');
    }

    public function periods()
    {
        return $this->hasMany('App\Period');
    }

    public function users()
    {
        return $this->belongstoMany('App\User');
    }

    public function users_force()
    {
        return $this->belongstoMany('App\User','lesson_user_force');
    }
}
