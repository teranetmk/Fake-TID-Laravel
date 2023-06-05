<?php

/**
 * Framework
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 */

namespace App\Services\ShortLink;

use App\Models\ShortLink\ShortLink;
use Carbon\Carbon;
use App\Services\Service;
use Jenssegers\Agent\Agent;
use Illuminate\Auth\AuthManager;
use App\Models\ShortLink\ShortLinkAnalytics;
use BADDIServices\Framework\Repositories\ShortLink\ShortLinkAnalyticsRepository;

class ShortLinkAnalyticsService extends Service
{
    /** @var AuthManager */
    private $authManager;

    public function __construct(ShortLinkAnalyticsRepository $shortLinkAnalyticsRepository, AuthManager $authManager) 
    {
        $this->repository = $shortLinkAnalyticsRepository;
        $this->authManager = $authManager;
    }

    public function record(ShortLink $shortLink, ?string $ip = null, ?Agent $agent = null): bool
    {
        $browser = $agent->browser();
        $browserVersion = $agent->version($browser);
        $languages = $agent->languages();
        $platform = $agent->platform();
        $platformVersion = $agent->version($platform);
        $device = $agent->device();
        $isPhone = $agent->isPhone();

        $data = [
            ShortLinkAnalytics::IP_COLUMN                   => $ip,
            ShortLinkAnalytics::SHORT_LINK_ID_COLUMN        => $shortLink->getId(),
            ShortLinkAnalytics::USER_ID_COLUMN              => $this->authManager->id(),
            ShortLinkAnalytics::BROWSER_COLUMN              => $browser,
            ShortLinkAnalytics::BROWSER_VERSION_COLUMN      => $browserVersion,
            ShortLinkAnalytics::LANGUAGES_COLUMN            => implode(',', $languages),
            ShortLinkAnalytics::PLATFORM_COLUMN             => $platform,
            ShortLinkAnalytics::PLATFORM_VERSION_COLUMN     => $platformVersion,
            ShortLinkAnalytics::DEVICE_COLUMN               => $device,
            ShortLinkAnalytics::IS_PHONE_COLUMN             => $isPhone,
            ShortLinkAnalytics::VIEWS_COLUMN                => 1,
        ];

        if (is_null($ip)) {
            $this->repository->create($data);

            return true;
        }

        $conditions = [
            [ShortLinkAnalytics::SHORT_LINK_ID_COLUMN, '=', $shortLink->getId()],
            [ShortLinkAnalytics::IP_COLUMN, '=', $ip],
            [ShortLinkAnalytics::CREATED_AT_COLUMN, '<=', Carbon::now()],
            [ShortLinkAnalytics::CREATED_AT_COLUMN, '>', Carbon::now()->subDay()],
        ];

        if ($this->authManager->check()) {
            $conditions[ShortLinkAnalytics::USER_ID_COLUMN] = $this->authManager->id();
        }

        $existsRecod = $this->repository->first($conditions);

        if (! $existsRecod instanceof ShortLinkAnalytics) {
            $this->repository->create($data);

            return true;
        }

        $data[ShortLinkAnalytics::VIEWS_COLUMN] = $existsRecod->getViews() + 1;

        return $this->repository
            ->update([ShortLinkAnalytics::ID_COLUMN => $existsRecod->getId()], $data);
    }
}