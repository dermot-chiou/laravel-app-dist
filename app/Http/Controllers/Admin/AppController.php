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
        $this->disk = Storage::disk('public');
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
        $app->load('files');
        $url = $this->url->to('/#/'.$appId);
        return view('admin.app.show', compact('app', 'url'));
    }

    public function create()
    {
        return view('admin.app.create');
    }

    public function store(Request $request, \CFPropertyList\CFPropertyList $plist)
    {
        $appId = null;
        if($request->app_file->getClientOriginalExtension() == 'ipa')
            $appId = $this->storeIPA($request->app_file, $plist);
        if ($request->app_file->getClientOriginalExtension() == 'apk')
            $appId = $this->storeAPK($request->app_file);
        if ($appId == null)
            return redirect()->action('Admin\AppController@index');
        else
            return redirect()->action('Admin\AppController@show', [$appId]);
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
        $app = MobileApp::where('app_id', $appId)->first();
        if (!$app)
            abort(404);

        $app->description = $request->description;
        $app->name = $request->name;
        $app->save();
        return redirect()->action('Admin\AppController@edit', [$appId]);
    }

    public function destroy($appId, $file = null)
    {
        $mobileApp = MobileApp::where('app_id', $appId)->first();

        $dir = 'apps/'.$appId;
        if (!$this->disk->has($dir))
        {
            $mobileApp->delete();
        }
        if ($file == null && $this->disk->has($dir))
        {
            $this->disk->deleteDirectory($dir);
            foreach ($mobileApp->files() as $file)
            {
                $file->delete();
            }

            $mobileApp->delete();
        }

        else if($file != null)
        {
            $filePath = $dir.'/'.$file;
            if ($this->disk->has($filePath))
                $this->disk->delete($filePath);

            $mobileAppFile = $mobileApp->files()->where('file_name', $file)->first();
            $mobileAppFile->delete();
        }

        return redirect()->back();
    }

    private function storeIPA($file, $plist)
    {

        $appId = null;
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $zip = \zip_open($file->getRealPath());
        if ($zip)
        {
            while ($zip_entry = \zip_read($zip)) {
                $fileinfo = \pathinfo(\zip_entry_name($zip_entry));
                if ($fileinfo['basename']=="Info.plist")
                {
                    if (\zip_entry_open($zip, $zip_entry, "r")) {
                        $disk = Storage::disk('public');
                        $buf =\ zip_entry_read($zip_entry, \zip_entry_filesize($zip_entry));
                        $plist->parse($buf);
                        $plist = $plist->toArray();
                        $appStorePath = $this->getAppStorePath($plist['CFBundleIdentifier']);
                        $file->storeAs($appStorePath, $file->getClientOriginalName(), 'public');
                        $ipaURL = ($disk->url($appStorePath.$file->getClientOriginalName()));

                        $distributionPlist = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>items</key>
	<array>
		<dict>
			<key>assets</key>
			<array>
				<dict>
					<key>kind</key>
					<string>software-package</string>
					<key>url</key>
					<string>'.$ipaURL.'</string>
				</dict>
			</array>
			<key>metadata</key>
			<dict>
				<key>bundle-identifier</key>
				<string>'.$plist['CFBundleIdentifier'].'</string>
				<key>bundle-version</key>
				<string>'.$plist['CFBundleVersion'].'</string>
				<key>kind</key>
				<string>software</string>
				<key>title</key>
				<string>'.$plist['CFBundleDisplayName'].'</string>
			</dict>
		</dict>
	</array>
</dict>
</plist>';
                        $disk->put($appStorePath.$fileName.'.plist', $distributionPlist);
                        \zip_entry_close($zip_entry);

                        $mobileApp = MobileApp::firstOrNew(['app_id' => $plist['CFBundleIdentifier']]);
                        $mobileApp->name = $plist['CFBundleDisplayName'];
                        $mobileApp->save();

                        $mobileAppFile = $mobileApp->files()->where('file_name', $file->getClientOriginalName())->first();
                        if ($mobileAppFile == null)
                        {
                            $mobileAppFile = MobileAppFile::create([
                                'app_id' => $mobileApp->id,
                                'file_name' => $file->getClientOriginalName(),
                                'version' => $plist['CFBundleVersion'],
                            ]);
                        }
                        else
                        {
                            $mobileAppFile->version = $plist['CFBundleVersion'];
                            $mobileAppFile->save();
                        }


                        $appId = $plist['CFBundleIdentifier'];
                        break;
                    }
                }
            }
            \zip_close($zip);
        }

        return $appId;
    }

    private function storeAPK($file)
    {
        $appId = null;
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $apk = new \ApkParser\Parser($file->getRealPath());
        $manifest = $apk->getManifest();
        $ids = explode('.', $manifest->getPackageName());
        if ($ids[0] == 'air')
            unset($ids[0]);
        $appId = implode('.', $ids);
        $version = $manifest->getVersionName();

        $mobileApp = MobileApp::firstOrNew(['app_id' => $appId]);
        $mobileApp->name = $appId;
        $mobileApp->save();

        $mobileAppFile = $mobileApp->files()->where('file_name', $file->getClientOriginalName())->first();
        if ($mobileAppFile == null)
        {
            $mobileAppFile = MobileAppFile::create([
                'app_id' => $mobileApp->id,
                'file_name' => $file->getClientOriginalName(),
                'version' => $version,
            ]);
        }
        else
        {
            $mobileAppFile->version = $version;
            $mobileAppFile->save();
        }

        $appStorePath = $this->getAppStorePath($appId);
        $file->storeAs($appStorePath, $file->getClientOriginalName(), 'public');

        return $appId;
    }

    private function getAppStorePath($appId)
    {
        return 'apps/'.$appId.'/';
    }
}
