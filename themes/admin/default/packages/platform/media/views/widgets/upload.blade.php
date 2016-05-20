
{{-- Queue assets --}}
{{ Asset::queue('media', 'platform/media::css/media.scss', 'style') }}
{{ Asset::queue('selectize', 'selectize/css/selectize.bootstrap3.css', 'style') }}

{{ Asset::queue('fileapi', 'platform/media::js/FileAPI/FileAPI.min.js', 'jquery') }}
{{ Asset::queue('fileexif', 'platform/media::js/FileAPI/FileAPI.exif.js', 'fileapi') }}

{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}
{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery') }}
{{ Asset::queue('mediamanager', 'platform/media::js/mediamanager.js', ['fileapi', 'underscore']) }}
{{ Asset::queue('upload', 'platform/media::js/upload.js', ['platform', 'mediamanager']) }}

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

<style>
    div.overlay {
        display: none;
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
    }

    div.overlay i.fa-spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        margin-left: -7px;
        margin-top: -7px;
    }
</style>

<input type="hidden" data-upload-post-url="{{ route('admin.media.link_media') }}">

<div class="clearfix">
    <ul class="upload__attachments list-group">
        @foreach ($currentUploads as $upload)
        <li class="list-group-item clearfix">
            <div class="flex-row">
                <div class="list-group-item-left">
                    <i class="fa fa-arrows-v"></i>
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

    <a class="tip btn btn-primary btn-md" href="#" data-toggle="modal" data-target="#media-modal" data-original-title="{{ trans('action.upload') }}">
        <i class="fa fa-upload"></i> {{ trans('action.upload') }}
    </a>

    <a class="tip btn btn-primary btn-md" href="#" data-toggle="modal" data-target="#media-selection-modal" data-original-title="{{ trans('action.select') }}">
        <i class="fa fa-file"></i> {{ trans('action.select') }}
    </a>
</div>

@include('platform/media::modal')
@include('platform/media::selection-modal')

<script type="text/template" data-media-attachment-template>
    <li class="list-group-item clearfix">
        <div class="overlay">
            <i class="fa fa-spinner fa-spin"></i>
        </div>
        <span class="pull-left">
            <%- media.name %>
            <input type="hidden" name="_media_ids[]" value="<%= media.id %>">
            <img src="{{ url('/') }}<%= media.thumbnail %>" alt=""/>
        </span>
        <span class="pull-right button-group">
            <button type="button" class="btn btn-danger btn-xs" data-media-delete><i class="fa fa-trash"></i></button>
        </span>
    </li>
</script>
