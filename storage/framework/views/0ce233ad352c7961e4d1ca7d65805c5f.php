<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($subtopic); ?></title>
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

        .sidebar h4 {
            font-weight: 600;
            margin-bottom: 50px;
            padding-left: 20px;
            color: var(--accent-color);
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
            position: relative;
        }

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
            color: #1a73e8;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .back-button:hover i {
            color: white;
        }

        .content-container {
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .image-placeholder {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <h4 class="d-flex align-items-center">
            <img src="<?php echo e(asset('images/bslogo.png')); ?>" alt="BrainShare Logo" style="width: 35px; height: auto; margin-right: 10px;">
            <span style="color: white;">BrainShare</span>
        </h4>
        <a href="https://brainshare.ai/dashboard" class="active"><i class="fas fa-book mr-2"></i> My Courses</a>
        <a href="#"><i class="fas fa-user mr-2"></i> Profile</a>
        <a href="#"><i class="fas fa-cog mr-2"></i> Settings</a>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="course-header">
            <a class="back-button" href="<?php echo e(url()->previous()); ?>">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1><?php echo e($subtopic); ?></h1>
        </div>

        <div class="content-container">
            <?php echo $content; ?>

            <div class="image-placeholder">
                <p><strong>Placeholder:</strong> for visuals.</p>
            </div>
        </div>

        <div class="text-center">
            <form action="<?php echo e(url('/generated-course-details')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="courseId" value="<?php echo e($courseId); ?>">
                <button type="submit" class="btn btn-secondary">Back</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php /**PATH /var/www/brainshare_ai/resources/views/content/view.blade.php ENDPATH**/ ?>