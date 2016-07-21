<script>
$(function () {
    var dg = new DataGridManager();

    var grid = dg.create('main', {
        source: '{{ route('admin.media.grid') }}',
        pagination: {
            threshold: '10',
            throttle: '10'
        },
        loader: {
            element: '.loading'
        }
    });
});
</script>
