<div class="card" style="--accent:{{ $accent ?? '#1b84ff' }}">
    <div class="card-head">
        <span class="eye" style="background:{{ $eyeBg ?? '#eef4ff' }}">{!! $eyeIcon !!}</span>
        <div class="card-title">{{ $item['name'] ?? '' }}</div>
    </div>
    @if(!empty($item['meta']))
        <div class="badges">
            @foreach($item['meta'] as $m)
                <span class="badge {{ ($m['key'] ?? '') === 'vacant' ? 'badge-danger' : '' }}">{{ $m['label'] ?? '' }}</span>
            @endforeach
        </div>
    @endif
    @if(!empty($item['manager']))
        <div class="manager">{{ $item['manager'] }}</div>
    @endif
    @if(!empty($item['vacant']))
        <div class="vacant">{{ $item['vacant'] }}</div>
    @endif
</div>
