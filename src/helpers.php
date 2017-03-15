<?php

/**
 * Part of the Platform Media extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Media extension
 * @version    5.0.4
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Media\Models\Media;

if (! function_exists('formatBytes')) {
    function formatBytes($size, $precision = 2)
    {
        $base = log($size) / log(1024);

        $suffixes = ['', 'KB', 'MB', 'GB', 'TB'];

        $suffix = $suffixes[floor($base)];

        return round(pow(1024, $base - floor($base)), $precision)." {$suffix}";
    }
}

if (! function_exists('getImagePath')) {
    function getImagePath(Media $media, $presetName, array $attributes = [])
    {
        $manager = app('platform.media.manager');

        if (! $manager->isValidPreset($presetName)) {
            if (! isset($attributes['macros'])) {
                $attributes['macros'][] = 'fit';
            }

            $manager->setPreset($presetName, $attributes);
        }

        $preset = $manager->getPreset($presetName);

        $cachedMediaPath = $preset->path.'/'.basename($media->path);

        if (! app('files')->exists($cachedMediaPath)) {
            $manager->applyPreset($presetName, 'up', $media);
        }

        return url(str_replace(public_path(), null, $cachedMediaPath));
    }
}
