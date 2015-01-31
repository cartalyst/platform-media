# Media Change Log

This project follows [Semantic Versioning](CONTRIBUTING.md).

## Proposals

We do not give estimated times for completion on `Accepted` Proposals.

- [Accepted](https://github.com/cartalyst/platform-media/labels/Accepted)
- [Rejected](https://github.com/cartalyst/platform-media/labels/Rejected)

---

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
