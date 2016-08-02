{{-- Queue assets --}}
{{ Asset::queue('media', 'platform/media::css/media.scss', 'style') }}
{{ Asset::queue('selectize', 'selectize/css/selectize.bootstrap3.css', 'style') }}

{{ Asset::queue('fileapi', 'platform/media::js/FileAPI/FileAPI.min.js', 'jquery') }}
{{ Asset::queue('fileexif', 'platform/media::js/FileAPI/FileAPI.exif.js', 'fileapi') }}

{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'jquery') }}
{{ Asset::queue('exoskeleton', 'cartalyst/js/exoskeleton.min.js', 'jquery') }}
{{ Asset::queue('lodash', 'cartalyst/js/lodash.min.js', 'jquery') }}

{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}
{{ Asset::queue('mediamanager', 'platform/media::js/mediamanager.js', ['fileapi', 'data-grid']) }}
{{ Asset::queue('sortable', 'platform/media::js/sortable.min.js') }}
{{ Asset::queue('upload', 'platform/media::js/upload.js', ['platform', 'mediamanager', 'sortable', 'data-grid']) }}

{{-- Inline scripts --}}
@section('scripts')
@parent

<script type="text/javascript">
    Extension.Uploader.MediaManager.setUploadUrl('{{ route('admin.media.upload') }}');
    Extension.Uploader.MediaManager.setNamespace('{{ $namespace }}');
    Extension.Uploader.setMultiUpload('{{ $multiUpload }}');
</script>

@stop

<div class="media-upload-widget">

    @if ($model)
    <input type="hidden" data-model-id="{{ $model->id }}">
    <input type="hidden" data-object-class="{{ get_class($model) }}">
    @endif

    <input type="hidden" data-upload-post-url="{{ route('admin.media.link_media') }}">

    <ul class="nav nav-default pull-right">

        <a class="tip btn btn-default" href="#" data-toggle="modal" data-target="#media-modal" data-original-title="{{ trans('action.upload') }}">
            <i class="fa fa-upload"></i> {{ trans('action.upload') }}
        </a>

        <a class="tip btn btn-default" href="#" data-toggle="modal" data-target="#media-selection-modal" data-original-title="{{ trans('platform/media::action.select') }}">
            <i class="fa fa-list-ul"></i> {{ trans('platform/media::action.select') }}
        </a>

    </ul>

    <p class="lead">{{ trans('platform/media::widget.delete_info') }} {{ $multiUpload ? trans('platform/media::widget.multi_upload') : trans('platform/media::widget.single_upload') }}</p>

    <hr>

    <input type="hidden" id="mediaArray">
    <input type="hidden" name="selected_media[]">

    <!-- List group -->
    <ul id="mediaList" class="upload__attachments list-group">

        @foreach ($currentUploads as $upload)

        <li class="list-group-item clearfix" id="attached_media_{{ $upload->id }}">

            <div class="flex-row">

                <div class="list-group-item-left">

                    <i class="fa fa-arrows"></i>

                    @if ($upload->is_image == 1)
                    <div class="selected-media-img" style="background-image: url('@mediaPath($upload->id)')"></div>
                    @elseif ($upload->mime == 'text/plain')
                    <div class="selected-media-img" style="background-image: url('{{ Asset::getUrl('platform/media::img/txt.png') }}')"></div>
                    @elseif ($upload->mime == 'application/pdf')
                    <div class="selected-media-img" style="background-image: url('{{ Asset::getUrl('platform/media::img/pdf.png') }}')"></div>
                    @else
                    <div class="selected-media-img" style="background-image: url('{{ Asset::getUrl('platform/media::img/other.png') }}')"></div>
                    @endif

                </div>

                <div class="list-group-item-center">
                    <span>{{ $upload->name }}</span>
                    <input type="hidden" name="_media_ids[]" value="{{ $upload->id }}">
                </div>

                <div class="list-group-item-right">
                    <a href="{{ route('admin.media.edit', [ $upload->id ]) }}" class="btn btn-sm btn-default"><i class="fa fa-fw fa-pencil"></i></a>
                    <button type="button" class="btn btn-sm btn-danger" data-media-delete><i class="fa fa-fw fa-trash"></i></button>
                </div>

            </div>

            <div class="overlay">
                <i class="fa fa-spinner fa-spin"></i>
            </div>

        </li>

        @endforeach

</ul>

</div>

@include('platform/media::modal')
@include('platform/media::selection-modal')

<script type="text/template" data-media-attachment-template>

    <li class="list-group-item clearfix" id="attached_media_<%= media.id %>">

        <div class="flex-row">

            <div class="list-group-item-left">
                <i class="fa fa-arrows"></i>
                <% if (media.is_image == 1) { %>
                <div class="selected-media-img" style="background-image: url('<%= media.preset_paths.thumb %>')"></div>
                <% } else if (media.mime == 'text/plain') { %>
                <div class="selected-media-img" style="background-image:url('{{ Asset::getUrl('platform/media::img/txt.png') }}')"></div>
                <% } else if (media.mime == 'application/pdf') { %>
                <div class="selected-media-img" style="background-image:url('{{ Asset::getUrl('platform/media::img/pdf.png') }}')"></div>
                <% } else { %>
                <div class="selected-media-img" style="background-image:url('{{ Asset::getUrl('platform/media::img/other.png') }}')"></div>
                <% } %>
            </div>

            <div class="list-group-item-center">
                <span><%- media.name %></span>
                <input type="hidden" name="_media_ids[]" value="<%= media.id %>">
            </div>

            <div class="list-group-item-right">
                <a href="<%- '{{ route('admin.media.edit', [ 'id' ]) }}'.replace('id', media.id) %>" class="btn btn-sm btn-default"><i class="fa fa-fw fa-pencil"></i></a>

                <button type="button" class="btn btn-sm btn-danger" data-media-delete><i class="fa fa-fw fa-trash"></i></button>
            </div>

        </div>

        <div class="overlay">
            <i class="fa fa-spinner fa-spin"></i>
        </div>

    </li>

</script>
