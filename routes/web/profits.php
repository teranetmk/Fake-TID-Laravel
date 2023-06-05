<?php

Route::prefix('admin/profits')
    ->name('admin.profits')
    ->middleware(['backend', 'permission:see_profits'])
    ->group(function (){
        Route::get('/', Backend\Profits\ListProfitsController::class);
        Route::post('/', Backend\Profits\ListProfitsController::class)->name('.in-period');
        
    });