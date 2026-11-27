<?php

test('contact page renders page-specific canonical and social metadata', function () {
    $contactUrl = route('front.contact');

    $this->get($contactUrl.'?utm_source=share')
        ->assertOk()
        ->assertSee('<title>Contact Us</title>', false)
        ->assertSee('<meta name="description" content="General inquiries and questions">', false)
        ->assertSee('<link rel="canonical" href="'.$contactUrl.'">', false)
        ->assertSee('<meta property="og:url" content="'.$contactUrl.'">', false)
        ->assertSee('<meta property="og:title" content="Contact Us">', false)
        ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
        ->assertSee('<meta property="og:image" content="'.Illuminate\Support\Facades\Vite::asset('resources/images/hero.webp').'">', false)
        ->assertSee('<meta name="twitter:image" content="'.Illuminate\Support\Facades\Vite::asset('resources/images/hero.webp').'">', false);
});

test('donation page renders its own metadata description', function () {
    $this->get(route('front.donate'))
        ->assertOk()
        ->assertSee('<title>Donate - Project City Build</title>', false)
        ->assertSee('<meta name="description" content="Support Project City Build and help cover the costs of running our Minecraft servers and community website.">', false)
        ->assertSee('<meta property="og:description" content="Support Project City Build and help cover the costs of running our Minecraft servers and community website.">', false)
        ->assertSeeText('/tp');
});
