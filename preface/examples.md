### Examples

The `$media` variable used below is a reference to the MediaRepository.

```php
$media = app('platform.media');
```

###### Retrieve all media.

```php
$media = $media->find(1);
```

###### Dynamically create a new employee.

```php
// $file must be an instance of `Symfony\Component\HttpFoundation\File\UploadedFile`

$media->upload($file, [
    'name'  => 'Foobar',
]);
```
