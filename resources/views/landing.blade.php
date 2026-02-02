<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasetemco Financial Management System</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <style>
        /* Reset */
        html, body {
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            background: #f8f9fa;
            color: #212529;
        }

        /* Page Background */
        .landing-page {
            background-image: url('{{ asset("images/background-desktop.png") }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }

        .container {
            max-width: 700px;
            margin: 150px auto 0 auto;
            padding: 40px;
            /* background: #fff; */
            /* border-radius: 8px; */
            /* box-shadow: 0 4px 12px rgba(0,0,0,0.08); */
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.15); /* Transparent white */
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px); /* Frosted glass effect */
            -webkit-backdrop-filter: blur(10px); /* Safari support */
            border: 1px solid rgba(255, 255, 255, 0.3);
            /* color: white; */
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
<body class="landing-page">

<div class="container">
    <h1>Tasetemco Coperative Financial Management System</h1>

    <p>
        This system is used for generating, reviewing, and printing
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
