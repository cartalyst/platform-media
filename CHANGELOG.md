# Media Change Log

This project follows [Semantic Versioning](CONTRIBUTING.md).

## Proposals

We do not give estimated times for completion on `Accepted` Proposals.

- [Accepted](https://github.com/cartalyst/platform-media/labels/Accepted)
- [Rejected](https://github.com/cartalyst/platform-media/labels/Rejected)

---

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
