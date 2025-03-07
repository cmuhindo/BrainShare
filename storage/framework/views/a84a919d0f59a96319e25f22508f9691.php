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

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #357ab7 100%, #357ab7 100%);
            color: white;
            padding: 20px;
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
        }

        .sidebar a:hover, .sidebar a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin-left: 250px;
        }

        .container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .course-card {
            border: 2px solid skyblue;
            border-radius: 12px;
            padding: 20px;
            background: white;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .top-bar {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            background-color: white;
            z-index: 10;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 40px 40px;
        }

        .top-bar .d-flex {
            padding-left: 10px; /* Adjust this value to reduce the space between the back button and welcome message */
            padding-right: 540px; /* Adjust this value to reduce the space between the back button and welcome message */
        }


        /* Back button styling */
        .back-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4a90e2;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 18px;
            margin-left: 10px;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <nav class="sidebar">
            <h4 class="d-flex align-items-center" style="padding-bottom: 42px; padding-left: 20px;">
                <img src="<?php echo e(asset('images/bslogo.png')); ?>" alt="BrainShare Logo" style="width: 35px; height: auto; margin-right: 10px;">
                <span style="color: white;">BrainShare</span>
            </h4>
            <a href="https://brainshare.ai/dashboard" class="active"><i class="fas fa-book mr-2"></i> My Courses</a>
            <a href="#"><i class="fas fa-user mr-2"></i> Profile</a>
            <a href="#"><i class="fas fa-cog mr-2"></i> Settings</a>
        </nav>
        
        <main class="col-md-12 main-content">
            <div class="top-bar">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Back Button -->
                    <div class="back-btn" onclick="window.history.back();">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    <div class="bg-white p-3 rounded shadow-sm">
                        <h2 class="mb-0 text-primary">Welcome <?php echo e(Auth::user()->first_name); ?> <?php echo e(Auth::user()->last_name); ?> 
                            <span class="text-accent">(<?php echo e(Auth::user()->academic_level); ?>)</span>
                        </h2>
                    </div>                          
                </div>
            </div>

            <div class="container d-flex justify-content-center align-items-center vh-100">
                <div class="row w-100">
                    <!-- Course Generator Form -->
                    <div class="col-md-6 d-flex justify-content-center">
                        <div class="card course-card">
                            <div class="card-body">
                                <h5 class="card-title mb-3 text-center">Generate a New Course</h5>
                                <form method="POST" action="/generate-course" id="courseForm">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-group mb-4">
                                        <label for="title">Course Title/Topic</label>
                                        <input type="text" id="title" name="title" class="form-control" 
                                               required placeholder="e.g., Introduction to Quantum Physics, Basic Python Programming...">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="academic_level">Academic Level</label>
                                        <input type="text" id="academic_level" name="class" class="form-control"
                                               value="<?php echo e(Auth::user()->academic_level); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100" id="generateButton">
                                        <span id="buttonText">Generate Course</span>
                                        <span id="spinner" class="spinner-border spinner-border-sm text-light ms-2 d-none" role="status"></span>
                                    </button>

                                    <script>
                                        document.getElementById("courseForm").addEventListener("submit", function() {
                                            let buttonText = document.getElementById("buttonText");
                                            let spinner = document.getElementById("spinner");
                                            let button = document.getElementById("generateButton");

                                            buttonText.textContent = "Generating Course Outline...";
                                            spinner.classList.remove("d-none"); // Show spinner
                                            button.disabled = true; // Disable button to prevent multiple submissions
                                        });
                                    </script>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions Card -->
                    <div class="col-md-6 d-flex justify-content-center">
                        <div class="card course-card">
                            <div class="card-body">
                                <h5 class="card-title mb-3 text-center">How to Generate a Course</h5>
                                <ul>
                                    <li>Enter the course title or topic in the input field.</li>
                                    <li>The academic level is pre-filled based on your profile.</li>
                                    <li>Click "Generate Course" to create structured course content.</li>
                                    <li>The AI will generate a well-organized outline for your course.</li>
                                    <li>Review and refine the generated content as needed.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH /var/www/brainshare_ai/resources/views/generate-course.blade.php ENDPATH**/ ?>