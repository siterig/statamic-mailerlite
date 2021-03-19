<?php

Route::prefix('mailerlite/')->name('mailerlite.')->group(function() {
    Route::get('/', 'FormsController@index')->name('index');
});
