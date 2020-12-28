<?php

namespace Tests\Feature;

use Tests\TestCase;

class PanelTest extends TestCase
{
    public function testGuestCannotSeePanel()
    {
        $this->get(route('front.panel.accounts.index'))
            ->assertForbidden();
    }
}
