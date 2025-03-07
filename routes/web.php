<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseGeneratorController;
use App\Http\Controllers\Auth\CustomAuthController;
use App\Models\GeneratedCourse;



Route::get('/studentlogin', [CustomAuthController::class, 'showLoginForm'])->name('studentlogin');
Route::post('/studentlogin', [CustomAuthController::class, 'login']);

Route::get('/studentregister', [CustomAuthController::class, 'showRegistrationForm'])->name('studentregister');
Route::post('/studentregister', [CustomAuthController::class, 'register']);
Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('welcome');
});


// web.php
Route::get('/dashboard', function () {
    return view('dashboard', [
        'courses' => Auth::user()->generatedCourses()
                    ->orderBy('created_at', 'desc')
                    ->get(),
        'user' => Auth::user()
    ]);
})->middleware(['auth'])->name('dashboard');


// web.php
Route::get('/dashboard2', function () {
    return view('dashboard2', [
        'courses' => Auth::user()->generatedCourses()
                    ->orderBy('created_at', 'desc')
                    ->get(),
        'user' => Auth::user()
    ]);
})->middleware(['auth'])->name('dashboard2');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');




        // AI Course Generator
    // Route to show the course generation form
    Route::get('/generate-course', [CourseGeneratorController::class, 'showForm'])->name('generate-course');
    Route::post('/generate-course', [CourseGeneratorController::class, 'generateCourse']);

    // Route to view a generated course
    Route::get('/generated-course', [CourseGeneratorController::class, 'generatedCourse'])->name('view-course');
    Route::post('/generated-course', [CourseGeneratorController::class, 'generatedCourse']);
    //Route::get('/dashboard-generated-course', [CourseGeneratorController::class, 'viewDashboardGeneratedCourse'])->name('view-course-details');

    Route::get('/dashboard-generated-course', function () {
    return view('dashboard', [
            'courses' => Auth::user()->generatedCourses()
                        ->orderBy('created_at', 'desc')
                        ->get(),
            'user' => Auth::user()
        ]);
    })->middleware(['auth'])->name('dashboard');
    
    Route::post('/dashboard-generated-course', [CourseGeneratorController::class, 'viewDashboardGeneratedCourse'])->name('view-course-details');
    Route::get('/generated-course-details', [CourseGeneratorController::class, 'generatedCourseDetails']);
    Route::post('/generated-course-details', [CourseGeneratorController::class, 'generatedCourseDetails']);


    

    // Route to generate a course
    Route::post('/api/generate-course', [CourseGeneratorController::class, 'generateCourse']);

    // Route to view a specific generated course by ID
    Route::get('/generated-course/{courseId}', [CourseGeneratorController::class, 'viewGeneratedCourse']);

    // Route to generate content for a specific subtopic
    Route::post('/generate-subtopic-content', [CourseGeneratorController::class, 'generateSubtopicContent']);

    // Route to generate a quiz for a topic
    Route::post('/generate-quiz', [CourseGeneratorController::class, 'generateQuiz']);

    Route::get('/view-content', [CourseGeneratorController::class, 'showContentPage']);



});

// Route to generate text from a prompt using the Gemini API
Route::post('/api/generate-text', [CourseGeneratorController::class, 'generateTextFromPrompt']);



Route::view('/chat', 'chat')->name('chat');
Route::post('/chat/send', [CourseGeneratorController::class, 'sendMessage'])->name('chat.send');


require __DIR__.'/auth.php';

//Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
