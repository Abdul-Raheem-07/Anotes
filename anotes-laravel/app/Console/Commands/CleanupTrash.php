<?php

namespace App\Console\Commands;

use App\Models\Note;
use App\Models\Reminder;
use Illuminate\Console\Command;

class CleanupTrash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Mirrors the original PHP application behavior from db.php:
     *   DELETE FROM notes WHERE deleted_at IS NOT NULL AND deleted_at <= (NOW() - INTERVAL 7 DAY)
     *   DELETE FROM reminders WHERE deleted_at IS NOT NULL AND deleted_at <= (NOW() - INTERVAL 7 DAY)
     */
    protected $signature = 'trash:cleanup';

    /**
     * The console command description.
     */
    protected $description = 'Permanently delete soft-deleted Notes and Reminders older than 7 days (mirrors legacy db.php auto-purge).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoff = now()->subDays(7);

        $deletedNotes = Note::onlyTrashed()
            ->where('deleted_at', '<=', $cutoff)
            ->forceDelete();

        $deletedReminders = Reminder::onlyTrashed()
            ->where('deleted_at', '<=', $cutoff)
            ->forceDelete();

        $this->info("Trash cleanup complete.");
        $this->line("  Notes permanently deleted: {$deletedNotes}");
        $this->line("  Reminders permanently deleted: {$deletedReminders}");

        return Command::SUCCESS;
    }
}
