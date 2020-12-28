<?php


namespace Tests\Feature;


use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use App\Http\Actions\SyncUserToDiscourse;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class PanelAccountTest extends TestCase
{
    use WithFaker;

    private $adminAccount;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->adminAccount = Account::factory()->create();
        $adminGroup = Group::create([
            'name' => 'Administrator',
            'can_access_panel' => true
        ]);

        $this->adminAccount->groups()->attach($adminGroup->group_id);
    }

    public function testAccountDetailsShown()
    {
        $this->withoutExceptionHandling();

        $account = Account::factory()->create();

        $this->actingAs($this->adminAccount)
            ->get(route('front.panel.accounts.show', $account))
            ->assertOk()
            ->assertSee($account->username);
    }

    public function testAccountDetailsChange()
    {
        $account = Account::factory()->create();

        $newData = [
            'email' => $this->faker->email,
            'username' => $this->faker->userName
        ];

        $this->mock(SyncUserToDiscourse::class, function ($mock) {
            $mock->shouldReceive('setUser')->once();
            $mock->shouldReceive('syncAll')->once();
        });


        $this->actingAs($this->adminAccount)
            ->withoutExceptionHandling()
            ->put(route('front.panel.accounts.update', $account), $newData)
            ->assertRedirect();

        $this->assertDatabaseHas('accounts', $newData);

    }
}
