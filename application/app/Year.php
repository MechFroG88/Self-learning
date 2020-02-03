<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    /**
    * primaryKey 
    * 
    * @var integer
    * @access protected
    */
    protected $primaryKey = null;

    public $timestamps = false;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $table = 'years';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lesson_id','year'
    ];

    public function lessons()
    {
        return $this->belongsTo('App\Lesson');
    }
}
