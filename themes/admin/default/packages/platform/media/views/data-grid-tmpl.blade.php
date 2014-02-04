<script type="text/template" data-grid="main" id="data-grid-tmpl">

	<% _.each(results, function(r) { %>

		<div class="col-6 col-sm-6 col-lg-4">

			<div class="media">

				<div class="media__type">
					<div class="media__type--<%= r.extension %>">
						<% if (r.is_image == 1) { %>
							<img src="{{ URL::to(media_cache_path('<%= r.thumbnail %>')) }}" />
						<% } %>
					</div>
				</div>

				<div class="media__mask" data-media="<%= r.id %>">

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
					</ul>

				</div>

				<label class="media__select">
					<input id="media_<%= r.id %>" name="media"  type="checkbox" value="<%= r.id %>">
				</label>

				<div class="media__status media__status--private <%= r.private == 1 ? 'media__status--private' : null %>"></div>

			</div>

		</div>

	<% }); %>

</script>



