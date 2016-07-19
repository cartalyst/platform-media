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

namespace Platform\Media\Styles\Macros;

use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ResizeMacro extends AbstractMacro implements MacroInterface
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

        $this->intervention = $app['image'];

        $this->filesystem = $app['files'];

        $this->presets = $app['config']->get('platform-media.presets');
    }

    /**
     * {@inheritDoc}
     */
    public function up(Media $media, File $file, UploadedFile $uploadedFile)
    {
        // Check if the file is an image
        if ($file->isImage()) {
            foreach ($this->presets as $name => $info) {
                $path = $this->getPath($file, $media, $name);

                // Create the image
                $this->intervention->make($file->getContents())
                    ->fit(array_get($info, 'width'), array_get($info, 'height'), function ($constraint) use ($info) {
                        foreach (array_get($info, 'constraints', []) as $_constraint) {
                            $constraint->{$_constraint}();
                        }
                    })->save($path);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down(Media $media, File $file)
    {
        if ($file->isImage()) {
            foreach ($this->presets as $name => $info) {
                $path = $this->getPath($file, $media, $name);

                $this->filesystem->delete($path);
            }
        }
    }

    /**
     * Returns the prepared file path.
     *
     * @param  \Cartalyst\Filesystem\File  $file
     * @param  \Platform\Media\Models\Media  $media
     * @param  string  $style
     * @return string
     */
    protected function getPath(File $file, Media $media, $style)
    {
        if (! $this->filesystem->exists("{$this->style->path}/{$style}")) {
            $this->filesystem->makeDirectory("{$this->style->path}/{$style}");
        }

        return "{$this->style->path}/{$style}/{$file->getFilename()}.{$file->getExtension()}";
    }
}
