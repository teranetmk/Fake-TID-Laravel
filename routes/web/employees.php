<?php

Route::prefix('admin/employees')
    ->name('admin.employees')
    ->middleware(['backend', 'permission:manage_employees'])
    ->group(function (){
        Route::get('/', Backend\Employee\ListEmployeesController::class);

        Route::get('/create', Backend\Employee\CreateEmployeeController::class)->name('.create');
        Route::post('/create', Backend\Employee\PostEmployeeController::class)->name('.post');

        Route::get('/edit/{id}', Backend\Employee\EditEmployeeController::class)->name('.edit');
        Route::put('/edit/{id}', Backend\Employee\UpdateEmployeeController::class)->name('.update');
        Route::get('/{id}', Backend\Employee\DeleteEmployeeController::class)->name('.delete');
    });