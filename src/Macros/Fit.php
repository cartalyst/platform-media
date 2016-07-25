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

namespace Platform\Media\Macros;

use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Platform\Media\Styles\Preset;
use Illuminate\Container\Container;

class Fit extends AbstractMacro
{
    /**
     * The Illuminate Container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * The Filesystem instance.
     *
     * @var \Cartalyst\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * The Intervention Image Manager instance.
     *
     * @var \Intervention\Image\ImageManager
     */
    protected $intervention;

    /**
     * The image presets.
     *
     * @var array
     */
    protected $presets;

    /**
     * Constructor.
     *
     * @param  \Illuminate\Container\Container  $app
     * @return void
     */
    public function __construct(Container $app)
    {
        $this->app = $app;

        $this->filesystem = $app['files'];

        $this->intervention = $app['image'];

        $this->presets = $app['config']->get('platform-media.presets');
    }

    /**
     * {@inheritdoc}
     */
    public function up(Media $media, File $file)
    {
        if (! $file->isImage()) {
            return;
        }

        $preset = $this->getPreset();

        $path = $this->getPath($file, $media);

        $this->intervention->make($file->getContents())
            ->fit($preset->width, $preset->height, function ($constraint) use ($preset) {
                foreach ($preset->constraints as $_constraint) {
                    $constraint->{$_constraint}();
                }
            })->save($path)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function down(Media $media, File $file)
    {
        if (! $file->isImage()) {
            return;
        }

        #
        // foreach ($this->presets as $name => $info) {
        //     $path = $this->getPath($file, $media, $name);
        //
        //     $this->filesystem->delete($path);
        // }
    }

    /**
     * Returns the prepared file path.
     *
     * @param  \Cartalyst\Filesystem\File  $file
     * @param  \Platform\Media\Models\Media  $media
     * @return string
     */
    protected function getPath(File $file, Media $media)
    {
        $filesystem = $this->filesystem;

        $path = $this->getPreset()->path;

        if (! $filesystem->exists($path)) {
            $filesystem->makeDirectory($path);
        }

        return $path.'/'.$file->getFilename().'.'.$file->getExtension();
    }
}
