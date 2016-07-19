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
 * @version    3.3.1
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
     * {@inheritDoc}
     */
    protected $name = 'images:regenerate';

    /**
     * {@inheritDoc}
     */
    protected $description = 'Regenerates images according to defined sizes.';

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $presets = $this->laravel['config']->get('platform-media.presets');

        $medias = $this->laravel['platform.media']->get();

        foreach ($medias as $media) {
            foreach ($presets as $name => $info) {
                $path = $this->getPath($media, $name);

                if (! $this->laravel['files']->exists($path)) {
                    $contents = $this->laravel['cartalyst.filesystem']->read($media->path);

                    $this->laravel['image']->make($contents)
                        ->fit(array_get($info, 'width'), array_get($info, 'height'), function ($constraint) use ($info) {
                            foreach (array_get($info, 'constraints', []) as $_constraint) {
                                $constraint->{$_constraint}();
                            }
                        })->save($path);
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
     *
     * @return string
     */
    protected function getPath(Media $media, $dir)
    {
        $path = public_path('cache/media');

        if (! $this->laravel['files']->exists("{$path}/{$dir}")) {
            $this->laravel['files']->makeDirectory("{$path}/{$dir}");
        }

        $mediaName = $this->prepareFileName($media->name, $media->id);

        return "{$path}/{$dir}/{$mediaName}";
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
