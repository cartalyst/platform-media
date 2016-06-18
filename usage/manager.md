### Manager

The media manager makes integrating media with your extension a breeze. It allows you to attach media to your entities. The following example is going to explain how to use the manager for an `Employee`.

#### Model setup

The model must use `Platform\Media\Support\MediaTrait` and implement `Cartalyst\Support\Contracts\NamespacedEntityInterface`

##### Example

```php
<?php
namespace Platform\Employees\Models;

use Platform\Media\Support\MediaTrait;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Support\Traits\NamespacedEntityTrait;
use Cartalyst\Support\Contracts\NamespacedEntityInterface;

class Employee extends Model implements NamespacedEntityInterface
{
    use NamespacedEntityTrait, MediaTrait;

    protected $fillable = [
      ...
    ];

    protected static $entityNamespace = 'platform/employees';

    ...
}

```

> **Note**
>
> You must use the model's fillable property when using the media widget.
>
> The `$entityNamespace` of your model is used by the media when uploading images.

#### View setup

On the view, a simple blade call is needed that requires an instance of the model in question to be passed in as first argument, an optional second argument indicates whether the widget should allow attaching a single or multiple media objects, multiple is the default.

Third argument is optional and can be a view that would override the default widget view that ships with the extension.

##### Example

###### Allow only a single image to be attached to the model

```
@mediaUploader($employee, false)
```

###### Allow multiple images to be attached to the model

```
@mediaUploader($employee)
```

###### Use a custom view for the media widget

```
@mediaUploader($employee, true, 'yourvendor/yourextension::widgets.upload')
```

###### Use the media manager in a Platform Extension / Model Form

```php
@extends('layouts/default')

{{-- Page content --}}
@section('page')

<section class="panel panel-default panel-tabs">

	{{-- Form --}}
	<form id="employees-form" action="{{ request()->fullUrl() }}" role="form" method="post">

    {{-- Form fields --}}
		...

		<div class="row">

			@mediaUploader($employee)

		</div>

	</form>

</section>
@stop
```

#### Upload media

1. Click on **Upload**
2. Drop your files on the uploadable area
3. **Start Upload**

![upload-media](https://cloud.githubusercontent.com/assets/3426944/16172857/6a9333a2-3591-11e6-909e-7ada257c15a8.gif)

> **Note**
>
> The files are going to be attached to your entity.

#### Select media
You can attach media to your entity by using the Media Manager Selector.

1. Click **Select**
2. Select your files
3. **Select** to attach the selected media to your entityNamespace

![select-media](https://cloud.githubusercontent.com/assets/3426944/16172856/6a928164-3591-11e6-9c92-07096990a20c.gif)

> **Note**
>
> You can select/unselect files by clicking on the **Selected** Collapse or by clicking again on the selected media. Furthermore you can search your media library or narrow your library down by applying mime-type filters.

#### Sort media
The media manager is built with sorting in mind. Just drag the media by clicking and dragging the arrows icon.

![drag-media](https://cloud.githubusercontent.com/assets/3426944/16172855/6a91c15c-3591-11e6-8474-a843d1b59087.gif)

#### Detach media
Detach a media by clicking on the trash Icon. The File is not going to be entirely deleted, only the relation to the entity. If you want to delete a media permanently you can use the **Media Extension**.

![detach-media](https://cloud.githubusercontent.com/assets/3426944/16172854/6a90e2be-3591-11e6-9f56-42030d704725.gif)
