<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrainShare - AI Powered eLearning Chatbot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Common Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Navbar Styles */
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

        /* Chat Styles */
        #chat-container {
            flex: 1;
            width: 100%;
            max-width: 600px;
            margin: auto;
            border: 1px solid #ccc;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        #chat-window {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
            border-bottom: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 10px;
            max-width: 75%;
        }

        .user-message {
            text-align: right;
            background-color: #e1f5fe;
            margin-left: auto;
        }

        .ai-message {
            text-align: left;
            background-color: #ffebee;
        }

        .typing-indicator {
            font-style: italic;
            color: #999;
        }

        #chat-form {
            display: flex;
            padding: 10px;
            background-color: #fff;
        }

        #chat-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }

        #chat-submit {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #chat-submit:hover {
            background-color: #0056b3;
        }

        /* Footer Styles */
        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        .footer ul {
            padding: 0;
            list-style: none;
        }

        .footer ul li {
            display: inline;
            margin: 0 10px;
        }

        .footer ul li a {
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="https://brainshare.ai">
                <img src="<?php echo e(asset('images/bslogo.png')); ?>" alt="BrainShare Logo">
                <span>BrainShare</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="chat-container">
        <div id="chat-window">
            <!-- Chat messages will appear here -->
        </div>
        <form id="chat-form">
            <input type="text" id="chat-input" placeholder="Type your question here..." required>
            <button type="submit" id="chat-submit">Send</button>
        </form>
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

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            $('#chat-form').on('submit', function(e) {
                e.preventDefault();

                var message = $('#chat-input').val();

                // Display the user's message in the chat window
                $('#chat-window').append('<div class="message user-message">' + message + '</div>');
                $('#chat-input').val(''); // Clear the input field

                // Scroll to the bottom of the chat window
                $('#chat-window').scrollTop($('#chat-window')[0].scrollHeight);

                // Show typing animation
                var typingIndicator = $('<div class="message typing-indicator">BrainShare AI is typing...</div>');
                $('#chat-window').append(typingIndicator);
                $('#chat-window').scrollTop($('#chat-window')[0].scrollHeight);

                // Function to format the content based on the logic provided
                function formatCourseContent(details) {
                    let lines = details.split("\n");
                    let formattedContent = "";
                    let currentSection = "";

                    lines.forEach(line => {
                        let trimmedLine = line.trim();

                        if (/^## (.+)/.test(trimmedLine)) {
                            formattedContent += "</div>"; // Close previous section
                            formattedContent += "<div class='section'>";
                            formattedContent += `<h2>${trimmedLine.substring(3)}</h2>`;
                        } else if (/^### (.+)/.test(trimmedLine)) {
                            formattedContent += `<h3>${trimmedLine.substring(4)}</h3>`;
                        } else if (/^\*\*(.+?)\*\*/.test(trimmedLine)) {
                            let formattedLine = trimmedLine.replace(/\*\*(.+?)\*\*/, '<strong>$1</strong>');
                            formattedContent += `<p>${formattedLine}</p>`;
                        } else if (/^\*\s(.+)/.test(trimmedLine)) {
                            formattedContent += `<ul><li>${trimmedLine.substring(2)}</li></ul>`;
                        } else if (/^(\d+)\.\s(.+)/.test(trimmedLine)) {
                            formattedContent += `<ol><li>${trimmedLine.substring(trimmedLine.indexOf(' ') + 1)}</li></ol>`;
                        } else {
                            formattedContent += `<p>${trimmedLine}</p>`;
                        }
                    });

                    formattedContent += "</div>"; // Close the last section
                    return formattedContent;
                }

                // Send the message to the server
                $.ajax({
                    url: '/chat/send',
                    type: 'POST',
                    data: {
                        message: message,
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        // Remove typing animation
                        typingIndicator.remove();

                        if (response.status === 'success') {
                            // Format and display the AI's response in the chat window
                            var formattedResponse = formatCourseContent(response.response);
                            $('#chat-window').append('<div class="message ai-message">' + formattedResponse + '</div>');
                        } else {
                            // Handle error case
                            $('#chat-window').append('<div class="message ai-message">Error: ' + response.message + '</div>');
                        }

                        // Scroll to the bottom of the chat window
                        $('#chat-window').scrollTop($('#chat-window')[0].scrollHeight);
                    },
                    error: function() {
                        // Handle server error
                        typingIndicator.remove();
                        $('#chat-window').append('<div class="message ai-message">Server error. Please try again later.</div>');
                        $('#chat-window').scrollTop($('#chat-window')[0].scrollHeight);
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php /**PATH /var/www/brainshare_ai/resources/views/chat.blade.php ENDPATH**/ ?>