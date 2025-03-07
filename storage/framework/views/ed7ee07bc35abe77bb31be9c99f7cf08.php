<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: 20px;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        header h1 {
            font-size: 2.5em;
            margin: 0;
            color: #333;
        }
        .content {
            font-size: 1.2em;
            line-height: 1.6;
        }
        .details {
            margin-top: 20px;
            padding: 20px;
            background-color: #e9ecef;
            border-radius: 8px;
        }
        .details h2 {
            margin-top: 0;
            font-size: 1.5em;
        }
        .details h3 {
            margin-top: 0;
            font-size: 1.2em;
        }
        .details p {
            margin: 10px 0;
        }
        .section {
            margin-top: 20px;
        }
        footer {
            margin-top: 50px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Course Details</h1>
        </header>
        <div class="content">
            <div class="details">
                <div><?php echo $formattedDetails; ?></div>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 AI Course Generator. All rights reserved.</p>
    </footer>
</body>
</html>
<?php /**PATH /var/www/brainshare_ai/resources/views/coursedetails.blade.php ENDPATH**/ ?>