<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite([
        'resources/js/manage/manage.ts',
        'resources/sass/manage/manage.scss',
    ])
    @inertiaHead
</head>
<body>
@inertia
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
</body>
</html>
