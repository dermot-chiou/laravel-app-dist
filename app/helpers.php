<?php

// global CDN link helper function
function cdn( $asset, $default = null, $secure = true ){


    // Get file name incl extension and CDN URLs
    $cdns = Config::get('app.cdn');
    // Verify if KeyCDN URLs are present in the config file
    if( !$cdns || empty($cdns) )
        return $default ? $default : asset( $asset );

    $assetName = basename( $asset );

    // Remove query string
    $assetName = explode("?", $assetName);
    $assetName = $assetName[0];

    // Select the CDN URL based on the extension
    foreach( $cdns as $cdn => $types ) {
        if( preg_match('/^.*\.(' . $types . ')$/i', $assetName) )
            return cdnPath($cdn, $asset, $secure);
    }

    // In case of no match use the last in the array
    end($cdns);
    //return cdnPath( key( $cdns ) , $asset, $secure);
    return $default ? $default : cdnPath( key( $cdns ) , $asset, $secure);

}

function cdnPath($cdn, $asset, $secure) {
    return  ($secure ? 'https' : 'http') . "://" . rtrim($cdn, "/") . "/" . ltrim( $asset, "/");
}