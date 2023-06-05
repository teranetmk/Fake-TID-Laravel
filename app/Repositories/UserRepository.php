<?php

/**
 * Framework
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\Framework\Repositories;

use App\Models\User;
use BADDIServices\Framework\Repositories\EloquentRepository;

class UserRepository extends EloquentRepository
{
    /** @var User */
    protected $model = User::class;
}