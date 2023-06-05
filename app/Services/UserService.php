<?php

/**
 * Framework
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 */

namespace App\Services;

use App\Services\Service;
use BADDIServices\Framework\Repositories\UserRepository;

class UserService extends Service
{
    public function __construct(UserRepository $userRepository) 
    {
        $this->repository = $userRepository;
    }
}