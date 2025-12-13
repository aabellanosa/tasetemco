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

    <style>
        .table-container {
            max-height: 400px; /* Set a maximum height for the scrollable area */
            overflow-y: auto; /* Enable vertical scrolling */
        }

        .sticky-header th {
            position: sticky;
            top: 0; /* Stick to the top of the .table-container */
            background-color: #f8f9fa; /* Add a background color to prevent content from showing through */
            z-index: 10; /* Ensure the header is above other table content */
            /* Add box-shadow for better visual separation */
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }
    </style>    
    @yield('styles')
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
