<?php

namespace App\Http\Controllers;

use App\MobileApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppController extends Controller
{
    public function index()
    {
        $apps = MobileApp::all();
        return $apps;
    }

    public function show($appId)
    {
        $app = MobileApp::where('app_id', $appId)->first();
        $app->load('files');
    	foreach ($app->files as $file)
    	{
    		$pathinfo = pathinfo($file->file_name);
    		$extension = $pathinfo['extension'];
    		if (method_exists($this, $extension)) {
    			$data = $this->{$extension}($appId, 'apps/'.$app->app_id.'/'.$file->file_name, Storage::disk('public'));
    			//$resp['urls'][$data['os']][$data['device']] = $data;
                $file->os = $data['os'];
                $file->device = $data['device'];
                $file->url = $data['url'];
    		}
    	}
        //dd($app);
    	return $app;
    }

    private function ipa($appId, $file, $disk)
    {
        $pathinfo = pathinfo($file);
        $extension = $pathinfo['extension'];
        $filename = $pathinfo['filename'];

        return ['os' => 'iOS',
            'device' => $this->device($filename),
            'url' => 'itms-services://?action=download-manifest&url='. action('PlistController@getPlist', [$appId, $filename])];
    }

    private function plist($appId, $file, $disk)
    {
    	$pathinfo = pathinfo($file);
    	$extension = $pathinfo['extension'];
    	$filename = $pathinfo['filename'];

    	return ['os' => 'iOS',
    	'device' => $this->device($filename),
    	'url' => 'itms-services://?action=download-manifest&url='. $disk->url($file)];
    }

    private function apk($appId, $file, $disk)
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
