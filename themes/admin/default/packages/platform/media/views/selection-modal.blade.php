{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'jquery') }}
{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery') }}
{{ Asset::queue('moment', 'moment/js/moment.js', 'jquery') }}

<div class="modal modal-media-selection fade" id="media-selection-modal" tabindex="-1" role="dialog" aria-labelledby="media-selection-modal" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-body">

                <div class="input-group" data-search data-grid="main">

                    <span class="input-group-btn">

                        <button class="btn btn-default" type="button" disabled>
                            {{{ trans('common.filters') }}}
                        </button>

                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>

                        <ul class="dropdown-menu" role="menu">

                            <li>
                                <a data-grid="main" data-filter="private:0" data-label="private::{{{ trans('platform/media::action.filter.public') }}}" data-reset>
                                    <i class="fa fa-unlock"></i> {{{ trans('platform/media::action.filter.public') }}}
                                </a>
                            </li>

                            <li>
                                <a data-grid="main" data-filter="private:1" data-label="private::{{{ trans('platform/media::action.filter.private') }}}" data-reset>
                                    <i class="fa fa-lock"></i> {{{ trans('platform/media::action.filter.private') }}}
                                </a>
                            </li>

                            <li class="divider"></li>

                        </ul>

                    </span>

                    <input class="form-control" type="text" placeholder="{{{ trans('common.search') }}}">

                    <span class="input-group-btn">

                        <button class="btn btn-default" type="submit">
                            <span class="fa fa-search"></span>
                        </button>

                        <button class="btn btn-default" data-grid="main" data-reset>
                            <i class="fa fa-refresh fa-sm"></i>
                        </button>

                    </span>

                </div>

                {{-- Grid: Applied Filters --}}
                <div class="btn-toolbar" role="toolbar" aria-label="data-grid-applied-filters">

                    <div id="data-grid_applied" class="btn-group" data-grid="main"></div>

                </div>

                {{-- Grid: Table --}}
                <div class="table-responsive">

                    <table id="data-grid" class="table table-hover" data-source="{{ route('admin.media.images.grid') }}" data-grid="main">
                        <thead>
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
                        <tbody></tbody>
                    </table>

                </div>

                <footer class="panel-footer clearfix">

                    {{-- Grid: Pagination --}}
                    <div id="data-grid_pagination" data-grid="main"></div>

                </footer>

            </div>

            <div class="modal-footer">

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
