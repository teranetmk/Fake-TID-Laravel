<?php

namespace App\Http\Controllers\Backend\ShortLink;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ShortLink\ShortLinkService;

class ListShortLinksController extends Controller
{
    /** @var ShortLinkService */
    private $shortLinkService;

    public function __construct(ShortLinkService $shortLinkService) 
    {
        $this->shortLinkService = $shortLinkService;
    }

    public function __invoke(Request $request)
    {
        $shortLinks = $this->shortLinkService->paginate($request->query('page'));

        return view(
            'backend.shortlinks.index',
            [
                'managementPage'    => true,
                'shortLinks'        => $shortLinks
            ]
        );
    }
}
