### Artisan Commands

#### `php artisan images:generate`

Generates images using the given criteria and presets.

##### Optional parameters

`--mime=:val` only apply for the given mime.
`--media=:val` only apply for the given media.
`--preset=:val` only apply for the given preset.
`--namespace=:val` only apply for the given namespace.

##### Examples

###### Generate media items for the `720p` preset

    php artisan images:generate --preset=720p

###### Generate media items for all items with a mime type of `image/jpeg`.

    php artisan images:generate --mime=image/jpeg

#### `php artisan images:clear`

Clears images using the given criteria and presets.

`--mime=:val` only apply for the given mime.
`--media=:val` only apply for the given media.
`--preset=:val` only apply for the given preset.
`--namespace=:val` only apply for the given namespace.

##### Examples

###### Clear all `thumb` preset media items

    php artisan images:clear --preset=thumb

###### Clear all media items for the `foo/bar` namespace

    php artisan images:clear --namespace=foo/bar
