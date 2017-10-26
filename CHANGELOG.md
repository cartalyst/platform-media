# Change Log

This project follows [Semantic Versioning](CONTRIBUTING.md).

## Proposals

We do not give estimated times for completion on `Accepted` Proposals.

- [Accepted](https://github.com/cartalyst/platform-media/labels/Accepted)
- [Rejected](https://github.com/cartalyst/platform-media/labels/Rejected)

---

### v6.0.8 - 2017-10-26

`FIXED`

- Listing method sorts.

### v6.0.7 - 2017-09-12

`FIXED`

- Redactor media manager.

### v6.0.6 - 2017-07-13

`FIXED`

- Redactor images list bug.
- Postgresql ordering bug.
- Issue with preset paths model attribute being triggered on non images.

### v6.0.5 - 2017-03-16

`FIXED`

- A bug on the media uploader widget.

### v6.0.4 - 2017-03-15

`FIXED`

- Data Grid filters.

### v6.0.3 - 2017-03-08

`FIXED`

- Sorting by status.

### v6.0.2 - 2017-03-08

`REVISED`

- Markup clean up.

### v6.0.1 - 2017-03-07

`FIXED`

- Media manager.
- Redactor upload.

### v6.0.0 - 2017-02-24

- Updated for Platform 7.

### v5.0.6 - 2017-07-13

`FIXED`

- Redactor images list bug.
- Postgresql ordering bug.
- Issue with preset paths model attribute being triggered on non images.

### v5.0.5 - 2017-03-16

`FIXED`

- A bug on the media uploader widget.

### v5.0.4 - 2017-03-15

`FIXED`

- Data Grid filters.

### v5.0.3 - 2017-03-08

`FIXED`

- Sorting by status.

### v5.0.2 - 2017-03-08

`REVISED`

- Markup clean up.

### v5.0.1 - 2017-03-07

`FIXED`

- Media manager.
- Redactor upload.

### v5.0.0 - 2017-02-24

- Updated for Platform 6.

### v4.0.5 - 2017-07-12

`FIXED`

- Issue with preset paths model attribute being triggered on non images.

### v4.0.4 - 2016-12-15

`FIXED`

- Redactor images list bug.
- Postgresql ordering bug.

### v4.0.3 - 2016-11-04

`FIXED`

- Redactor upload route name.
- Redactor upload permission.

### v4.0.2 - 2016-10-27

`UPDATED`

- Update index view to use the new Blade help widget.

### v4.0.1 - 2016-08-10

`FIXED`

- An issue where some users couldn't unlink media entries due to incorrect permissions.

### v4.0.0 - 2016-08-03

`UPDATED`

- Updated for Platform 5.
- Reworked Styles into Presets to allow more flexibility with Macros.
- Blade Widget calls were renamed to `@mediaPath` and `@mediaUpload`.

`REMOVED`

- `@thumbnail` Blade widget call, use `@mediaPath`.

### v3.3.3 - 2016-08-10

`FIXED`

- An issue where some users couldn't unlink media entries due to incorrect permissions.

### v3.3.2 - 2016-07-20

`FIXED`

- A bug preventing guarded from being used on the model.

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
