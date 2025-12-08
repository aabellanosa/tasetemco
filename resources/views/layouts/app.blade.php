<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Financial Statement</title>

    {{-- Bootstrap 5 CDN --}}
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >

</head>
<body class="bg-light">

    {{-- Top Navigation --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                Financial Position
            </a>

            <div class="ms-auto text-white">
                {{ date('F Y') }}
            </div>
        </div>
    </nav>

    {{-- Main Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Bootstrap JS (optional but recommended for components) --}}
    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    
    @yield('scripts')

</body>
</html>
