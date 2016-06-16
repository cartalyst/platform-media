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

                <div class="modal-header-right" data-search data-grid="main">

                    <div>
                      <div class="input-group">
                          <span class="input-group-btn">

                              <button class="btn btn-default" type="button" disabled>
                                  {{{ trans('common.filters') }}}
                              </button>

                              <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                  <span class="caret"></span>
                                  <span class="sr-only">Toggle Dropdown</span>
                              </button>

                              <ul class="dropdown-menu" role="menu">

                                  @foreach(collect(app('platform.media')->all()->lists('mime'))->unique()->values()->all() as $media)
                                  <li><a href="#" data-filter="mime:{{ $media }}" data-grid="main">{{ $media }}</a></li>
                                  @endforeach

                              </ul>

                          </span>

                          <input class="form-control" name="filter" type="text" placeholder="{{{ trans('common.search') }}}">

                          <span class="input-group-btn">

                              <button class="btn btn-default" type="submit">
                                  <span class="fa fa-search"></span>
                              </button>

                              <button class="btn btn-default" data-grid="main" data-reset>
                                  <i class="fa fa-refresh fa-sm"></i>
                              </button>

                          </span>

                      </div>
                    </div>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

            </div>

            <div class="modal-selected">
                <a href="#" class="modal-selected-header">
                    <div class="flex-row">
                        <div class="flex">
                            <h3>Selected (<span class="selected-index">0</span>)</h3>
                        </div>
                        <i class="fa fa-chevron-down"></i>
                    </div>
                </a>

                <div class="modal-selected-body media-results">
                    <div class="no-results">
                        <p>No items selected.</p>
                    </div>
                </div>
            </div>

            <div class="modal-body">

                {{-- Grid: Applied Filters --}}
                <div class="btn-toolbar" role="toolbar" aria-label="data-grid-applied-filters">
                    <div id="data-grid_applied" class="btn-group" data-grid="main"></div>
                </div>

                <div>
                    <div class="media-results" id="data-grid" data-source="{{ route('admin.media.grid') }}" data-grid="main">
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
@include('platform/media::grid/manager/results')
@include('platform/media::grid/manager/pagination')
@include('platform/media::grid/manager/filters')
@include('platform/media::grid/manager/no_results')
