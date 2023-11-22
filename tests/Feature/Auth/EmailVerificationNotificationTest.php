<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;

class EmailVerificationNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_new_email_verification_notification_positive(): void
    {
        /**
         * @var User $user
         */
        $user = User::factory()->unverified()->create();
        Notification::fake();

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertJson(['status' => 'verification-link-sent']);
    }

    public function  test_send_new_email_verification_notification_negative(): void
    {
        /**
         * @var User $user
         */
        $user = User::factory()->create();
        Notification::fake();

        $response = $this->actingAs($user)->post('/email/verification-notification');

        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
