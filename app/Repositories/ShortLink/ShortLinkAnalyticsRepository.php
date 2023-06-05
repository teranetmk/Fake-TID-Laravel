<?php

/**
 * Framework
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\Framework\Repositories\ShortLink;

use App\Models\ShortLink\ShortLinkAnalytics;
use BADDIServices\Framework\Repositories\EloquentRepository;

class ShortLinkAnalyticsRepository extends EloquentRepository
{
    /** @var ShortLinkAnalytics */
    protected $model = ShortLinkAnalytics::class;
}