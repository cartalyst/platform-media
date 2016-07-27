Manage media/files across your application. Features include role-based permissions to access selected media files.

---

### Blade Calls

#### @mediaPath

This blade call allows you to retrieve the url to the original media file or to a preset url like a thumbnail.

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
            <td>$id</td>
            <td>integer</td>
            <td>null</td>
            <td>yes</td>
            <td>The id of the media object.</td>
        </tr>
        <tr>
            <td>$name</td>
            <td>string</td>
            <td>null</td>
            <td>no</td>
            <td>The name of the preset to use.</td>
        </tr>
        <tr>
            <td>$parameters</td>
            <td>array</td>
            <td>null</td>
            <td>no</td>
            <td>For a custom inline preset, some parameters can be passed like width, height or macros.</td>
        </tr>
    </tbody>
</table>

##### Usage

	// Returns the media url
	@mediaPath(1)

	// Returns the media url for the given preset
	@mediaPath(1, 'thumb')

	// Returns the media url for a custom inline preset
	@mediaPath(1, '80px', [ 'width' => 80, 'height' => 80, 'macros' => [ 'fit' ] ])

---

#### @mediaUploader

This blade call allows you to use the media manager in your form/view and associate media to any model in your app.

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
            <td>$namespace</td>
            <td>Cartalyst\Support\Contracts\NamespacedEntityInterface or string</td>
            <td>null</td>
            <td>yes</td>
            <td>The entity to be used.</td>
        </tr>
        <tr>
            <td>$multiUpload</td>
            <td>bool</td>
            <td>true</td>
            <td>no</td>
            <td>Should this support multiple uploads?</td>
        </tr>
        <tr>
            <td>$view</td>
            <td>string</td>
            <td>null</td>
            <td>no</td>
            <td>Pass the path to a custom view to be used instead of the default one.</td>
        </tr>
    </tbody>
</table>

##### Usage

	// Returns the media manager for any model
	@mediaUploader($model)

**Note:** Read our manual to set up your models in order to use the media manager.

##### F.A.Q.

###### Q: When should I use it?

###### A: When you need to handle media/file uploads.

----

###### Q: How can I use it?

###### A: Upload your media item, then simply return the media url or element using the @mediaPath Widget.
