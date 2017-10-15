<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppController extends Controller
{
    public function index()
    {
    	$resp = [];
    	$disk = Storage::disk('public');
    	$directories = $disk->directories('apps');
    	foreach ($directories as $dir) 
    	{
    		$data = [];
    		$files = $disk->files($dir);
    		foreach ($files as $file) 
    		{
    			$basename = basename($file);
    			if ($basename == 'manifest.json') {
    				$data = json_decode($disk->get($file), true);
    				break;
    			}
    		}
    		$resp[] = $data;
    	}

    	return $resp;
    }

    public function show($appId)
    {
    	
    	$disk = Storage::disk('public');
    	$files = $disk->files('apps/'.$appId);
    	$resp['info'] = json_decode($disk->get('apps/'.$appId.'/manifest.json'), true);
    	foreach ($files as $file) 
    	{
    		$pathinfo = pathinfo($file);
    		$extension = $pathinfo['extension'];
    		if (method_exists($this, $extension)) {
    			$data = $this->{$extension}($file, $disk);
    			$resp['urls'][$data['os']][$data['device']] = $data;
    		}
    	}
    	return $resp;
    }

    private function plist($file, $disk)
    {
    	$pathinfo = pathinfo($file);
    	$extension = $pathinfo['extension'];
    	$filename = $pathinfo['filename'];

    	return ['os' => 'iOS',
    	'device' => $this->device($filename),
    	'url' => 'itms-services://?action=download-manifest&url='. $disk->url($file)];
    }

    private function apk($file, $disk)
    {
    	$pathinfo = pathinfo($file);
    	$extension = $pathinfo['extension'];
    	$filename = $pathinfo['filename'];

    	return ['os' => 'Android',
    	'device' => $this->device($filename), 
    	'url' => $disk->url($file)];
    }

    private function device($filename)
    {
    	return strpos($filename, 'mobile') !== false ? 'phone' : 'tablet';
    }
}
