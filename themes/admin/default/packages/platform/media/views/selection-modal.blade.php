{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'jquery') }}
{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery') }}
{{ Asset::queue('moment', 'moment/js/moment.js', 'jquery') }}

<div class="modal modal-media-selection fade" id="media-selection-modal" tabindex="-1" role="dialog" aria-labelledby="media-selection-modal" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <div class="modal-header-left">
                    <a href="#" data-toggle="tooltip" data-original-title="Show all files" data-view="grid" class="modal-header-icon active"><i class="fa fa-th-large"></i></a>
                    <a href="#" data-toggle="tooltip" data-original-title="Show only images" data-view="list" class="modal-header-icon"><i class="fa fa-th-list"></i></a>
                </div>

                <div class="modal-header-center">
                    <h4 class="modal-title">Media Manager</h4>
                </div>

                <div class="modal-header-right">
                    <div class="modal-header-search" data-search data-grid="main">
                        <i class="fa fa-search"></i>
                        <input type="text" placeholder="{{{ trans('common.search') }}}">
                        <div class="modal-header-search-action">
                            <a href="#" class="btn btn-default" data-grid="main" data-reset>
                                <i class="fa fa-refresh"></i>
                            </a>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

            </div>

            <div class="modal-body">

                {{-- Grid: Applied Filters --}}
                <div class="btn-toolbar" role="toolbar" aria-label="data-grid-applied-filters">
                    <div id="data-grid_applied" class="btn-group" data-grid="main"></div>
                </div>

                <div>
                    <div class="media-results" id="data-grid" data-source="{{ route('admin.media.images.grid') }}" data-grid="main">

                    </div>

                </div>

            </div>

            <div class="modal-footer">
                {{-- Grid: Pagination --}}
                <div id="data-grid_pagination" data-grid="main"></div>

                <span class="pull-right text-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{{ trans('action.cancel') }}}</button>

                    <button type="button" class="btn btn-primary" data-media-add><i class="fa fa-upload"></i> Select</button>
                </span>
            </div>

        </div>

    </div>

</div>

{{-- Grid: templates --}}
@include('platform/media::grid/uploader/results')
@include('platform/media::grid/uploader/pagination')
@include('platform/media::grid/uploader/filters')
@include('platform/media::grid/uploader/no_results')
