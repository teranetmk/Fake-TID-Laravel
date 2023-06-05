<?php

namespace App\Http\Controllers;

use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ShortLink\ShortLink;
use App\Http\Controllers\Controller;
use App\Services\ShortLink\ShortLinkAnalyticsService;
use Illuminate\Support\Facades\Redirect;
use App\Services\ShortLink\ShortLinkService;

class RedirectShortLinkController extends Controller
{
    /** @var ShortLinkService */
    private $shortLinkService;

    /** @var ShortLinkAnalyticsService */
    private $shortLinkAnalyticsService;

    public function __construct(ShortLinkService $shortLinkService, ShortLinkAnalyticsService $shortLinkAnalyticsService) 
    {
        $this->shortLinkService = $shortLinkService;
        $this->shortLinkAnalyticsService = $shortLinkAnalyticsService;
    }

    public function __invoke(string $code, Request $request)
    {
        $shortLink = $this->shortLinkService->findByCode(strtolower($code));
        abort_unless($shortLink instanceof ShortLink, Response::HTTP_NOT_FOUND);

        $this->shortLinkAnalyticsService->record($shortLink, $request->ip(), new Agent());

        return Redirect::to($shortLink->getLink());
    }
}