<?php

namespace Tests\Feature;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_is_publicly_accessible(): void
    {
        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertSee('Send Message');
    }

    public function test_valid_contact_submission_creates_a_message_and_sets_success_flash(): void
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'subject' => 'A question',
            'message' => 'Please tell me more about ANoteS.',
        ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('success', "Thank you! Your message has been sent. I'll get back to you soon.");
        $this->assertDatabaseHas('messages', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'subject' => 'A question',
            'message' => 'Please tell me more about ANoteS.',
        ]);
    }

    public function test_invalid_contact_submission_returns_validation_errors_and_old_input(): void
    {
        $response = $this->from(route('contact'))->post(route('contact.submit'), [
            'name' => '',
            'email' => 'not-an-email',
            'subject' => '',
            'message' => '',
        ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
        $response->assertSessionHasInput('email', 'not-an-email');
        $this->assertDatabaseCount('messages', 0);
    }

    public function test_contact_submission_does_not_accept_unexpected_database_fields(): void
    {
        $this->post(route('contact.submit'), [
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
            'subject' => 'Security',
            'message' => 'A valid message.',
            'id' => 999,
            'created_at' => '2000-01-01 00:00:00',
        ]);

        $message = Message::query()->first();

        $this->assertNotNull($message);
        $this->assertNotSame(999, $message->id);
        $this->assertNotSame('2000-01-01 00:00:00', (string) $message->created_at);
    }

    public function test_no_public_message_management_route_exists(): void
    {
        $this->get('/messages')->assertNotFound();
    }
}
