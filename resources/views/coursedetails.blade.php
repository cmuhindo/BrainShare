<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $courseContent['courseTitle'] }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #6c757d;
            --accent-color: #20c997;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #357ab7;
            color: white;
            padding: 20px;
            position: fixed;
            left: 0;
            overflow-y: auto;
        }

        .sidebar h4 {
            padding-left: 20px;
            color: var(--accent-color);
        }

        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            transition: all 0.3s;
        }

        .sidebar a:hover, .sidebar a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            background-color: #f8fafc;
        }

        .course-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: white;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .back-button:hover {
            background-color: var(--primary-color);
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
        }

        .btn-animation {
            padding: 5px 15px;
            font-size: 0.875rem;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-animation:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <nav class="sidebar">
        <h4>
            <img src="{{ asset('images/bslogo.png') }}" alt="BrainShare Logo" style="width: 35px; margin-right: 10px;">
            BrainShare
        </h4>
        <a href="https://brainshare.ai/dashboard" class="active">My Courses</a>
        <a href="#">Profile</a>
        <a href="#">Settings</a>
    </nav>

    <div class="main-content">
        <div class="course-header">
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
                                    <button class="btn btn-success btn-animation" onclick="viewContent('{{ $subtopic }}', {{ $course->id }})">View Content</button>
                                @else
                                    <button class="btn btn-primary btn-animation" onclick="generateContent('{{ $subtopic }}', this, {{ $course->id }})">Generate Content</button>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <div class="text-end">
                        <button class="btn btn-animation" onclick="generateQuiz('{{ $topic['title'] }}', this, {{ $course->id }})">Generate Topical Quiz</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
