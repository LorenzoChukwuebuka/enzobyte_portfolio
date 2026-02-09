<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Enzobyte Admin</title>
    @vite('resources/js/app.ts')
    @vite('resources/css/app.css')
</head>

<body>
    <div id="app">
        <app></app>
    </div>
</body>

</html>
