<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Course Generator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
        background-color: #20c997; /* Green background or any other color */
        color: white; /* White text for better visibility */
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

    .sidebar h4 {
        font-weight: 600;
        margin-bottom: 30px;
        padding-left: 20px;
        position: relative;
        color: var(--accent-color);
    }

    .main-content {
        flex: 1;
        margin-left: 250px;
        padding-top: 160px; /* Adjusted for fixed top bar */
        padding-right: 20px;
        padding-bottom: 2rem;
        background-color: #f8fafc;
        overflow-y: auto;
        height: 100vh;
        position: relative;
    }

    /* Fix the top bar */
    .top-bar {
        position: fixed;
        top: 0;
        left: 250px; /* Adjusted for the sidebar width */
        right: 0;
        background-color: white;
        z-index: 10;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        padding: 40px 40px;
    }

    .course-card {
        border: 2px solid skyblue;
        border-radius: 12px;
        padding: 20px;
        background: white;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        font-weight: 500;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
    }

    .alert-info {
        background-color: #e3fafc;
        border-color: #99e9f2;
        color: #0c8599;
        border-radius: 8px;
    }
    </style>
</head>
<body>
    <div class="d-flex">
        <nav class="sidebar">
            <h4 class="d-flex align-items-center" style="padding-bottom: 20px; padding-left: 20px;">
                <img src="{{ asset('images/bslogo.png') }}" alt="BrainShare Logo" style="width: 35px; height: auto; margin-right: 10px;">
                <span style="color: white;">BrainShare</span>
            </h4>

            <a href="https://brainshare.ai/dashboard" class="active"><i class="fas fa-book mr-2"></i> My Courses</a>
            <a href="#"><i class="fas fa-user mr-2"></i> Profile</a>
            <a href="#"><i class="fas fa-cog mr-2"></i> Settings</a>
        </nav>
        
        <main class="col-md-12 main-content">
            <!-- Top bar content -->
            <div class="top-bar">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="bg-white p-3 rounded shadow-sm">
                        <h2 class="mb-0 text-primary">Welcome {{ Auth::user()->first_name }} {{ Auth::user()->last_name }} 
                            <span class="text-accent">({{ Auth::user()->academic_level }})</span>
                        </h2>
                    </div>                          
                    <a href="{{ route('generate-course') }}" class="btn btn-primary px-4 py-2">
                        <i class="fas fa-plus mr-2"></i> New Course
                    </a>
                </div>
            </div>

            <!-- Course content -->
            <div class="container">
                <div class="row mt-4">
                    @forelse($courses as $course)
                        <div class="col-md-4 mb-4">
                            <div class="card course-card color-{{ ($loop->index % 4) + 1 }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title mb-0 course-title">{{ $course->course_title }}</h5>
                                        <span class="badge custom-badge">{{ $course->class }}</span>
                                    </div>
                                    <p class="card-text text-muted">
                                        {{ \Illuminate\Support\Str::limit($course->course_description, 120) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <form action="{{ route('view-course-details') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                                <i class="fas fa-book-open mr-2"></i> Continue
                                            </button>
                                        </form>
                                        <small class="text-muted">
                                            Created {{ $course->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                No courses generated yet. Start by creating your first AI-powered course!
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
