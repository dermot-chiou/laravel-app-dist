<?php

namespace App\Http\Controllers\Admin;

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
        $dirs = $this->disk->directories('apps');
        $apps = [];
        foreach ($dirs as $dir)
        {
            $manifest = json_decode($this->disk->get($dir.'/manifest.json'));
            $manifest->url = $this->url->to('/#/'.$manifest->id);
            $apps[] = $manifest;
        }

        return view('admin.app.index', compact('apps'));
    }

    public function show($appId)
    {
        if (!$this->disk->has('apps/'.$appId.'/manifest.json'))
            return redirect()->action('admin\AppController@index');
        $app = json_decode($this->disk->get('apps/'.$appId.'/manifest.json'));
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
            return redirect()->action('admin\AppController@index');
        else
            return redirect()->action('admin\AppController@show', [$appId]);
    }

    public function edit($appId)
    {

    }

    public function update($appId, Request $request)
    {

    }

    public function destroy($appId, $file = null)
    {
        $dir = 'apps/'.$appId;
        if ($file == null && $this->disk->has($dir))
            $this->disk->deleteDirectory($dir);
        else
        {
            $filePath = $dir.'/'.$file;
            if ($this->disk->has($filePath))
                $this->disk->delete($filePath);

            $app = json_decode($this->disk->get('apps/'.$appId.'/manifest.json'), true);
            unset($app[$file]);
            $this->disk->put('apps/'.$appId.'/manifest.json', json_encode($app));
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

                        $manifest = [];
                        if($disk->exists($appStorePath.'manifest.json'))
                        {
                            $manifest = json_decode($disk->get($appStorePath.'manifest.json'), true);
                        }

                        $manifest['id'] = $plist['CFBundleIdentifier'];
                        $manifest[$file->getClientOriginalName()]['version'] = $plist['CFBundleVersion'];
                        $manifest['name'] = $plist['CFBundleDisplayName'];

                        $disk->put($appStorePath.'manifest.json', json_encode($manifest));
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
        $zip = \zip_open($file->getRealPath());
        if ($zip)
        {
            while ($zip_entry = \zip_read($zip)) {
                $fileinfo = \pathinfo(\zip_entry_name($zip_entry));
                if ($fileinfo['basename']=="application.xml")
                {
                    if (\zip_entry_open($zip, $zip_entry, "r")) {
                        $disk = Storage::disk('public');
                        $buf =\ zip_entry_read($zip_entry, \zip_entry_filesize($zip_entry));
                        $xml = \Parser::xml($buf);
                        $appStorePath = $this->getAppStorePath($xml['id']);
                        $file->storeAs($appStorePath, $file->getClientOriginalName(), 'public');
                        \zip_entry_close($zip_entry);

                        $manifest = [];
                        if($disk->exists($appStorePath.'manifest.json'))
                        {
                            $manifest = json_decode($disk->get($appStorePath.'manifest.json'), true);
                        }

                        $manifest['id'] = $xml['id'];
                        $manifest[$file->getClientOriginalName()]['version'] = $xml['versionNumber'];
                        $manifest['name'] = $xml['name'];

                        $disk->put($appStorePath.'manifest.json', json_encode($manifest));
                        $appId = $xml['id'];
                        break;
                    }
                }
            }
            \zip_close($zip);
        }
        return $appId;
    }

    private function getAppStorePath($appId)
    {
        return 'apps/'.$appId.'/';
    }
}
