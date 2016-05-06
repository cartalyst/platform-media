### Repository

#### IoC Binding

The media repository is bound to `platform.media` and can be resolved out of the IoC Container using that offset.

```php
$media = app('platform.media');
```

#### Methods

The repository contains several methods that are used throughout the extension, most common methods are listed below.

For an exhaustive list of available methods, checkout the `MediaRepositoryInterface`

- find($id);

Returns an media object based on the given id.

- findByPath($path);

Returns a collection of all media.

- upload($file, array $input)

Uploads the given file and populates the data passed as input.

- create(array $data);

Creates and stores a new media object.

- update($id, array $data);

Updates an existing media object.

- delete($id);

Deletes a media object.
