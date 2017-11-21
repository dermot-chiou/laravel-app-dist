<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppResource extends Model
{
    public $fillable = ['app_id', 'path', 'md5', 'sha1'];
    protected $hidden = ['id', 'app_id', 'created_at', 'updated_at'];
}
