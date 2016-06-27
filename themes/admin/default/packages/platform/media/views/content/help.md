Manage media/files across your application. Features include role-based permissions to access selected media files.

---

### Blade Calls

`@media('id', 'type')`

This blade call allows you to retrieve the media uri, download uri or thumbnail uri.

	// Returns the media uri
	@media(1)

	// Returns the media download uri
	@media(1, 'download')

	// Returns the media thumbnail uri
	@media(1, 'thumbnail')

---

`@thumbnail('id', [ 'options' ], 'default')`

This blade call allows you to return media thumbnails as an html `img` element.

	// Returns the html img tag using the media thumbnail
	@thumbnail('1', ['class="foo"'], '/foo/bar/placeholder.png')

---

`@mediaUploader($model, 'multiupload' = true, 'view')`

This blade call allows you to use the media manager in your form/view and associate media to any model in your app.

	// Returns the media manager for any model
	@mediaUploader($model)

**Note:** Read our manual to set up your models in order to use the media manager.

---

### When should I use it?

When you need to handle media/file uploads.

---

### How can I use it?

Upload your media item, then simply return the media uri or element using one of the blade calls above.
