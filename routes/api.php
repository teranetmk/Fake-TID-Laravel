<?php

    //\URL::forceScheme('https');
    
    use Illuminate\Http\Request;
  
    Route::middleware('auth:api')->get('/user', 'API\UserController');