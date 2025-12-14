<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cooperative Financial System</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            background: #f8f9fa;
            color: #212529;
        }

        .container {
            max-width: 900px;
            margin: 80px auto;
            padding: 40px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        h1 {
            margin-top: 0;
            font-size: 28px;
        }

        p {
            line-height: 1.6;
        }

        .actions {
            margin-top: 30px;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-primary {
            background: #0d6efd;
            color: #fff;
        }

        .btn-outline {
            border: 1px solid #0d6efd;
            color: #0d6efd;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Cooperative Financial Statement System</h1>

    <p>
        This system is a proof-of-concept for generating, reviewing, and printing
        cooperative financial statements in a structured and auditable manner.
    </p>

    <p>
        Access to financial data is restricted to authorized users only.
    </p>

    <div class="actions">
        <a href="/login" class="btn btn-primary">Authorized Access</a>
    </div>
</div>

</body>
</html>
