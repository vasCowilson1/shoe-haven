<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactNewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_renders(): void
    {
        $this->get('/contact')->assertOk();
    }

    public function test_contact_message_can_be_submitted_without_subject(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'I have a question about my order.',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => null,
            'message' => 'I have a question about my order.',
        ]);
    }

    public function test_contact_message_can_be_submitted_with_subject(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Refund request',
            'message' => 'Please process my refund.',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('contact_messages', ['subject' => 'Refund request']);
    }

    public function test_contact_message_requires_valid_email(): void
    {
        $this->post('/contact', [
            'name' => 'Jane',
            'email' => 'not-an-email',
            'message' => 'Hello',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_message_records_logged_in_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/contact', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'Hello',
        ]);

        $this->assertDatabaseHas('contact_messages', ['user_id' => $user->id]);
    }

    public function test_newsletter_subscription_works(): void
    {
        $response = $this->post('/newsletter/subscribe', ['email' => 'sub@example.com']);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'sub@example.com']);
    }

    public function test_newsletter_rejects_duplicate_email(): void
    {
        NewsletterSubscriber::create(['email' => 'sub@example.com']);

        $this->post('/newsletter/subscribe', ['email' => 'sub@example.com']);

        $this->assertDatabaseCount('newsletter_subscribers', 1);
    }

    public function test_newsletter_requires_valid_email(): void
    {
        $this->post('/newsletter/subscribe', ['email' => 'bad'])->assertSessionHasErrors('email');
        $this->assertDatabaseCount('newsletter_subscribers', 0);
    }
}
