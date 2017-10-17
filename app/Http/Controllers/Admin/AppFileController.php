<?php

namespace App\Http\Controllers\Admin;

use App\MobileApp;
use App\MobileAppFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AppFileController extends Controller
{
    private $appId;
    private $mobileApp;
    private $disk;
    public function __construct(Request $request)
    {
        $this->appId = $request->route()->parameter('appId');
        $this->mobileApp = MobileApp::where('app_id', $this->appId)->first();
        $this->disk = Storage::disk('public');
        if (!$this->mobileApp)
            abort(404);
    }

    public function create($appId)
    {
        return view('admin.file.create', compact('appId'));
    }
    public function store($appId, Request $request,  \CFPropertyList\CFPropertyList $plist)
    {
        if($request->app_file->getClientOriginalExtension() == 'ipa')
            $data = $this->parseIAP($request->app_file, $plist);
        if ($request->app_file->getClientOriginalExtension() == 'apk')
            $data = $this->parseAPK($request->app_file);
        $tablet = $request->tablet == null ? false : true;
        $this->saveFile($request->app_file, $data, $tablet);

        return redirect()->action('Admin\AppController@show', [$this->mobileApp->app_id]);
    }

    public function destroy($appId, $file)
    {
        $mobileAppFile = $this->mobileApp->files()->where('file_name', $file)->first();
        if($mobileAppFile)
            $mobileAppFile->delete();
        $filePath = 'apps/'.$this->mobileApp->app_id.'/'.$file;
        if ($this->disk->has($filePath))
            $this->disk->delete($filePath);

        return redirect()->back();
    }

    private function saveFile($file, $data, $tablet)
    {
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $fileName = $this->mobileApp->app_id.'-'.($tablet ? 'tablet' : 'mobile').'.'.$extension;

        $mobileAppFile = $this->mobileApp->files()->where('file_name', $fileName)->first();
        if (!$mobileAppFile)
        {
            MobileAppFile::create([
                'file_name' => $fileName,
                'app_id' => $this->mobileApp->id,
                'version' => $data['version'],
                'bundle_id' => $data['app_id'],
                'original_name' => $file->getClientOriginalName(),
                'tablet' => $tablet
            ]);
        }
        else
        {
            $mobileAppFile->version = $data['version'];
            $mobileAppFile->bundle_id = $data['app_id'];
            $mobileAppFile->original_name = $file->getClientOriginalName();
            $mobileAppFile->save();
        }

        $file->storeAs('apps/'.$this->mobileApp->app_id.'/', $fileName, 'public');
    }

    private function parseIAP($file, $plist)
    {
        $zip = \zip_open($file->getRealPath());
        if ($zip)
        {
            while ($zip_entry = \zip_read($zip)) {
                $fileinfo = \pathinfo(\zip_entry_name($zip_entry));
                if ($fileinfo['basename']=="Info.plist")
                {
                    if (\zip_entry_open($zip, $zip_entry, "r")) {

                        $buf =\ zip_entry_read($zip_entry, \zip_entry_filesize($zip_entry));
                        $plist->parse($buf);
                        $plist = $plist->toArray();

                        \zip_entry_close($zip_entry);

                        return [
                            'app_id' => $plist['CFBundleIdentifier'],
                            'version' => $plist['CFBundleVersion']
                        ];
                    }
                }
            }
            \zip_close($zip);
        }

        return null;
    }

    private function parseAPK($file)
    {
        $apk = new \ApkParser\Parser($file->getRealPath());
        $manifest = $apk->getManifest();
        return [
            'app_id' => $manifest->getPackageName(),
            'version' => $manifest->getVersionName()
        ];
    }
}
