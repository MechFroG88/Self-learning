<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class _Class extends Model
{

    public $timestamps = false;
    protected $table = 'classes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cn_name','en_name'
    ];

    public function users()
    {
        return $this->hasMany('App\User');
    }
}
