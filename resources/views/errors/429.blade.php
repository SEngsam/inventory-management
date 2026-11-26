@include('layouts.error', [
    'title' => __('Too many requests'),
    'message' => __('You are sending requests too quickly. Please wait a moment and try again.'),
    'image' => '429-error.svg',
])
