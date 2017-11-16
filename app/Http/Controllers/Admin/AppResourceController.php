<?php

namespace App\Http\Controllers\Admin;

use App\AppResource;
use App\MobileApp;
use App\Service\CloudFront;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AppResourceController extends Controller
{
    private $appId;
    private $mobileApp;
    private $disk;
    private $cloudFront;
    public function __construct(Request $request, CloudFront $cloudFront)
    {
        $this->appId = $request->route()->parameter('appId');
        $this->mobileApp = MobileApp::where('app_id', $this->appId)->first();
        $this->disk = Storage::disk(config('disk.default'));
        $this->cloudFront = $cloudFront;
        if (!$this->mobileApp)
            abort(404);
    }

    public function create($appId)
    {
        return view('admin.resource.create', ['app' => $this->mobileApp]);
    }

    public function store($appId, Request $request)
    {
        $file = $request->file('asset');
        $path = '/apps/'.$this->mobileApp->app_id.'/';
        $file->storeAs($path, $file->getClientOriginalName(), config('disk.default'));
        $this->cloudFront->invalidate($path.$file->getClientOriginalName());

        AppResource::create(['app_id' => $this->mobileApp->id, 'path' => $path.$file->getClientOriginalName()]);

        return redirect()->back();
    }

    public function destroy($appId, $resourceId)
    {
        $resource = AppResource::find($resourceId);
        if ($this->disk->has($resource->path))
        {
            $this->disk->delete($resource->path);
            $this->cloudFront->invalidate($resource->path);
        }

        $resource->delete();
        return redirect()->back();
    }
}
