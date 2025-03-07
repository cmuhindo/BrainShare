<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrainShare - Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        html, body {
            height: 100%;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Ensure space between navbar and footer */
        }

        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar {
            background-color: #007bff;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            max-height: 40px;
            margin-right: 10px;
        }

        .navbar-brand span {
            font-size: 1.8rem;
            margin-bottom: 0;
            line-height: 40px;
        }

        .navbar-brand, .navbar-nav .nav-link {
            color: #fff;
        }

        .hero {
            height: 100vh;
            background: url('{{ asset('images/bg.png') }}') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
            padding: 0 20px;
            position: relative;
        }

        .hero .content-box {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
        }

        .hero h1 {
            font-size: 4rem;
        }

        .hero p {
            font-size: 1.5rem;
        }

        .hero .btn {
            margin: 5px;
        }

        .features {
            padding: 50px 0;
        }

        .features .feature {
            padding: 20px;
            text-align: center;
        }

        .features .feature i {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .cta {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 50px 20px;
        }

        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-top: auto;
        }

        .form-container {
            width: 500px; /* Maintain fixed width */
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        button {
            width: 100%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('images/bslogo.png') }}" alt="BrainShare Logo">
                <span>BrainShare</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('studentlogin') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('studentregister') }}">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5 main-content">
        <div class="form-container">
            <form method="POST" action="{{ route('studentlogin') }}">
                
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                @foreach ($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @csrf
            <div class="form-group">
                <label for="username_or_email">Username or Email</label>
                <input type="text" name="username_or_email" class="form-control" required>
                @error('username_or_email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Login</button>
        </form>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 BrainShare. All rights reserved.</p>
            <ul class="list-inline">
                <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                <li class="list-inline-item"><a href="#">Terms of Service</a></li>
                <li class="list-inline-item"><a href="#">Contact Us</a></li>
            </ul>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
