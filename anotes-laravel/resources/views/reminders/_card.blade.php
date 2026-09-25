<article class="reminder-card {{ $reminder->is_done ? 'is-completed' : '' }}">
    <form action="{{ route('reminders.toggle', $reminder) }}" method="POST" class="reminder-check-form">
        @csrf
        @method('PATCH')
        <button type="submit" class="reminder-check" aria-label="{{ $reminder->is_done ? 'Mark incomplete' : 'Mark complete' }}: {{ $reminder->title }}">
            <i class="bi {{ $reminder->is_done ? 'bi-check-lg' : 'bi-circle' }}" aria-hidden="true"></i>
        </button>
    </form>
    <div class="reminder-card-main">
        <div class="reminder-card-heading"><h4>{{ $reminder->title }}</h4><span class="reminder-status {{ $reminder->is_done ? 'status-complete' : 'status-pending' }}">{{ $reminder->is_done ? 'Completed' : 'Pending' }}</span></div>
        @if ($reminder->description)
            <p class="reminder-description">{{ $reminder->description }}</p>
        @endif
        <div class="reminder-meta"><span><i class="bi bi-calendar3 me-1"></i>{{ is_object($reminder->remind_date) ? $reminder->remind_date->format('M j, Y') : $reminder->remind_date }}</span><span><i class="bi bi-clock me-1"></i>{{ substr((string) $reminder->remind_time, 0, 5) }}</span></div>
    </div>
    <div class="reminder-card-side">
        @if (($reminder->repeat_option ?? 'none') !== 'none')
            <span class="repeat-badge"><i class="bi bi-arrow-repeat me-1"></i>{{ ucfirst($reminder->repeat_option) }}@if ($reminder->repeat_option === 'custom' && $reminder->custom_days) · {{ $reminder->custom_days }}d @endif</span>
        @endif
        <div class="reminder-actions"><a href="{{ route('reminders.edit', $reminder) }}" class="icon-button" title="Edit reminder" aria-label="Edit {{ $reminder->title }}"><i class="bi bi-pencil"></i></a><form action="{{ route('reminders.destroy', $reminder) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this reminder?');">@csrf @method('DELETE')<button type="submit" class="icon-button danger" title="Delete reminder" aria-label="Delete {{ $reminder->title }}"><i class="bi bi-trash3"></i></button></form></div>
    </div>
</article>
