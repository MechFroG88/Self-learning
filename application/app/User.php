<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $table = 'users';

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
        'id','cn_name', 'class_id', 'hash_ic',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'hash_ic', 'remember_token',
    ];

    public function classes()
    {
        return $this->belongsTo('App\_Class');
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
