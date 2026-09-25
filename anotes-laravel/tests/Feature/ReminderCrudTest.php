<?php

namespace Tests\Feature;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReminderCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_reminders_routes(): void
    {
        $this->get(route('reminders.index'))->assertRedirect(route('login'));
        $this->get(route('reminders.create'))->assertRedirect(route('login'));
        $this->post(route('reminders.store'), ['title' => 'Test'])->assertRedirect(route('login'));
        $this->get('/reminders/1/edit')->assertRedirect(route('login'));
        $this->put('/reminders/1', ['title' => 'Test'])->assertRedirect(route('login'));
        $this->patch('/reminders/1/toggle')->assertRedirect(route('login'));
        $this->delete('/reminders/1')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_reminder(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('reminders.store'), [
            'title' => 'Doctor Appointment',
            'description' => 'Checkup at clinic',
            'remind_date' => '2026-10-15',
            'remind_time' => '10:30',
            'repeat_option' => 'monthly',
        ]);

        $response->assertRedirect(route('reminders.index'));

        $this->assertDatabaseHas('reminders', [
            'user_id' => $user->id,
            'title' => 'Doctor Appointment',
            'description' => 'Checkup at clinic',
            'remind_time' => '10:30',
            'repeat_option' => 'monthly',
            'is_done' => 0,
        ]);
        $created = Reminder::where('title', 'Doctor Appointment')->first();
        $this->assertNotNull($created);
        $this->assertEquals('2026-10-15', is_object($created->remind_date) ? $created->remind_date->format('Y-m-d') : $created->remind_date);
    }

    public function test_users_only_see_their_own_reminders(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Reminder::create([
            'user_id' => $user1->id,
            'title' => 'User 1 Reminder',
            'remind_date' => '2026-10-15',
            'remind_time' => '09:00',
            'repeat_option' => 'none',
        ]);

        Reminder::create([
            'user_id' => $user2->id,
            'title' => 'User 2 Reminder',
            'remind_date' => '2026-10-16',
            'remind_time' => '10:00',
            'repeat_option' => 'none',
        ]);

        $response = $this->actingAs($user1)->get(route('reminders.index'));

        $response->assertStatus(200);
        $response->assertSee('User 1 Reminder');
        $response->assertDontSee('User 2 Reminder');
        $response->assertViewHas('remindersData');

        $remindersData = $response->viewData('remindersData');
        $this->assertCount(1, $remindersData);
        $this->assertEquals('User 1 Reminder', $remindersData[0]['title']);
        $this->assertArrayHasKey('id', $remindersData[0]);
        $this->assertArrayHasKey('remind_date', $remindersData[0]);
        $this->assertArrayHasKey('remind_time', $remindersData[0]);
        $this->assertArrayHasKey('repeat_option', $remindersData[0]);
        $this->assertArrayHasKey('is_done', $remindersData[0]);
    }

    public function test_user_can_edit_and_update_own_reminder(): void
    {
        $user = User::factory()->create();
        $reminder = Reminder::create([
            'user_id' => $user->id,
            'title' => 'Original Reminder',
            'remind_date' => '2026-10-15',
            'remind_time' => '09:00',
            'repeat_option' => 'none',
        ]);

        $response = $this->actingAs($user)->get(route('reminders.edit', $reminder));
        $response->assertStatus(200);
        $response->assertSee('Original Reminder');

        $updateResponse = $this->actingAs($user)->put(route('reminders.update', $reminder), [
            'title' => 'Updated Reminder Title',
            'description' => 'Updated Description',
            'remind_date' => '2026-11-20',
            'remind_time' => '14:00',
            'repeat_option' => 'weekly',
        ]);

        $updateResponse->assertRedirect(route('reminders.index'));

        $this->assertDatabaseHas('reminders', [
            'id' => $reminder->id,
            'title' => 'Updated Reminder Title',
            'description' => 'Updated Description',
            'remind_time' => '14:00',
            'repeat_option' => 'weekly',
        ]);
        $updated = $reminder->fresh();
        $this->assertEquals('2026-11-20', is_object($updated->remind_date) ? $updated->remind_date->format('Y-m-d') : $updated->remind_date);
    }

    public function test_user_can_toggle_is_done_status(): void
    {
        $user = User::factory()->create();
        $reminder = Reminder::create([
            'user_id' => $user->id,
            'title' => 'Task To Complete',
            'remind_date' => '2026-10-15',
            'remind_time' => '09:00',
            'is_done' => false,
        ]);

        $this->actingAs($user)->patch(route('reminders.toggle', $reminder));

        $this->assertDatabaseHas('reminders', [
            'id' => $reminder->id,
            'is_done' => true,
        ]);

        $this->actingAs($user)->patch(route('reminders.toggle', $reminder));

        $this->assertDatabaseHas('reminders', [
            'id' => $reminder->id,
            'is_done' => false,
        ]);
    }

    public function test_user_can_soft_delete_own_reminder(): void
    {
        $user = User::factory()->create();
        $reminder = Reminder::create([
            'user_id' => $user->id,
            'title' => 'Reminder To Delete',
            'remind_date' => '2026-10-15',
            'remind_time' => '09:00',
        ]);

        $response = $this->actingAs($user)->delete(route('reminders.destroy', $reminder));
        $response->assertRedirect(route('reminders.index'));

        // Assert excluded from index
        $indexResponse = $this->actingAs($user)->get(route('reminders.index'));
        $indexResponse->assertDontSee('Reminder To Delete');

        // Assert soft deleted in database
        $this->assertSoftDeleted('reminders', [
            'id' => $reminder->id,
        ]);
    }

    public function test_another_user_cannot_edit_update_delete_or_toggle_reminder(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $reminder = Reminder::create([
            'user_id' => $user1->id,
            'title' => 'User 1 Confidential Reminder',
            'remind_date' => '2026-10-15',
            'remind_time' => '09:00',
        ]);

        $this->actingAs($user2)->get(route('reminders.edit', $reminder))->assertStatus(403);

        $this->actingAs($user2)->put(route('reminders.update', $reminder), [
            'title' => 'Hacked Reminder',
            'remind_date' => '2026-10-15',
            'remind_time' => '09:00',
        ])->assertStatus(403);

        $this->actingAs($user2)->patch(route('reminders.toggle', $reminder))->assertStatus(403);

        $this->actingAs($user2)->delete(route('reminders.destroy', $reminder))->assertStatus(403);

        $this->assertDatabaseHas('reminders', [
            'id' => $reminder->id,
            'title' => 'User 1 Confidential Reminder',
        ]);
    }

    public function test_validation_rules_work(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('reminders.store'), [
            'title' => '', // missing title
            'remind_date' => 'invalid-date',
            'repeat_option' => 'invalid_option',
        ]);

        $response->assertSessionHasErrors(['title', 'remind_date', 'repeat_option']);
    }
}
