# Change Log

This project follows [Semantic Versioning](CONTRIBUTING.md).

## Proposals

We do not give estimated times for completion on `Accepted` Proposals.

- [Accepted](https://github.com/cartalyst/platform-media/labels/Accepted)
- [Rejected](https://github.com/cartalyst/platform-media/labels/Rejected)

---

### v4.0.0 - 2016-08-03

`UPDATED`

- Updated for Platform 5.
- Reworked Styles into Presets to allow more flexibility with Macros.
- Blade Widget calls were renamed to `@mediaPath` and `@mediaUpload`.

`REMOVED`

- `@thumbnail` Blade widget call, use `@mediaPath`.

### v3.3.1 - 2016-06-25

`FIXED`

- A js bug on the upload widget.

### v3.3.0 - 2016-06-24

`ADDED`

- Media Widget Upload.

### v3.2.2 - 2016-05-12

`FIXED`

- Fix delete issue when file doesn't exist on filesystem.

`ADDED`

- Ability to force the name to change when a file was uploaded.

### v3.2.1 - 2016-04-29

`FIXED`

- Issue when viewing private media files.

`UPDATED`

- Bumped Intervention Image version to `~2.3`.

### v3.2.0 - 2016-01-20

`REVISED`

- Only register routes if not cached by the app.

`UPDATED`

- Bumped `access`, `tags` extensions' version.

### v3.1.2 - 2016-05-12

`FIXED`

- Fix delete issue when file doesn't exist on filesystem.

`ADDED`

- Ability to force the name to change when a file was uploaded.

### v3.1.1 - 2016-04-29

`FIXED`

- Issue when viewing private media files.

`UPDATED`

- Only register routes if the routes are not cached.
- Bumped Intervention Image version to `~2.3`.

### v3.1.0 - 2015-07-24

`UPDATED`

- Bumped `access`, `tags` extensions' version.

### v3.0.2 - 2016-05-12

`FIXED`

- Fix delete issue when file doesn't exist on filesystem.

`ADDED`

- Ability to force the name to change when a file was uploaded.

### v3.0.1 - 2016-04-29

`FIXED`

- Issue when viewing private media files.

`UPDATED`

- Bumped Intervention Image version to `~2.3`.

### v3.0.0 - 2015-07-06

- Updated for Platform 4.

### v2.1.0 - 2015-07-20

`UPDATED`

- Bumped `access`, `tags` extensions' version.

### v2.0.2 - 2015-06-30

`UPDATES`

- Consistency tweaks.

### v2.0.1 - 2015-06-13

`ADDED`

- Media download cache.

`FIXED`

- Media emails.
- Bulk delete selector listener.

### v2.0.0 - 2015-03-05

- Updated for Platform 3.

### v1.1.0 - 2015-07-16

`UPDATED`

- Bumped `access`, `tags` extensions' version.

### v1.0.7 - 2015-06-30

`UPDATES`

- Consistency tweaks.

### v1.0.6 - 2015-06-13

`ADDED`

- Media download cache.

`FIXED`

- Bulk delete selector listener.

### v1.0.5 - 2015-02-17

`FIXED`

- Make database fields nullable on the migration to allow creating empty records.

### v1.0.4 - 2015-01-31

`ADDED`

- Drag and drop support.

`FIXED`

- Prevent file duplicates by appending the media id to the filename.
- Disable the Start Upload button after clicking it.

### v1.0.3 - 2015-01-28

`FIXED`

- Upload modal background glitch.

### v1.0.2 - 2015-01-28

`FIXED`

- Mass private/public actions.

### v1.0.1 - 2015-01-28

`FIXED`

- Fixes for latest Underscore version.

`UPDATED`

- Updated javascript files for consistency.

### v1.0.0 - 2015-01-26

- Can create, update, delete files.
- Can email files.
- Can set private/public.
- Can share files.
- Can download files.
- Has blade call `@media('id', 'download|thumbnail')`
- Has blade call `@thumbnail('id', [ 'options' ], 'default')`
- Can add tags.
- Can create config styles.
