<?php

Route::prefix('admin/deposits')
    ->name('admin.deposits')
    ->middleware(['backend', 'permission:see_profits'])
    ->group(function (){
        Route::get('/', Backend\Deposits\ListDepositsController::class);
        Route::post('/', Backend\Deposits\ListDepositsController::class)->name('.in-period');
        
    });