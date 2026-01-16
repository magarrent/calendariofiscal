<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Calendario Fiscal') }} - Calendario Fiscal 2026</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxStyles
</head>
<body class="min-h-screen bg-gray-50 antialiased dark:bg-gray-900">
    <livewire:calendar.calendar-view />
    @fluxScripts
</body>
</html>
