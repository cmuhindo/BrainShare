<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrainShare - AI Powered eLearning Chatbot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
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
            background: url('<?php echo e(asset('images/bg.png')); ?>') no-repeat center center/cover;
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
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="<?php echo e(asset('images/bslogo.png')); ?>" alt="BrainShare Logo">
                <span>BrainShare</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('login')); ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo e(route('register')); ?>">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <div class="content-box">
                <h1>Welcome to BrainShare</h1>
                <p>AI Powered eLearning Chatbot. Interact with our AI ChatBot on WhatsApp/SMS +256753900500 or Generate a Course on any topic on the fly and start learning! Try it now</p>
                <a href="<?php echo e(url('/chat')); ?>" class="btn btn-success btn-lg">Ask A Question / Chat</a>
                <a href="https://brainshare.ai/generate-course" class="btn btn-primary btn-lg">Generate A Course</a>                
            </div>
        </div>
    </header>

    <section id="features" class="features container">
        <div class="row">
            <div class="col-md-4 feature">
                <i class="fas fa-brain"></i>
                <h3>AI-Powered</h3>
                <p>Harness the power of AI to enhance your learning experience.</p>
            </div>
            <div class="col-md-4 feature">
                <i class="fas fa-comments"></i>
                <h3>Interactive Chatbot</h3>
                <p>Engage with our smart chatbot for a seamless learning journey.</p>
            </div>
            <div class="col-md-4 feature">
                <i class="fas fa-laptop-code"></i>
                <h3>Accessible Anywhere</h3>
                <p>Learn from anywhere, at any time, on any device.</p>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2>Get Started with BrainShare Today</h2>
            <p>Join the future of learning with our AI-powered chatbot.</p>
            <a href="#" class="btn btn-primary btn-lg">Sign Up Now</a>
        </div>
    </section>

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
<?php /**PATH /var/www/brainshare_ai/resources/views/welcome.blade.php ENDPATH**/ ?>