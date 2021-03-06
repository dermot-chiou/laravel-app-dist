<?php

namespace App\Http\Controllers\Admin;

use App\MobileApp;
use App\MobileAppFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $apps->load('files');

        foreach ($apps as $app)
        {
            $app->url =  $this->url->to('/#/'.$app->app_id);
        }

        return view('admin.app.index', compact('apps'));
    }

    public function show($appId)
    {
        $app = MobileApp::where('app_id', $appId)->first();
        if(!$app)
            return redirect()->action('Admin\AppController@index');
        $app->load(['files', 'resources']);
        $disk =  Storage::disk(config('disk.default'));
        foreach ($app->resources as $resource)
        {
            if (!$resource->md5)
                $resource->md5 = md5_file($disk->url(ltrim($resource->path, '/')));

            if (!$resource->sha1)
                $resource->sha1 = sha1_file($disk->url(ltrim($resource->path, '/')));

            $resource->save();
        }
        $url = $this->url->to('/#/'.$appId);

        return view('admin.app.show', compact('app', 'url', 'disk'));
    }

    public function create()
    {
        return view('admin.app.create');
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'app_id' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $app = MobileApp::firstOrCreate($request->only(['app_id', 'name']));

        return redirect()->action('Admin\AppController@show', $app->app_id);
    }

    public function edit($appId)
    {
        $app = MobileApp::where('app_id', $appId)->first();
        if (!$app)
            abort(404);
        return view('admin.app.edit', compact('app'));
    }

    public function update($appId, Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $app = MobileApp::where('app_id', $appId)->first();
        if (!$app)
            abort(404);

        $app->description = $request->description;
        $app->name = $request->name;
        $app->save();
        return redirect()->action('Admin\AppController@edit', [$appId]);
    }

    public function manifest($appId, Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'manifest' => 'required',
        ]);

        $validator->after(function ($validator) use($request) {
            if (!$this->isJson($request->get('manifest'))){
                $validator->errors()->add('manifest', 'Invalid format of manifest');
            }
        });

        if ($validator->fails())
        {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        $app = MobileApp::where('app_id', $appId)->first();
        if(!$app)
            abort(404);
        $app->manifest = $request->get('manifest');
        $app->save();
        return redirect()->back();
    }

    private function isJson($str)
    {
        json_decode($str);
        return json_last_error() == JSON_ERROR_NONE;
    }

    public function destroy($appId)
    {
        $mobileApp = MobileApp::where('app_id', $appId)->first();

        $dir = 'apps/'.$appId;

        if ($this->disk->has($dir))
        {
            $this->disk->deleteDirectory($dir);

        }

        foreach ($mobileApp->files() as $file)
        {
            $file->delete();
        }

        $mobileApp->delete();

        /*

        else
        {
            $filePath = $dir.'/'.$file;
            if ($this->disk->has($filePath))
                $this->disk->delete($filePath);

            $mobileAppFile = $mobileApp->files()->where('file_name', $file)->first();
            $mobileAppFile->delete();
        }
        */

        return redirect()->back();
    }
}
