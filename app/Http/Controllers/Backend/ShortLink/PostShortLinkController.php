<?php

namespace App\Http\Controllers\Backend\ShortLink;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShortLink\ShortLink;
use App\Services\ShortLink\ShortLinkService;

class PostShortLinkController extends Controller
{
    /** @var ShortLinkService */
    private $shortLinkService;

    public function __construct(ShortLinkService $shortLinkService) 
    {
        $this->shortLinkService = $shortLinkService;
    }

    public function __invoke(Request $request)
    {
        $validator = Validator::make( 
            $request->input(), 
            [
                ShortLink::NAME_COLUMN => ['required', 'string'], 
                ShortLink::CODE_COLUMN => ['nullable', 'string'], 
                ShortLink::LINK_COLUMN => ['nullable', 'url'], 
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        if (is_null($request->input(ShortLink::LINK_COLUMN))) {
            $shortLink = $this->shortLinkService->createForHomePage($request->input(ShortLink::NAME_COLUMN));
        } else {
            $shortLink = $this->shortLinkService->create($request->input());
        }

        return redirect()
            ->route('admin.shortlinks')
            ->with('successMessage', sprintf('Short link successfully created: %s', route('shortlink', ['code' => $shortLink->getCode()])));
    }
}