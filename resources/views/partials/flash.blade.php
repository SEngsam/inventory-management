@foreach (['message' => 'success', 'success' => 'success', 'error' => 'danger'] as $key => $type)
    @if (session()->has($key))
        <div class="alert alert-{{ $type }}">{{ session($key) }}</div>
    @endif
@endforeach
