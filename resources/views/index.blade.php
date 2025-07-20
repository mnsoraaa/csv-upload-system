<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CSV Upload System') }} - File Upload</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold text-gray-900">
                        CSV File Upload System
                    </h1>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="space-y-8">
                <!-- Upload Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Upload CSV File</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Upload your CSV files for processing.
                        </p>
                    </div>
                    <div class="p-6">
                        <div data-vue-app>
                            <file-upload></file-upload>
                        </div>
                    </div>
                </div>

                <!-- File History Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Upload History</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            View the status and progress of your uploaded files in real-time.
                        </p>
                    </div>
                    <div class="p-6">
                        <div data-vue-app>
                            <file-history></file-history>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
