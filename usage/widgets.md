### Widgets

#### `@mediaPath($id, $name, $attributes)`

##### Examples

###### Retrieve original file path

```php
@mediaPath(1)
```

###### Retrieve the thumb preset path

```php
@mediaPath(1, 'thumb')
```

###### Retrieve a non existing preset (created on demand)

By passing the attributes below for a non existing preset, an image will be created based on them and its path returned.

```php
@mediaPath(1, 'new_preset', ['width' => 100, 'height' => 100, 'macros' => [ 'fit' ]])
```

#### `@mediaUploader($namespace, $multiUpload, $view)`

Documented under the Manager section above.
