<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\ContactController;

// Public API Routes
Route::prefix('v1')->group(function () {
    
    // Projects
    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('projects/featured', [ProjectController::class, 'featured']);
    Route::get('projects/{slug}', [ProjectController::class, 'show']);

    // Services
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{slug}', [ServiceController::class, 'show']);

    // Team
    Route::get('team', [TeamController::class, 'index']);
    Route::get('team/{id}', [TeamController::class, 'show']);

    // Blog
    Route::get('blog', [BlogController::class, 'index']);
    Route::get('blog/recent/{limit?}', [BlogController::class, 'recent']);
    Route::get('blog/{slug}', [BlogController::class, 'show']);

    // Testimonials
    Route::get('testimonials', [TestimonialController::class, 'index']);
    Route::get('testimonials/featured', [TestimonialController::class, 'featured']);

    // Contact
    Route::post('contact', [ContactController::class, 'store']);
});

// Admin API Routes (protected by auth middleware)
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
    
    // Projects
    Route::apiResource('projects', App\Http\Controllers\Admin\ProjectController::class);

    // Services
    Route::apiResource('services', App\Http\Controllers\Admin\ServiceController::class);

    // Team Members
    Route::apiResource('team-members', App\Http\Controllers\Admin\TeamMemberController::class);

    // Blog Posts
    Route::apiResource('blog-posts', App\Http\Controllers\Admin\BlogPostController::class);

    // Testimonials
    Route::apiResource('testimonials', App\Http\Controllers\Admin\TestimonialController::class);

    // Contact Inquiries
    Route::get('inquiries/stats', [App\Http\Controllers\Admin\ContactInquiryController::class, 'stats']);
    Route::apiResource('inquiries', App\Http\Controllers\Admin\ContactInquiryController::class)
        ->only(['index', 'show', 'update', 'destroy']);
});
