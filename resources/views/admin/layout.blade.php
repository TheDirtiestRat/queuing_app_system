<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body>
    {{-- layout --}}
    <div class="drawer lg:drawer-open">
        <input id="my-drawer-4" type="checkbox" class="drawer-toggle" />
        <div class="drawer-content p-2 ps-2 md:ps-0 w-full">
            @include('components.admin-nav-bar')

            <!-- Page content here -->
            <div class="p-3 rounded-2xl bg-base-300 mt-2 w-full">
                @yield('admin-content')
            </div>
        </div>

        @include('components.admin-side-bar')
    </div>

    {{-- alert modal --}}
    @include('components.alert-modal')
    @yield('admin-scripts')
</body>

</html>
