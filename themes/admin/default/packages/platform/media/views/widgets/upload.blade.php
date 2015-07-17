
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
        Extension.Upload.MediaManager.setUploadUrl('{{ route('admin.media.upload_json') }}');
        Extension.Upload.field = '{{ $field }}';
        Extension.Upload.multiple = '{{ $multiupload }}';
    </script>
@stop

<div class="clearfix">
    <ul class="upload__attachments list-group">
    </ul>
    <a class="tip btn btn-primary btn-md" href="#" data-toggle="modal" data-target="#media-modal" data-original-title="{{{ trans('action.upload') }}}">
        <i class="fa fa-upload"></i> {{{ trans('action.upload') }}}
    </a>
</div>

@include('platform/media::modal')

<script type="text/template" data-media-attachment-template>
    <li class="list-group-item clearfix">
        <span class="pull-left">
            <%- media.name %>
            <input type="hidden" name="<%= field %>" value="<%= media.id %>">
        </span>
        <span class="pull-right button-group">
            <button type="button" class="btn btn-danger btn-xs media-delete"><i class="fa fa-trash"></i></button>
        </span>
    </li>
</script>