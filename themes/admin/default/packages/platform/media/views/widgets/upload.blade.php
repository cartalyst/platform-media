
{{-- Queue assets --}}
{{ Asset::queue('media', 'platform/media::css/media.scss', 'style') }}
{{ Asset::queue('selectize', 'selectize/css/selectize.bootstrap3.css', 'style') }}

{{ Asset::queue('fileapi', 'platform/media::js/FileAPI/FileAPI.min.js', 'jquery') }}
{{ Asset::queue('fileexif', 'platform/media::js/FileAPI/FileAPI.exif.js', 'fileapi') }}

{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}
{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery') }}
{{ Asset::queue('mediamanager', 'platform/media::js/mediamanager.js', ['fileapi', 'underscore']) }}
{{ Asset::queue('sortable', 'platform/media::js/sortable.min.js') }}
{{ Asset::queue('upload', 'platform/media::js/upload.js', ['platform', 'mediamanager', 'sortable']) }}

{{-- Inline scripts --}}
@section('scripts')
@parent

<script type="text/javascript">
    Extension.Uploader.MediaManager.setUploadUrl('{{ route('admin.media.upload') }}');
    Extension.Uploader.MediaManager.setNamespace('{{ $namespace }}');
    Extension.Uploader.setMultiUpload('{{ $multiUpload }}');
</script>

@stop

@if ($model)
<input type="hidden" data-model-id="{{ $model->id }}">
<input type="hidden" data-object-class="{{ get_class($model) }}">
@endif


<input type="hidden" data-upload-post-url="{{ route('admin.media.link_media') }}">

<div class="clearfix">
    <div class="pull-right mb15">
            <a class="tip btn btn-primary btn-md" href="#" data-toggle="modal" data-target="#media-modal" data-original-title="{{ trans('action.upload') }}">
                <i class="fa fa-upload"></i> {{ trans('action.upload') }}
            </a>

            <a class="tip btn btn-primary btn-md" href="#" data-toggle="modal" data-target="#media-selection-modal" data-original-title="{{ trans('platform/media::action.select') }}">
                <i class="fa fa-list-ul"></i> {{ trans('platform/media::action.select') }}
            </a>
    </div>
    <label class="mt5"><i class="fa fa-info"></i> Media</label>
</div>

<div class="clearfix">
    <input type="hidden" id="mediaArray">
    <ul id="mediaList" class="upload__attachments list-group">
        @foreach ($currentUploads as $upload)
        <li class="list-group-item clearfix" id="attached_media_{{ $upload->id }}">

            <div class="flex-row">
                <div class="list-group-item-left">
                    <i class="fa fa-arrows"></i>
                    @thumbnail($upload->id)
                </div>
                <div class="list-group-item-center">
                    <span>{{ $upload->name }}</span>
                    <input type="hidden" name="_media_ids[]" value="{{ $upload->id }}">
                </div>
                <div class="list-group-item-right">
                    <button type="button" class="btn btn-danger btn-xs" data-media-delete><i class="fa fa-trash"></i></button>
                </div>
            </div>
            <div class="overlay">
                <i class="fa fa-spinner fa-spin"></i>
            </div>

        </li>
        @endforeach
    </ul>

    <p><small><i class="fa fa-info"></i>&nbsp; Deleting an image will unlink it from this record, the image itself remains available under the media extension.</small></p>

    <p><small><i class="fa fa-info"></i>&nbsp; {{ $multiUpload ? 'Multiple images can be added.' : 'Only a single image can be added.' }}</small></p>

</div>

@include('platform/media::modal')
@include('platform/media::selection-modal')

<script type="text/template" data-media-attachment-template>
    <li class="list-group-item clearfix" id="<%= media.id %>">
        <div class="flex-row">
            <div class="list-group-item-left">
                <i class="fa fa-arrows"></i>
                <img src="<%= media.thumbnail %>" alt=""/>
            </div>
            <div class="list-group-item-center">
                <span><%- media.name %></span>
                <input type="hidden" name="_media_ids[]" value="<%= media.id %>">
            </div>
            <div class="list-group-item-right">
                <button type="button" class="btn btn-danger btn-xs" data-media-delete><i class="fa fa-trash"></i></button>
            </div>
        </div>
        <div class="overlay">
            <i class="fa fa-spinner fa-spin"></i>
        </div>
    </li>
</script>
