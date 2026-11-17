<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
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
        'resources/js/review/review.ts',
        'resources/css/review.css',
    ])
    @inertiaHead
</head>
<body>
@inertia
</body>
</html>
