<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $table = 'lessons';

    protected $with = ['years','periods'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','location','subject','limit','current','gender','stream'
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
