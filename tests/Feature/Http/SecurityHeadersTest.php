<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

it('allows blob and data image sources in the content security policy', function () {
    $response = (new SecurityHeaders)->handle(
        Request::create('/'),
        fn () => new Response('ok'),
    );

    $csp = $response->headers->get('Content-Security-Policy');

    expect($csp)->toContain("img-src 'self' data: blob:");
});
