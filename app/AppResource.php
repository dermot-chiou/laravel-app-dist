<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppResource extends Model
{
    public $fillable = ['app_id', 'path'];
}
