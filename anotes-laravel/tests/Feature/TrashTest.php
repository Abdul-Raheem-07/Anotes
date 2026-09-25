<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TrashTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_trash(): void
    {
        $this->get(route('trash.index'))->assertRedirect(route('login'));
    }

    public function test_user_sees_only_own_deleted_records_and_not_active_records(): void
    {
        $user = $this->createUser();
        $otherUser = $this->createUser();

        $deletedNote = $this->createNote($user, 'Deleted Note');
        $activeNote = $this->createNote($user, 'Active Note');
        $otherDeletedNote = $this->createNote($otherUser, 'Other Deleted Note');
        $deletedReminder = $this->createReminder($user, 'Deleted Reminder');
        $activeReminder = $this->createReminder($user, 'Active Reminder');
        $otherDeletedReminder = $this->createReminder($otherUser, 'Other Deleted Reminder');
        $deletedNote->delete();
        $otherDeletedNote->delete();
        $deletedReminder->delete();
        $otherDeletedReminder->delete();

        $response = $this->actingAs($user)->get(route('trash.index'));

        $response->assertOk();
        $response->assertSee('Deleted Note');
        $response->assertSee('Deleted Reminder');
        $response->assertDontSee('Active Note');
        $response->assertDontSee('Active Reminder');
        $response->assertDontSee('Other Deleted Note');
        $response->assertDontSee('Other Deleted Reminder');
    }

    public function test_user_can_restore_own_deleted_note(): void
    {
        $user = $this->createUser();
        $note = $this->createNote($user, 'Restorable Note');
        $note->delete();

        $response = $this->actingAs($user)->patch(route('trash.notes.restore', $note->sno));

        $response->assertRedirect(route('trash.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('notes', ['sno' => $note->sno, 'deleted_at' => null]);
        $this->assertFalse($note->fresh()->trashed());
    }

    public function test_user_can_restore_own_deleted_reminder(): void
    {
        $user = $this->createUser();
        $reminder = $this->createReminder($user, 'Restorable Reminder');
        $reminder->delete();

        $response = $this->actingAs($user)->patch(route('trash.reminders.restore', $reminder->id));

        $response->assertRedirect(route('trash.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('reminders', ['id' => $reminder->id, 'deleted_at' => null]);
        $this->assertFalse($reminder->fresh()->trashed());
    }

    public function test_user_can_permanently_delete_own_deleted_note(): void
    {
        $user = $this->createUser();
        $note = $this->createNote($user, 'Permanent Note');
        $note->delete();

        $response = $this->actingAs($user)->delete(route('trash.notes.force-delete', $note->sno));

        $response->assertRedirect(route('trash.index'))->assertSessionHas('success');
        $this->assertDatabaseMissing('notes', ['sno' => $note->sno]);
    }

    public function test_user_can_permanently_delete_own_deleted_reminder(): void
    {
        $user = $this->createUser();
        $reminder = $this->createReminder($user, 'Permanent Reminder');
        $reminder->delete();

        $response = $this->actingAs($user)->delete(route('trash.reminders.force-delete', $reminder->id));

        $response->assertRedirect(route('trash.index'))->assertSessionHas('success');
        $this->assertDatabaseMissing('reminders', ['id' => $reminder->id]);
    }

    public function test_user_cannot_restore_or_permanently_delete_another_users_note(): void
    {
        $owner = $this->createUser();
        $otherUser = $this->createUser();
        $note = $this->createNote($owner, 'Private Deleted Note');
        $note->delete();

        $this->actingAs($otherUser)->patch(route('trash.notes.restore', $note->sno))->assertForbidden();
        $this->actingAs($otherUser)->delete(route('trash.notes.force-delete', $note->sno))->assertForbidden();
        $this->assertSoftDeleted('notes', ['sno' => $note->sno]);
    }

    public function test_user_cannot_restore_or_permanently_delete_another_users_reminder(): void
    {
        $owner = $this->createUser();
        $otherUser = $this->createUser();
        $reminder = $this->createReminder($owner, 'Private Deleted Reminder');
        $reminder->delete();

        $this->actingAs($otherUser)->patch(route('trash.reminders.restore', $reminder->id))->assertForbidden();
        $this->actingAs($otherUser)->delete(route('trash.reminders.force-delete', $reminder->id))->assertForbidden();
        $this->assertSoftDeleted('reminders', ['id' => $reminder->id]);
    }

    public function test_cleanup_permanently_deletes_old_trash_and_keeps_recent_and_active_records(): void
    {
        $user = $this->createUser();
        $oldNote = $this->createNote($user, 'Old Note');
        $recentNote = $this->createNote($user, 'Recent Note');
        $activeNote = $this->createNote($user, 'Active Note');
        $oldReminder = $this->createReminder($user, 'Old Reminder');
        $recentReminder = $this->createReminder($user, 'Recent Reminder');
        $activeReminder = $this->createReminder($user, 'Active Reminder');
        $oldNote->delete();
        $recentNote->delete();
        $oldReminder->delete();
        $recentReminder->delete();
        Note::withTrashed()->whereKey($oldNote->sno)->update(['deleted_at' => now()->subDays(8)]);
        Reminder::withTrashed()->whereKey($oldReminder->id)->update(['deleted_at' => now()->subDays(8)]);

        $this->artisan('trash:cleanup')->assertSuccessful();

        $this->assertDatabaseMissing('notes', ['sno' => $oldNote->sno]);
        $this->assertDatabaseMissing('reminders', ['id' => $oldReminder->id]);
        $this->assertDatabaseHas('notes', ['sno' => $recentNote->sno]);
        $this->assertDatabaseHas('reminders', ['id' => $recentReminder->id]);
        $this->assertDatabaseHas('notes', ['sno' => $activeNote->sno, 'deleted_at' => null]);
        $this->assertDatabaseHas('reminders', ['id' => $activeReminder->id, 'deleted_at' => null]);
    }

    private function createNote(User $user, string $title): Note
    {
        return Note::create([
            'user_id' => $user->id,
            'title' => $title,
            'description' => 'Test description',
        ]);
    }

    private function createUser(): User
    {
        /** @var User $user */
        $user = User::factory()->create();

        return $user;
    }

    private function createReminder(User $user, string $title): Reminder
    {
        return Reminder::create([
            'user_id' => $user->id,
            'title' => $title,
            'description' => 'Test description',
            'remind_date' => Carbon::today()->addDay()->toDateString(),
            'remind_time' => '09:00',
            'repeat_option' => 'none',
        ]);
    }
}
