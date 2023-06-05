<?php

namespace App\Http\Controllers\Backend\ShortLink;

use App\Http\Controllers\Controller;

class CreateShortLinkController extends Controller
{
    public function __invoke()
    {
        return view('backend.shortlinks.create');
    }
}