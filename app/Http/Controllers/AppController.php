<?php

namespace App\Http\Controllers;

use App\MobileApp;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Storage;


class AppController extends Controller
{
    private $disk;
    private $url;
    public function __construct(UrlGenerator $url)
    {
        $this->disk = Storage::disk(config('disk.default'));
        $this->url = $url;
    }

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
    			$data = $this->{$extension}($appId, 'apps/'.$app->app_id.'/'.$file->file_name, $this->disk);
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

    public function version($appId)
    {
        $app = MobileApp::where('app_id', $appId)->first();
        if(!$app)
            abort(404);
        $resp = [];

        foreach ($app->files as $file)
        {
            $extension = pathinfo($file->file_name, PATHINFO_EXTENSION);
            $filename = pathinfo($file->file_name, PATHINFO_FILENAME);
            $filename = explode('-', $filename);
            $device = $filename[count($filename) - 1] == 'tablet' ? 'pad' : 'mobile';

            $os = $extension == 'ipa' ? 'iOS' : 'Android';
            $data = $this->{$extension}($app->app_id, 'apps/'.$app->app_id.'/'.$file->file_name, $this->disk);
            $resp[$os][$device]['ver'] = $file->version;
            $resp[$os][$device]['url'] = url('/#/'.$app->app_id);
            $resp[$os][$device]['file_url'] = $data['url'];
        }

        return $resp;


    }
}
