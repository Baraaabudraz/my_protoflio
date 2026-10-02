<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

// ─── Portfolio Frontend ───
Route::get('/', [PortfolioController::class, 'index'])->name('home');
Route::get('/projects/{id}', [PortfolioController::class, 'show'])->name('project.show')->where('id', '[0-9]+');
Route::get('/lang/{locale}', [PortfolioController::class, 'switchLocale'])->name('lang.switch');

// ─── SEO ───
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

// ─── Contact ───
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:contact')->name('contact.store');

// ─── Admin Auth ───
Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// ─── Admin CMS ───
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Projects
    Route::get('/projects', [AdminController::class, 'projects'])->name('projects');
    Route::get('/projects/create', [AdminController::class, 'projectCreate'])->name('projects.create');
    Route::post('/projects', [AdminController::class, 'projectStore'])->name('projects.store');
    Route::get('/projects/{id}/edit', [AdminController::class, 'projectEdit'])->name('projects.edit');
    Route::put('/projects/{id}', [AdminController::class, 'projectUpdate'])->name('projects.update');
    Route::delete('/projects/{id}', [AdminController::class, 'projectDelete'])->name('projects.delete');

    // Project gallery
    Route::post('/projects/{id}/gallery', [AdminController::class, 'galleryUpload'])->name('projects.gallery.upload');
    Route::put('/projects/{id}/gallery', [AdminController::class, 'galleryUpdate'])->name('projects.gallery.update');
    Route::delete('/gallery/{imageId}', [AdminController::class, 'galleryDelete'])->name('projects.gallery.delete');

    // Services
    Route::get('/services', [AdminController::class, 'services'])->name('services');
    Route::get('/services/create', [AdminController::class, 'serviceCreate'])->name('services.create');
    Route::post('/services', [AdminController::class, 'serviceStore'])->name('services.store');
    Route::get('/services/{id}/edit', [AdminController::class, 'serviceEdit'])->name('services.edit');
    Route::put('/services/{id}', [AdminController::class, 'serviceUpdate'])->name('services.update');
    Route::delete('/services/{id}', [AdminController::class, 'serviceDelete'])->name('services.delete');

    // Experience
    Route::get('/experience', [AdminController::class, 'experiences'])->name('experience');
    Route::get('/experience/create', [AdminController::class, 'experienceCreate'])->name('experience.create');
    Route::post('/experience', [AdminController::class, 'experienceStore'])->name('experience.store');
    Route::get('/experience/{id}/edit', [AdminController::class, 'experienceEdit'])->name('experience.edit');
    Route::put('/experience/{id}', [AdminController::class, 'experienceUpdate'])->name('experience.update');
    Route::delete('/experience/{id}', [AdminController::class, 'experienceDelete'])->name('experience.delete');

    // Skills
    Route::get('/skills', [AdminController::class, 'skills'])->name('skills');
    Route::post('/skills/category', [AdminController::class, 'skillCategoryStore'])->name('skills.category.store');
    Route::delete('/skills/category/{id}', [AdminController::class, 'skillCategoryDelete'])->name('skills.category.delete');
    Route::post('/skills', [AdminController::class, 'skillStore'])->name('skills.store');
    Route::delete('/skills/{id}', [AdminController::class, 'skillDelete'])->name('skills.delete');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'settingsUpdate'])->name('settings.update');

    // Contact messages
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
    Route::patch('/messages/{id}/read', [AdminController::class, 'messageToggleRead'])->name('messages.read');
    Route::delete('/messages/{id}', [AdminController::class, 'messageDelete'])->name('messages.delete');
});
