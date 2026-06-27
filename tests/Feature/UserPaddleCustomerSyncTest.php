<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UserPaddleCustomerSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_paddle_customer_when_a_user_is_created(): void
    {
        config()->set('cashier.api_key', 'test_api_key');
        config()->set('cashier.sandbox', true);

        Http::fake([
            'https://sandbox-api.paddle.com/customers*' => Http::sequence()
                ->push(['data' => []], 200)
                ->push([
                    'data' => [
                        'id' => 'ctm_123',
                        'name' => 'Test User',
                        'email' => 'test@example.com',
                    ],
                ], 200),
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('customers', [
            'billable_type' => User::class,
            'billable_id' => $user->id,
            'paddle_id' => 'ctm_123',
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
