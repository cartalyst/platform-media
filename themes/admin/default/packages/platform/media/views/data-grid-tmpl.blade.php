<script type="text/template" data-grid="main" id="data-grid-tmpl">

	<% _.each(results, function(r) { %>

		<div class="col-lg-3 col-md-4 col-xs-6">
			<div class="media" data-media-id="<%= r.id %>">

				<div class="media__type">
					<div class="media__type--<%= r.extension %>">
						<% if (r.is_image == 1) { %>
							<img src="{{ URL::to(media_cache_path('<%= r.thumbnail %>')) }}" />
						<% } %>
					</div>
				</div>

				<div class="media__mask">

					<h2><%= r.name %></h2>
					<p>@media('<%= r.id %>')</p>

					<ul class="media__actions">
						<li class="action">
							<a href="{{ URL::to('media/<%= r.path %>') }}" target="_blank">
								<i class="fa fa-link tip" data-placement="bottom" title="View"></i>
							</a>
						</li>
						<li class="action">
							<a href="{{ URL::to('media/download/<%= r.path %>') }}">
								<i class="fa fa-download tip" data-placement="bottom" title="Download"></i>
							</a>
						</li>
						<li class="action action--checkbox">
							<label>
								<input name="privacy" type="checkbox" value="">
								<i class="fa fa-lock tip" data-placement="bottom" title="Privacy"></i>
							</label>
						</li>
						<li class="action action--checkbox">
							<label>
								<input id="delete" type="checkbox" value="">
								<i class="fa fa-trash-o tip" data-placement="bottom" title="Delete"></i>
							</label>
						</li>
					</ul>

				</div>

				<div class="media__selected"></div>

			</div>
		</div>

	<% }); %>

</script>



