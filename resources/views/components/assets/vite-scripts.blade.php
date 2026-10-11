@props(['js' => [], 'defer' => true, 'async' => false])

{!! vite_preload($js) !!}

@foreach($js as $file)
    <script src="{{ vite_asset($file) }}" 
        @if($defer) defer @endif
        @if($async) async @endif>
    </script>
@endforeach