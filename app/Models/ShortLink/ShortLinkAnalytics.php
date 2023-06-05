<?php

namespace App\Models\ShortLink;

use BADDIServices\Framework\Entities\Entity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortLinkAnalytics extends Entity
{
    public const USER_ID_COLUMN = 'user_id';
    public const SHORT_LINK_ID_COLUMN = 'short_link_id';
    public const IP_COLUMN = 'ip';
    public const VIEWS_COLUMN = 'views';
    public const BROWSER_COLUMN = 'browser';
    public const BROWSER_VERSION_COLUMN = 'browser_version';
    public const DEVICE_COLUMN = 'device';
    public const LANGUAGES_COLUMN = 'languages';
    public const PLATFORM_COLUMN = 'platform';
    public const PLATFORM_VERSION_COLUMN = 'platform_version';
    public const CITY_COLUMN = 'city';
    public const COUNTRY_COLUMN = 'country';
    public const TIME_ZONE_COLUMN = 'time_zone';
    public const IS_PHONE_COLUMN = 'is_phone';

    protected $table = 'short_link_analytics';

    public function getUserId(): ?int
    {
        return $this->getAttribute(self::USER_ID_COLUMN);
    }
    
    public function getIp(): string
    {
        return $this->getAttribute(self::IP_COLUMN);
    }
    
    public function getViews(): int
    {
        return $this->getAttribute(self::VIEWS_COLUMN) ?? 0;
    }

    public function shortLink(): BelongsTo
    {
        return $this->belongsTo(ShortLink::class, 'short_link_id', 'id');
    }
}