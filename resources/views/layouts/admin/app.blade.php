<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8"/>
    <title>Chat UI - 4 Columns</title>
            @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
                @vite(['resources/css/app.css', 'resources/js/app.js'])
            @endif
    {{--    <script src="https://cdn.tailwindcss.com"></script>--}}
    {{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>--}}
    <title>{{ $title ?? 'Page Title' }}</title>
</head>

<body class="bg-gray-100 flex items-center justify-center">
{{$slot}}
</body>
</html>
