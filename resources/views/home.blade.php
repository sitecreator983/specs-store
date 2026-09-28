<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>specs-store | PC parts and builds</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div id="app"></div>
<script>
    window.specsStoreAuthUser = {{ Illuminate\Support\Js::from(auth()->user()?->only(['id', 'name', 'email', 'is_admin'])) }};
</script>
</body>
</html>
