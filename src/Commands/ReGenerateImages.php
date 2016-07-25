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
        $manager = app('platform.media.manager');

        $medias = $this->laravel['platform.media']->get();

        $filesystem = $this->laravel['cartalyst.filesystem'];

        $bar = $this->output->createProgressBar(count($medias));

        foreach ($medias as $media) {
            $file = $filesystem->get($media->path);

            $manager->applyPresets('up', $media, $file);

            $bar->advance();
        }

        $bar->finish();
    }
}
