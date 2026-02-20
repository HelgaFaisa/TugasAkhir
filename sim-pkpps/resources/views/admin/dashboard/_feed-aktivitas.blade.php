{{-- Feed Aktivitas Terbaru --}}
<div class="content-section">
    <h3><i class="fas fa-rss"></i> Aktivitas Terbaru</h3>
    <div class="content-box">
        @if($feed->isEmpty())
            <p class="text-muted">Belum ada aktivitas tercatat.</p>
        @else
            <ul class="feed-list">
                @foreach($feed as $item)
                <li class="feed-item">
                    <span class="feed-icon feed-icon-{{ $item->color }}">
                        <i class="fas {{ $item->icon }}"></i>
                    </span>
                    <div class="feed-body">
                        <p>{{ $item->text }}</p>
                        <small class="text-muted">{{ $item->time->diffForHumans() }}</small>
                    </div>
                </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
