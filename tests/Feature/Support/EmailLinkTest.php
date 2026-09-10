<?php

use App\Support\EmailLink;

it('appends utm params to a url without a query string', function () {
    expect(EmailLink::withUtm('https://fytrr.com', 'plan_ready', 'logo'))
        ->toBe('https://fytrr.com?utm_source=email&utm_medium=notification&utm_campaign=plan_ready&utm_content=logo');
});

it('uses & when the url already has a query string', function () {
    expect(EmailLink::withUtm('https://fytrr.com/app?locale=de', 'plan_ready'))
        ->toBe('https://fytrr.com/app?locale=de&utm_source=email&utm_medium=notification&utm_campaign=plan_ready');
});
