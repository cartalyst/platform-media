<div class="modal modal-media fade" id="media-modal" tabindex="-1" role="dialog" aria-labelledby="media-modal" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-body upload">

                <div class="upload__instructions">
                    <div class="dnd"></div>

                    <i class="fa fa-upload fa-5x"></i>
                    <h4>Select Files</h4>
                    <p class="lead">Acceptable File Types.</p>
                    <p class="small">
                        <i>
                            {{ $mimes }}
                        </i>
                    </p>

                </div>

                <div class="upload__files" data-media-queue-list ></div>

                <div class="btn btn-default btn-block upload__select">
                    <div>Select</div>
                    <input name="files" class="upload__select-input" type="file"
                       @if (! isset($multiupload) || $multiupload)
                       multiple
                       @endif
                    />
                </div>

            </div>

            <div class="modal-footer">

				<span class="pull-left text-left">
					<div><span data-media-total-files>0</span> files in queue</div>
					<div><span data-media-total-size>0</span> KB</div>
				</span>

				<span class="pull-right text-right">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{{ trans('action.cancel') }}}</button>

					<button type="button" class="btn btn-primary" data-media-upload><i class="fa fa-upload"></i> Start Upload</button>
				</span>
            </div>

        </div>

    </div>

</div>

<script type="text/template" data-media-file-template>
    <div data-media-file="<%= FileAPI.uid(file) %>" class="file file_<%= file.type.split('/')[0] %>">

        <form class="form-inline">

            <div class="form-group">

                <div class="btn-group">
                    <button class="btn btn-default file-type" disabled><i class="fa <%= icon[file.type.split('/')[0]]||icon.def %>"></i></button>
                    <button class="btn btn-default file-size" disabled><small><%= (file.size/FileAPI.KB).toFixed(2) %> kb</small></button>
                </div>

            </div>

            <div class="form-group">
                <label class="sr-only" for="label">Filename</label>
                <input type="text" class="form-control file-name" name="<%= FileAPI.uid(file) %>_name" value="<%= file.name %>" placeholder="Filename" >
            </div>

            <div class="form-group">
                <label class="sr-only" for="tags">Tags</label>
                <input type="text" class="form-control file-tags" name="<%= FileAPI.uid(file) %>_tags[]" value="" placeholder="Tags">
            </div>

            <div class="form-group">

                <button class="btn btn-default file-remove" data-media-remove="<%= FileAPI.uid(file) %>"><i class="fa fa-trash-o"></i></button>

                <button class="btn btn-default file-status" disabled>

					<span class="file-ready">
						<i class="fa fa-clock-o"></i>
					</span>

					<span class="file-progress">
						<i class="fa fa-refresh fa-spin"></i>
					</span>

					<span class="file-success">
						<i class="fa fa-thumbs-o-up text-success"></i>
					</span>

					<span class="file-error" data-toggle="tooltip" data-title>
						<i class="fa fa-exclamation text-danger"></i>
					</span>

                </button>

            </div>

        </form>

        <div class="file-error-help text-danger"></div>

    </div>
</script>