<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UserCreationPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_the_user_creation_page(): void
    {
        $this->get(route('users.create'))
            ->assertOk()
            ->assertSee('Create a user and sync to Paddle sandbox');
    }

    public function test_it_creates_a_user_and_paddle_customer_from_the_form(): void
    {
        config()->set('cashier.api_key', 'test_api_key');
        config()->set('cashier.sandbox', true);

        Http::fake([
            'https://sandbox-api.paddle.com/customers*' => Http::sequence()
                ->push(['data' => []], 200)
                ->push([
                    'data' => [
                        'id' => 'ctm_form_123',
                        'name' => 'Form User',
                        'email' => 'form@example.com',
                    ],
                ], 200),
        ]);

        $this->post(route('users.store'), [
            'name' => 'Form User',
            'email' => 'form@example.com',
        ])->assertRedirect(route('users.create'));

        $user = User::where('email', 'form@example.com')->first();

        $this->assertNotNull($user);

        $this->assertDatabaseHas('customers', [
            'billable_type' => User::class,
            'billable_id' => $user->id,
            'paddle_id' => 'ctm_form_123',
            'email' => 'form@example.com',
        ]);
    }
}
