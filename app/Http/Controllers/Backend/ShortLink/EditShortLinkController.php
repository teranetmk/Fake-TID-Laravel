<?php

namespace App\Http\Controllers\Backend\ShortLink;

use Illuminate\Http\Response;
use App\Models\ShortLink\ShortLink;
use App\Http\Controllers\Controller;
use App\Services\ShortLink\ShortLinkService;

class EditShortLinkController extends Controller
{
    /** @var ShortLinkService */
    private $shortLinkService;

    public function __construct(ShortLinkService $shortLinkService) 
    {
        $this->shortLinkService = $shortLinkService;
    }

    public function __invoke(int $id)
    {
        $shortLink = $this->shortLinkService->findById($id);
        abort_unless($shortLink instanceof ShortLink, Response::HTTP_NOT_FOUND);

        return view(
            'backend.shortlinks.edit',
            ['shortLink' => $shortLink]
        );
    }
}