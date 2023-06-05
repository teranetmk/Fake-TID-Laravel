<?php

Route::prefix('admin/shortlinks')
    ->name('admin.shortlinks')
    ->middleware(['backend', 'permission:short_links'])
    ->group(function (){
        Route::get('/', Backend\ShortLink\ListShortLinksController::class);

        Route::get('/create', Backend\ShortLink\CreateShortLinkController::class)->name('.create');
        Route::post('/create', Backend\ShortLink\PostShortLinkController::class)->name('.post');

        Route::get('/edit/{id}', Backend\ShortLink\EditShortLinkController::class)->name('.edit');
        Route::put('/edit/{id}', Backend\ShortLink\UpdateShortLinkController::class)->name('.update');
        Route::get('/{id}', Backend\ShortLink\DeleteShortLinkController::class)->name('.delete');
    });

Route::get('u/{code}', RedirectShortLinkController::class)->name('shortlink');