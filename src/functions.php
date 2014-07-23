<?php

if ( ! function_exists('media_cache_path'))
{
	function media_cache_path($media)
	{
		return 'cache/media/' . $media; # make this a config option
	}
}

if ( ! function_exists('formatBytes'))
{
	function formatBytes($size, $precision = 2)
	{
		$base = log($size) / log(1024);

		$suffixes = ['', 'KB', 'MB', 'GB', 'TB'];

		$suffix = $suffixes[floor($base)];

		return round(pow(1024, $base - floor($base)), $precision) . " {$suffix}";
	}
}
