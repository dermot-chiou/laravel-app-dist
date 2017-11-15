<?php

namespace App\Http\Controllers;

use App\MobileApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlistController extends Controller
{
    public function getPlist($appId, $filename){
        $disk = Storage::disk(config('disk.default'));
        $mobileApp = MobileApp::where('app_id', $appId)->first();
        $mobileAppFile = $mobileApp->files()->where('file_name', $filename.'.ipa')->first();
        $ipaURL = cdn('apps/'.$appId.'/'.$mobileAppFile->file_name, $disk->url('apps/'.$appId.'/'.$mobileAppFile->file_name));
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
				<string>'.$mobileAppFile->bundle_id.'</string>
				<key>bundle-version</key>
				<string>'.$mobileAppFile->version.'</string>
				<key>kind</key>
				<string>software</string>
				<key>title</key>
				<string>'.$mobileApp->name.'</string>
			</dict>
		</dict>
	</array>
</dict>
</plist>';

        //dd($distributionPlist);
        return response($distributionPlist, 200)->header('Content-Type', 'application/xml');;
    }
}
