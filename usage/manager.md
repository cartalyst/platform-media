### Manager

The media manager makes integrating media with your extension a breeze. It allows you to attach media to your entities. The following example is going to explain how to use the manager for an `Employee`.

#### Model setup

The model must use `Platform\Media\Support\MediaTrait` and implement `Cartalyst\Support\Contracts\NamespacedEntityInterface`

##### Example

```
<?php

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

    protected static $entityNamespace = 'organization/employee';

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

```
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
