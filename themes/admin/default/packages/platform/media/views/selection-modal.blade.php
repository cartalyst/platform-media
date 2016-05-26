{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'jquery') }}
{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery') }}
{{ Asset::queue('moment', 'moment/js/moment.js', 'jquery') }}

<div class="modal modal-media-selection fade" id="media-selection-modal" tabindex="-1" role="dialog" aria-labelledby="media-selection-modal" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">
            
            <div class="modal-header">
                
                <div class="modal-header-left">
                    <a href="#" data-toggle="tooltip" data-original-title="Show all files" class="modal-header-icon"><i class="fa fa-th-large"></i></a>
                    <a href="#" data-toggle="tooltip" data-original-title="Show only images" class="modal-header-icon"><i class="fa fa-th-large"></i></a>
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

                {{-- Grid: Table --}}

                {{-- <div class="media-results">
                    <i class="fa fa-check"></i>
                    <div class="media-item">
                        <input id="media-item-1" data-grid-checkbox type="checkbox" name="row[]" value="<%= r.id %>">
                        <label for="media-item-1">
                            <div class="media-img"></div>
                            <span class="media-title">Screenshot.png</span>
                            <span class="media-date">15 May, 2016</span>
                        </label>
                    </div>

                </div> --}}
                <div>
                    <div class="media-results display-column" id="data-grid" data-source="{{ route('admin.media.images.grid') }}" data-grid="main">
                        
                        <!-- <thead>
                            <tr>
                                <th><input data-grid-checkbox="all" type="checkbox"></th>
                                <th class="sortable" data-sort="mime"><i class="fa fa-file-o"></i></th>
                                <th class="sortable" data-sort="mime"><i class="fa fa-shield"></i></th>
                                <th class="sortable" data-sort="name">{{{ trans('model.name') }}}</th>
                                <th>{{{ trans('platform/tags::model.tag.legend') }}}</th>
                                <th class="sortable" data-sort="size">{{{ trans('platform/media::model.general.size') }}}</th>
                                <th class="sortable hidden-xs" data-sort="created_at">{{{ trans('model.created_at') }}}</th>
                                <th class="text-center">{{{ trans('common.actions') }}}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>-->
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

<script type="text/template" data-media-file-template>

</script>
