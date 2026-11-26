<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @php
        $apiUrl = (string) config('app.api_url');
        $apiUrlParts = parse_url($apiUrl);
        $apiHost = $apiUrlParts['host'] ?? parse_url('//'.$apiUrl, PHP_URL_HOST) ?? $apiUrl;
        $apiScheme = in_array($apiUrlParts['scheme'] ?? null, ['http', 'https'], true)
            ? $apiUrlParts['scheme']
            : request()->getScheme();
        $apiPort = $apiUrlParts['port'] ?? parse_url('//'.$apiUrl, PHP_URL_PORT);
        $apiOrigin = $apiScheme.'://'.$apiHost.($apiPort ? ':'.$apiPort : '');
    @endphp
    <meta name="api-base-url" content="{{ $apiOrigin }}" />
    <script>
        (() => {
            const preference = window.localStorage.getItem('admin-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            document.documentElement.classList.toggle(
                'dark',
                preference === 'dark' || (preference !== 'light' && prefersDark),
            );
        })();
    </script>
    @vite([
        'resources/js/admin/admin.ts',
        'resources/css/admin/admin.css',
    ])
    @inertiaHead
</head>
<body>
@inertia
</body>
</html>
