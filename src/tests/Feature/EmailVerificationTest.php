<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_sent_after_registration()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'verify@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $user = User::where('email', 'verify@example.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);
    }


    public function test_verify_link_is_displayed()
    {
      $user = User::create([
        'name' => 'テストユーザー',
        'email' => 'verify2@example.com',
        'password' => bcrypt('password123'),
        'email_verified_at' => null,
      ]);

      $response = $this->actingAs($user)->get('/email/verify');

      $response->assertStatus(200);
      $response->assertSee('認証はこちらから');
    }

    public function test_user_can_be_verified()
    {
    $user = User::create([
        'name' => 'テストユーザー',
        'email' => 'verify3@example.com',
        'password' => bcrypt('password123'),
        'email_verified_at' => null,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    $response->assertRedirect('/attendance?verified=1'); 
    $this->assertNotNull($user->fresh()->email_verified_at); 
    }
}
