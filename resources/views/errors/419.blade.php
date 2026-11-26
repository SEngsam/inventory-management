@include('layouts.error', [
    'title' => __('Page expired'),
    'message' => __('Your session has expired. Please refresh the page and try again.'),
    'image' => '503-error.svg',
])
