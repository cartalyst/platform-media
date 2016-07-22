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
 * @version    4.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Commands;

use Platform\Media\Models\Media;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputOption;

class ReGenerateImages extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'images:regenerate
                            {--media : Whether or not if we should filter by the given media.}
                            {--preset : Whether or not if we should filter by the given preset.}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Regenerates images according to defined sizes.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $files = $this->laravel['files'];

        $intervention = $this->laravel['image'];

        $medias = $this->laravel['platform.media']->get();

        $filesystem = $this->laravel['cartalyst.filesystem'];

        $presets = $this->laravel['config']->get('platform-media.presets');

        foreach ($medias as $media) {
            foreach ($presets as $name => $info) {
                $path = $this->getPath($media, $name);

                if (! $files->exists($path)) {
                    $contents = $filesystem->read($media->path);

                    $macro = isset($info['macro']) ? $info['macro'] : null;

                    // Do we have a macro to run against?
                    if ($macro) {

                    } else {
                        $width = isset($info['width']) ? $info['width'] : null;

                        $height = isset($info['height']) ? $info['height'] : null;

                        $constraints = isset($info['constraints']) ? $info['constraints'] : [];

                        $intervention->make($contents)
                            ->fit($width, $height, function ($constraint) use ($constraints) {
                                foreach ($constraints as $_constraint) {
                                    $constraint->{$_constraint}();
                                }
                            })->save($path)
                        ;
                    }
                }
            }
        }

        $this->info('done');
    }

    /**
     * Returns the prepared file path.
     *
     * @param  \Platform\Media\Models\Media $media
     * @param  string  $dir
     * @return string
     */
    protected function getPath(Media $media, $dir)
    {
        $path = public_path('cache/media');

        $files = $this->laravel['files'];

        $directory = $path.'/'.$dir;

        if (! $files->exists($directory)) {
            $files->makeDirectory($directory);
        }

        $mediaName = $this->prepareFileName($media->name, $media->id);

        return $directory.'/'.$mediaName;
    }

    /**
     * Sanitizes the file name.
     *
     * @param  string  $fileName
     * @return string
     */
    protected function sanitizeFileName($fileName)
    {
        $regex = [ '#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#', '#[ ]#', '![_]+!u' ];

        return preg_replace($regex, '_', strtolower($fileName));
    }

    /**
     * Prepares the filename by sanitizing it and
     * appending the media id to the end.
     *
     * @param  string  $fileName
     * @param  string  $id
     * @return string
     */
    protected function prepareFileName($fileName, $id)
    {
        $fileName = $this->sanitizeFileName($fileName);

        return pathinfo($fileName, PATHINFO_FILENAME)."_{$id}.".pathinfo($fileName, PATHINFO_EXTENSION);
    }
}
