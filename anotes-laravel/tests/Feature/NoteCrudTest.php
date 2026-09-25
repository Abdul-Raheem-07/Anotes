<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_notes_routes(): void
    {
        $this->get(route('notes.index'))->assertRedirect(route('login'));
        $this->get(route('notes.create'))->assertRedirect(route('login'));
        $this->post(route('notes.store'), ['title' => 'Test'])->assertRedirect(route('login'));
        $this->get('/notes/1/edit')->assertRedirect(route('login'));
        $this->put('/notes/1', ['title' => 'Test'])->assertRedirect(route('login'));
        $this->delete('/notes/1')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_note(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('notes.store'), [
            'title' => 'My First Note',
            'description' => 'This is a test note description.',
        ]);

        $response->assertRedirect(route('notes.index'));
        $this->assertDatabaseHas('notes', [
            'user_id' => $user->id,
            'title' => 'My First Note',
            'description' => 'This is a test note description.',
        ]);
    }

    public function test_authenticated_user_sees_only_their_own_notes(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $note1 = Note::create([
            'user_id' => $user1->id,
            'title' => 'User 1 Note Title',
            'description' => 'User 1 Description',
        ]);

        $note2 = Note::create([
            'user_id' => $user2->id,
            'title' => 'User 2 Note Title',
            'description' => 'User 2 Description',
        ]);

        $response = $this->actingAs($user1)->get(route('notes.index'));

        $response->assertStatus(200);
        $response->assertSee('User 1 Note Title');
        $response->assertDontSee('User 2 Note Title');
    }

    public function test_authenticated_user_can_edit_their_own_note(): void
    {
        $user = User::factory()->create();
        $note = Note::create([
            'user_id' => $user->id,
            'title' => 'Original Title',
            'description' => 'Original Description',
        ]);

        $response = $this->actingAs($user)->get(route('notes.edit', $note));
        $response->assertStatus(200);
        $response->assertSee('Original Title');

        $updateResponse = $this->actingAs($user)->put(route('notes.update', $note), [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ]);

        $updateResponse->assertRedirect(route('notes.index'));
        $this->assertDatabaseHas('notes', [
            'sno' => $note->sno,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ]);
    }

    public function test_authenticated_user_can_delete_their_own_note_with_soft_delete(): void
    {
        $user = User::factory()->create();
        $note = Note::create([
            'user_id' => $user->id,
            'title' => 'Note To Delete',
            'description' => 'Will be soft deleted',
        ]);

        $response = $this->actingAs($user)->delete(route('notes.destroy', $note));

        $response->assertRedirect(route('notes.index'));

        // Assert disappears from listing
        $indexResponse = $this->actingAs($user)->get(route('notes.index'));
        $indexResponse->assertDontSee('Note To Delete');

        // Assert soft deleted in database
        $this->assertSoftDeleted('notes', [
            'sno' => $note->sno,
        ]);
    }

    public function test_another_user_cannot_edit_update_or_delete_note(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $note = Note::create([
            'user_id' => $user1->id,
            'title' => 'User 1 Private Note',
            'description' => 'Top Secret',
        ]);

        // User 2 attempts edit
        $this->actingAs($user2)->get(route('notes.edit', $note))->assertStatus(403);

        // User 2 attempts update
        $this->actingAs($user2)->put(route('notes.update', $note), [
            'title' => 'Hacked Title',
        ])->assertStatus(403);

        // User 2 attempts delete
        $this->actingAs($user2)->delete(route('notes.destroy', $note))->assertStatus(403);

        // Assert database remains unchanged
        $this->assertDatabaseHas('notes', [
            'sno' => $note->sno,
            'title' => 'User 1 Private Note',
        ]);
    }
}
