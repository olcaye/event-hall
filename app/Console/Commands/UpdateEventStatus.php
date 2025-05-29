<?php

namespace App\Console\Commands;

use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateEventStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:update-status';
    protected $description = 'Change status as ended of the ended events';

    public function handle(): int
    {
        $now = Carbon::now();

        $endedEvents = Event::whereNotIn('status', [EventStatus::ENDED, EventStatus::CANCELLED])
            ->where(function ($query) use ($now) {
                $query->where('date', '<', $now->toDateString())
                    ->orWhere(function ($query) use ($now) {
                        $query->where('date', '=', $now->toDateString())
                            ->whereTime('end_time', '<=', $now->toTimeString());
                    });
            })
            ->update(['status' => EventStatus::ENDED]);

        $this->info("{$endedEvents} event changed to 'ended' status");

        return Command::SUCCESS;
    }
}
