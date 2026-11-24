<?php

test('connect page explains how to join the Java Edition server', function () {
    $this->get(route('front.connect'))
        ->assertOk()
        ->assertSee('Minecraft: Java Edition')
        ->assertSee('pcbmc.co')
        ->assertSee('Copy address')
        ->assertSee('Multiplayer');
});

test('homepage sends the server CTA to the connection guide', function () {
    $this->get(route('front.home'))
        ->assertOk()
        ->assertSee('href="'.route('front.connect').'"', false)
        ->assertSee('Connect to')
        ->assertSee('pcbmc.co');
});
