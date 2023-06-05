<?php

namespace App\Http\Controllers\Backend\ShortLink;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ShortLink\ShortLink;
use App\Http\Controllers\Controller;
use App\Services\ShortLink\ShortLinkService;

class UpdateShortLinkController extends Controller
{
    /** @var ShortLinkService */
    private $shortLinkService;

    public function __construct(ShortLinkService $shortLinkService) 
    {
        $this->shortLinkService = $shortLinkService;
    }

    public function __invoke(int $id, Request $request)
    {
        $shortLink = $this->shortLinkService->findById($id);
        abort_unless($shortLink instanceof ShortLink, Response::HTTP_NOT_FOUND);

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

        $this->shortLinkService->update($shortLink, $request->input());

        return redirect()
            ->route('admin.shortlinks')
            ->with('successMessage', 'Short link successfully updated');
    }
}