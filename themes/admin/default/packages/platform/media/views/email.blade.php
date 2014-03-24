@extends('layouts/default')


@section('content')

<h3>Attachments</h3>

@foreach ($items as $item)

<div>
	<img src="{{ URL::to(media_cache_path($item->thumbnail)) }}"> {{{ $item->name }}}
</div>
<br>

@endforeach

@stop
