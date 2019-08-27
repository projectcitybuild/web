<?php

namespace Tests\Feature;

use Tests\TestCase;

class PanelAccountListTest extends TestCase
{
    public function testGuestCannotSeePanel()
    {
        $this->withoutExceptionHandling();

        $this->get(route('front.panel.accounts.index'))
            ->assertUnauthorized();
    }
}
