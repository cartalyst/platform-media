### Image Manipulation

If you have the need to present images in different sizes or even to add simple watermarks, presets and macros will surely help you.

As an example, you can have product images in different sizes while maintaining the source image clean.

These presets will be triggered when a file is uploaded and each preset will run depending on the "filters" you apply to each preset like only run on certain mime types or even namespaces.

#### Macros

A macro is what will perform the image manipulation behind the scenes.

##### Create a macro

We'll create a very basic macro:

```php
<?php

namespace App\Macros;

use Platform\Media\Macros\Fit;
use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Illuminate\Container\Container;

class Crop extends Fit
{
    public function up(Media $media, File $file)
    {
        if (! $file->isImage()) {
            return;
        }

        $preset = $this->getPreset();

        $path = $this->getPath($file, $media);

        $this->intervention->make($file->getContents())
            ->crop($preset->width, $preset->height)->save($path)
        ;
    }
}
```

> **Note:** In this example we're extending the default Fit macro, so the example is more cleaner and simple!

> **Note:** Every macro needs to extend the `Platform\Media\Macros\AbstractMacro` or to implement the `Platform\Media\Macros\MacroInterface` interface if more customization is required.

Now that we have the macro created, we need to register it and to register we have two ways, through the config file or at runtime, either one is fine.

**Config**

Open the `config/platform-media.php` config file and add the preset to the `macros` array, here's an example:

```php
'macros' => [

    'crop' => 'App\Macros\Crop',

],
```

**Runtime**

Somewhere on your application you can register the macro by running the following:

```php
$manager = app('platform.media.manager');

$manager->setMacro('crop', 'App\Macros\Crop');
```

#### Presets

A preset is mostly used to define the width and height using one or multiple macros.

##### Create a preset

Presets are however very easy and extremely simple to create/define and can be done either through the config or at runtime.

**Config**

Open the `config/platform-media.php` config file and add the preset to the `presets` array, here's an example:

```php
'presets' => [

    'mini' => [
        'width'  => 80,
        'macros' => [ 'fit' ],
        'mimes'  => [ 'image/jpeg' ],
    ],

],
```

> **Note:** Please refer to the list of allowed keys below.

**Runtime**

Somewhere on your application you can register the preset by running the following:

```php
$manager = app('platform.media.manager');

$manager->setPreset('mini', [
    'width'  => 80,
    'macros' => [ 'fit' ],
    'mimes'  => [ 'image/jpeg' ],
]);
```

> **Note:** Please refer to the list of allowed keys below.

##### Allowed Keys on Presets by Default

Here's a list of allowed keys that can be used on each preset, but you're free to pass any key for your custom macros.

<table class="table table-bordered">
    <thead>
        <tr>
            <td>Parameter</td>
            <td>Type</td>
            <td>Default</td>
            <td>Required</td>
            <td>Description</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>width</td>
            <td>integer</td>
            <td>null</td>
            <td>yes</td>
            <td>The width of the image.</td>
        </tr>
        <tr>
            <td>height</td>
            <td>integer</td>
            <td>null</td>
            <td>no</td>
            <td>The height of the image.</td>
        </tr>
        <tr>
            <td>path</td>
            <td>integer</td>
            <td>null</td>
            <td>no</td>
            <td>The path where to store this image.</td>
        </tr>
        <tr>
            <td>mimes</td>
            <td>array</td>
            <td>null</td>
            <td>no</td>
            <td>The valid mime types.</td>
        </tr>
        <tr>
            <td>macros</td>
            <td>array</td>
            <td>null</td>
            <td>no</td>
            <td>The macros that will run with this preset.</td>
        </tr>
        <tr>
            <td>constraints</td>
            <td>array</td>
            <td>null</td>
            <td>no</td>
            <td>The constraints to be applied on this preset.</td>
        </tr>
        <tr>
            <td>namespaces</td>
            <td>array</td>
            <td>null</td>
            <td>no</td>
            <td>The namespaces that are only allowed for this preset to run.</td>
        </tr>
    </tbody>
</table>
