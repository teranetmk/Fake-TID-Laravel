<?php

/**
 * Framework
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\Framework\Repositories\ShortLink;

use App\Models\ShortLink\ShortLink;
use BADDIServices\Framework\Repositories\EloquentRepository;

class ShortLinkRepository extends EloquentRepository
{
    /** @var ShortLink */
    protected $model = ShortLink::class;
}