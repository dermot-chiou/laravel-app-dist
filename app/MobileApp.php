<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class MobileApp extends Model
{
    public $fillable = ['app_id', 'name'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function files()
    {
        return $this->hasMany('App\MobileAppFile', 'app_id', 'id');
    }
}
