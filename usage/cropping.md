### Cropping Media
Often you would like to have uniform images attached to your entities. This example is going to show you how you could crop your images for a specific entity. As an example, we will continue to use an `Employee` model.

#### Setup the Media Style
I have created a new `Organization\Employee` Extension with `Platform's Workshop`, which simplifies the process of creating all the files and views significantly. This is an excellent starting point.

##### Organization\Employees\Styles\Macros\ImageMacro
We're going to create our own `ImageMacro` that is extending Platform's `AbstractMacro`. This file is saved under `workbench/organization/employees/src/Styles/Macros/ImageMacro`.

```
<?php
namespace Organization\Employees\Styles\Macros;

use Illuminate\Support\Str;
use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Illuminate\Container\Container;
use Platform\Media\Styles\Macros\AbstractMacro;
use Platform\Media\Styles\Macros\MacroInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageMacro extends AbstractMacro implements MacroInterface
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
     * Constructor.
     *
     * @param  \Illuminate\Container\Container $app
     *
     * @return void
     */
    public function __construct(Container $app)
    {
        $this->app = $app;

        $this->intervention = $app['image'];

        $this->filesystem = $app['cartalyst.filesystem'];
    }

    /**
     * @param Media $media
     * @param File  $file
     *
     * @return File
     * @internal param $cachedPath
     *
     */
    public function cropOriginalFile(Media $media, File $file)
    {
        $cachedPath = $this->style->path . '/' . str_random(10) . '.' . $file->getExtension();

        // Crop the Media
        app('image')->make($file->getContents())
                    ->fit($this->style->croppedImageWidth, $this->style->croppedImageHeight, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($cachedPath);

        // Upload the file
        app('cartalyst.filesystem')->update($media->path, file_get_contents($cachedPath));

        // Delete temporary File
        \Illuminate\Support\Facades\File::delete($cachedPath);
    }

    /**
     * {@inheritDoc}
     */
    public function down(Media $media, File $file)
    {
        $path = $this->getPath($file, $media);

        \Illuminate\Support\Facades\File::delete($path);
    }

    /**
     * {@inheritDoc}
     */
    public function up(Media $media, File $file, UploadedFile $uploadedFile)
    {
        // Check if the file is an image
        if ($file->isImage() && $media->namespace == 'organization/employees.employee') {
            $path = $this->getPath($file, $media);

            // Update the media entry
            $media->thumbnail = str_replace(public_path(), null, $path);
            $media->save();

            // Create the Thumbnail
            $this->intervention->make($file->getContents())
                 ->fit($this->style->width, $this->style->height, function ($constraint) {
                     $constraint->aspectRatio();
                 })->save($path);

            // Crop original File
            $this->cropOriginalFile($media, $file);
        }
    }

    /**
     * Returns the prepared file path.
     *
     * @param  \Cartalyst\Filesystem\File   $file
     * @param  \Platform\Media\Models\Media $media
     *
     * @return string
     */
    protected function getPath(File $file, Media $media)
    {
        $width  = $this->style->width;
        $height = $this->style->height;

        $name = Str::slug(implode([$file->getFilename(), $width, $height ?: $width], ' '));

        return "{$this->style->path}/{$media->id}_{$name}.{$file->getExtension()}";
    }
}

```

##### Service Provider

```
<?php
namespace Organization\Employees\Providers;

use Cartalyst\Support\ServiceProvider;

class EmployeeServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        // Register the attributes namespace
        $this->app['platform.attributes.manager']->registerNamespace(
            $this->app['Organization\Employees\Models\Employee']
        );

        // Subscribe the registered event handler
        $this->app['events']->subscribe('organization.employees.employee.handler.event');

        // Get the Media Manager
        $manager = app('platform.media.manager');

        // Set the Employee Image Style
        $manager->setStyle('EmployeeImage', function (Style $style) {
            // Set the style image height and width.
            $style->height = 1200;
            $style->width  = 1200;

            // Set the style image height and width for the cropped image
            $style->croppedImageHeight = 600;
            $style->croppedImageWidth  = 600;

            // Set the style macros
            $style->macros = ['EmployeeImageMacro'];

            // Set the storage path
            $style->path = public_path('cache/media');
        });

        // Set the Employee Image Macro
        $manager->setMacro('EmployeeImageMacro', 'Organization\Employees\Styles\Macros\ImageMacro');
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        // Register the repository
        $this->bindIf('organization.employees.employee', 'Organization\Employees\Repositories\Employee\EmployeeRepository');

        // Register the data handler
        $this->bindIf('organization.employees.employee.handler.data', 'Organization\Employees\Handlers\Employee\EmployeeDataHandler');

        // Register the event handler
        $this->bindIf('organization.employees.employee.handler.event', 'Organization\Employees\Handlers\Employee\EmployeeEventHandler');

        // Register the validator
        $this->bindIf('organization.employees.employee.validator', 'Organization\Employees\Validator\Employee\EmployeeValidator');
    }
}
```
