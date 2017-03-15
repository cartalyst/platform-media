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

namespace Platform\Media\Commands;

use Illuminate\Console\Command;
use Platform\Media\Models\Media;
use Symfony\Component\Console\Helper\ProgressBar;

class ImagesClear extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'images:clear
                            {--mime= : Whether or not if we should filter by the given mime.}
                            {--media= : Whether or not if we should filter by the given media.}
                            {--preset= : Whether or not if we should filter by the given preset.}
                            {--namespace= : Whether or not if we should filter by the given namespace.}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Clears images using the given criteria and presets.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $mime = $this->option('mime');

        $media = $this->option('media');

        $preset = $this->option('preset');

        $namespace = $this->option('namespace');

        $manager = app('platform.media.manager');

        if ($preset && ! $manager->isValidPreset($preset)) {
            throw new \Exception('The given preset does not exist.');
        }

        $query = app('platform.media')->newQuery();

        if ($media) {
            $query->where(function ($query) use ($media) {
                $query
                    ->orWhere('id', $media)
                    ->orWhere('name', $media)
                ;
            });
        }

        if ($mime) {
            $query->where('mime', $mime);
        }

        if ($namespace) {
            $query->where('namespace', $namespace);
        }

        $medias = $query->get();

        if (count($medias) === 0) {
            throw new \Exception('No results found.');
        }

        $confirm = $this->confirm('This will permantently remove all previously generated images, do you wish to continue?');

        if ($confirm) {
            $bar = $this->output->createProgressBar(count($medias));

            foreach ($medias as $media) {
                if ($preset) {
                    $manager->applyPreset($preset, 'down', $media);
                } else {
                    $manager->applyPresets('down', $media);
                }

                $bar->advance();
            }

            $bar->finish();

            $this->info(' ');

            $this->info('Done!');
        }
    }
}
