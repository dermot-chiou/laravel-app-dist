<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class IndexController extends Controller
{
    public function index()
    {
    	//dd(Storage::disk('public')->directories('apps'));
    	return view('admin.index');
    }

    public function store(Request $request, \CFPropertyList\CFPropertyList $plist)
    {
    	if($request->app_file->getClientOriginalExtension() == 'ipa')
    		$this->storeIPA($request->app_file, $plist);
    	if ($request->app_file->getClientOriginalExtension() == 'apk') 
    		$this->storeAPK($request->app_file);

    	return redirect()->action('Admin\IndexController@index');
    }

    private function storeIPA($file, $plist)
    {
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

			      	break;
			    }
			}
		  }
		  \zip_close($zip);
		}
    }

    private function storeAPK($file)
    {
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

			      break;
			    }
			}
		  }
		  \zip_close($zip);
		}
    }

    private function getAppStorePath($appId)
    {
    	return 'apps/'.$appId.'/';
    }
}
