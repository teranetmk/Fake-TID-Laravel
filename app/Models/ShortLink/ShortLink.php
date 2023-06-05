<?php

namespace App\Models\ShortLink;

use BADDIServices\Framework\Entities\Entity;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShortLink extends Entity
{
    public const USER_ID_COLUMN = 'user_id';
    public const NAME_COLUMN = 'name';
    public const CODE_COLUMN = 'code';
    public const LINK_COLUMN = 'link';
    public const TO_HOME_PAGE_COLUMN = 'to_home_page';

    protected $table = 'short_links';

    public function getName(): string
    {
        return $this->getAttribute(self::NAME_COLUMN);
    }
    
    public function getCode(): string
    {
        return $this->getAttribute(self::CODE_COLUMN);
    }
    
    public function getLink(): ?string
    {
        return $this->getAttribute(self::LINK_COLUMN);
    }
    
    public function forHomePage(): bool
    {
        return (bool)$this->getAttribute(self::TO_HOME_PAGE_COLUMN) === true;
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(ShortLinkAnalytics::class, 'short_link_id', 'id');
    }
}