<?php

$opts = getOpt('s:d:');

$source = array_key_exists('s', $opts) ? $opts['s'] : '/default/source';
$dest = array_key_exists('d', $opts) ? $opts['d'] : '/default/destination';

if ( !( $source && $destination ) ) {
	print 'Source and destination required, pass with -s and -d command line params.'
}

scan($source);

function scan($dir) {
	$finfo = new finfo();
	$files = scandir($dir);
	foreach ( $files as $file ) {
		$fullpath = $dir . '/' . $file;	
		$info = $finfo->file($fullpath);
		if ( $info == 'directory' && !in_array($file,['.', '..']) ) {
			scan($fullpath);
		}
		$params = explode(',', $info);
		$type = $params[0];
		if ( preg_match('/image/i', $type) ) {
			process($fullpath, $file);
		}	
	}
}

function process($path, $file) {
	global $dest; 
	$timestamp = filemtime($path);
	$date  = date('Y/m/d', $timestamp);
	$destPath = $dest . '/' . $date;
	if ( !file_exists($destPath) ) {
		mkdir($destPath, 0755, true);
	}
	$destPath .= '/';
	$destPath .= $file;
	copy($path, $destPath);
}

?>

