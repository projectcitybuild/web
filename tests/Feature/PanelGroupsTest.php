<?php

namespace Tests\Feature;

use App\Entities\Models\Eloquent\Group;
use Tests\TestCase;

class PanelGroupsTest extends TestCase
{
    public function testCanViewGroupList()
    {
        $group = Group::factory()->create();
        $this->actingAs($this->adminAccount())
            ->get(route('front.panel.groups.index'))
            ->assertSee($group->name);
    }
}
