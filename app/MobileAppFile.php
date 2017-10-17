<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MobileAppFile extends Model
{
    public $fillable = ['app_id', 'file_name', 'version', 'bundle_id', 'original_name', 'tablet'];

    protected $hidden = ['id', 'created_at', 'updated_at', 'app_id'];
}
