@props(['css' => [], 'critical' => false])

@if($critical)
    @foreach($css as $file)
        {!! critical_css($file) !!}
    @endforeach
@endif

@foreach($css as $file)
    <link rel="stylesheet" href="{{ vite_asset($file) }}">
@endforeach