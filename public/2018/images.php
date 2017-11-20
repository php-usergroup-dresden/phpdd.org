<?php declare(strict_types=1);
/**
 * @author hollodotme
 */

header( 'Content-Type: application/json; charset=utf-8', true, 200 );

$files     = [];
$sourceDir = __DIR__ . DIRECTORY_SEPARATOR . 'media';
$resized   = 'photos-400x267';
$original  = 'photos-orig';
$baseUrl   = 'https://2018.phpdd.org/media';

$dir = new \DirectoryIterator( $sourceDir . DIRECTORY_SEPARATOR . $resized );
foreach ( $dir as $file )
{
	if ( $file->isDot() || $file->isDir() )
	{
		continue;
	}

	$origPath = sprintf( '%s%s%s%s%s', $sourceDir, DIRECTORY_SEPARATOR, $original, DIRECTORY_SEPARATOR, $file->getFilename() );

	if ( !file_exists( $origPath ) )
	{
		continue;
	}

	$resizedUrl            = sprintf( '%s/%s/%s', $baseUrl, $resized, $file->getFilename() );
	$originalUrl           = sprintf( '%s/%s/%s', $baseUrl, $original, $file->getFilename() );
	$files[ $originalUrl ] = [
		'width'  => 400,
		'height' => 267,
		'url'    => $resizedUrl,
	];
}

echo json_encode( $files, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
flush();
