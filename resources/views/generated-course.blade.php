<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->course_title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #6c757d;
            --accent-color: #20c997;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        .custom-badge {
            background-color: #20c997;
            color: white;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 12px;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #357ab7 100%, #357ab7 100%);
            color: white;
            padding: 20px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar h4 {
            font-weight: 600;
            margin-bottom: 30px;
            padding-left: 20px;
            color: var(--accent-color);
            position: relative;
        }

        .sidebar .logo {
            margin-bottom: 30px;
        }

        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            margin-bottom: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar a:hover, .sidebar a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 10px 20px 20px 20px; /* Adjust padding for spacing between the sidebar and content */
            background-color: #f8fafc;
            overflow-y: auto;
            height: 100vh;
            position: relative;
        }

        .course-header {
            background-color: #4a90e2;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .course-section {
            margin: 20px 0;
        }

        .topic-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }

        

        .subtopic {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            width: 100%;
        }

        .subtopic span {
            flex: 1; /* Allows the text to grow while keeping space for the button */
            white-space: nowrap; /* Prevents wrapping to a new line */
            overflow: hidden; /* Ensures content doesn't overflow */
            text-overflow: ellipsis; /* Adds ... when text overflows */
            min-width: 0; /* Prevents flex from breaking ellipsis behavior */
        }

        .btn-animation {
            flex-shrink: 0; /* Prevents the button from shrinking */
        }

        .btn-animation {
            display: inline-block;
            padding: 5px 15px;
            font-size: 0.875rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-animation:hover {
            background-color: #0056b3;
        }

        .btn-animation.processing {
            background-color: #f0ad4e;
        }

        .btn-animation.complete {
            background-color: #28a745;
        }

        .generate-quiz-container {
            text-align: right;
            margin-top: 15px;
        }
        /* Updated back button styles */
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            border: none;
        }

        .back-button:hover {
            background-color: #4a90e2;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .back-button i {
            color: #1a73e8; /* Darker blue for better contrast */
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .back-button:hover i {
            color: white;
        }

        .course-header {
            position: relative;
        }

    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
            <h4 class="d-flex align-items-center" style="padding-bottom: 20px; padding-left: 20px;">
                <img src="{{ asset('images/bslogo.png') }}" alt="BrainShare Logo" style="width: 35px; height: auto; margin-right: 10px;">
                <span style="color: white;">BrainShare</span>
            </h4>
            <a href="https://brainshare.ai/dashboard" class="active"><i class="fas fa-book mr-2"></i> My Courses</a>
            <a href="#"><i class="fas fa-user mr-2"></i> Profile</a>
            <a href="#"><i class="fas fa-cog mr-2"></i> Settings</a>
        </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="course-header">
            <!-- Add the back button here -->
            <a class="back-button" href="{{ url()->previous() }}">
                <i class="fas fa-arrow-left"></i>
            </a>

            <h1>{{ $courseContent['courseTitle'] }}</h1>
            <p>{{ $courseContent['courseDescription'] }}</p>
            <span class="badge bg-secondary">{{ $courseContent['class'] }}</span>
        </div>

        <div class="course-section">
            @foreach ($courseContent['topics'] as $topic)
                <div class="mb-4">
                    <div class="topic-title">{{ $topic['title'] }}</div>
                    <ul>
                        @foreach ($topic['subtopics'] as $subtopic)
                            <li class="subtopic">
                                <span>{{ $subtopic }}</span>
                                @if (in_array($subtopic, $subtopicsWithContent))
                                    <button class="btn btn-success btn-animation" data-subtopic="{{ $subtopic }}" onclick="viewContent('{{ $subtopic }}', {{ $course->id }})">View Content</button>
                                @else
                                    <button class="btn btn-primary btn-animation" data-subtopic="{{ $subtopic }}" onclick="generateContent('{{ $subtopic }}', this, {{ $course->id }})">Generate Content</button>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <div class="generate-quiz-container">
                        <button class="btn btn-animation" onclick="generateQuiz('{{ $topic['title'] }}', this, {{ $course->id }})">Generate Topical Quiz</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const subtopicsWithContent = @json($subtopicsWithContent);

            subtopicsWithContent.forEach(subtopic => {
                const button = document.querySelector(`[data-subtopic="${subtopic}"]`);
                if (button) {
                    button.classList.add('complete', 'btn-success');
                    button.innerHTML = 'View Content';
                    button.onclick = function () {
                        viewContent(subtopic, {{ $course->id }});
                    };
                }
            });
        });

        function generateContent(subtopic, button, courseId) {
            button.classList.add('processing');
            button.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Generating...`;

            fetch('/generate-subtopic-content', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ subtopic: subtopic, course_id: courseId })
            })
            .then(response => response.json())
            .then(data => {
                button.classList.remove('processing');
                if (data.success) {
                    button.classList.add('complete', 'btn-success');
                    button.innerHTML = 'View Content';
                    button.onclick = function() {
                        viewContent(subtopic, courseId);
                    };
                } else {
                    button.innerHTML = 'Generate Content';
                    alert(data.message);
                }
            })
            .catch(error => {
                button.classList.remove('processing');
                button.innerHTML = 'Generate Content';
                console.error("Fetch error:", error);
            });
        }

        function viewContent(subtopic, courseId) {
            window.location.href = `/view-content?subtopic=${encodeURIComponent(subtopic)}&course_id=${courseId}`;
        }

        function generateQuiz(topic, quizButton, courseId) {
            quizButton.classList.add('processing');
            quizButton.innerHTML = `<span class="spinner-border" role="status" aria-hidden="true"></span> Generating...`;

            fetch('/generate-quiz', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ topic: topic, course_id: courseId })
            })
            .then(response => response.json())
            .then(data => {
                quizButton.classList.remove('processing');
                if (data.success) {
                    quizButton.classList.add('complete');
                    quizButton.innerHTML = 'View Quiz';
                    quizButton.onclick = function() {
                        viewQuiz(topic, courseId);
                    };
                } else {
                    quizButton.innerHTML = 'Generate Quiz';
                    alert(data.message);
                }
            })
            .catch(error => {
                quizButton.classList.remove('processing');
                quizButton.innerHTML = 'Generate Quiz';
                console.error("Fetch error:", error);
            });
        }

        function viewQuiz(topic, courseId) {
            window.location.href = `/view-quiz?topic=${encodeURIComponent(topic)}&course_id=${courseId}`;
        }
    </script>
</body>
</html>
