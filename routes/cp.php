<?php
use Illuminate\Support\Facades\Route;
use SiteRig\MailerLite\Http\Controllers\GetFormFieldsController;

Route::name('mailerlite.')->prefix('mailerlite')->group(function () {
    Route::get('form-fields/{form}', [GetFormFieldsController::class, '__invoke'])->name('form-fields');
});
