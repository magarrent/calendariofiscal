<?php

namespace App\Console\Commands;

use App\Models\Deadline;
use App\Models\NotificationLog;
use App\Models\TaxModelReminder;
use App\Notifications\TaxModelDeadlineReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTaxModelReminders extends Command
{
    protected $signature = 'notifications:send-tax-reminders';

    protected $description = 'Send tax model deadline reminders to users';

    public function handle(): int
    {
        $this->info('Checking for upcoming tax deadlines...');

        $today = Carbon::today();
        $reminders = TaxModelReminder::with(['user', 'taxModel'])
            ->enabled()
            ->get();

        $sentCount = 0;

        foreach ($reminders as $reminder) {
            $upcomingDeadlines = Deadline::where('tax_model_id', $reminder->tax_model_id)
                ->whereDate('deadline_date', $today->copy()->addDays($reminder->days_before))
                ->get();

            foreach ($upcomingDeadlines as $deadline) {
                $alreadySent = NotificationLog::where('user_id', $reminder->user_id)
                    ->where('tax_model_id', $reminder->tax_model_id)
                    ->where('tax_model_reminder_id', $reminder->id)
                    ->whereDate('sent_at', $today)
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                $reminder->user->notify(
                    new TaxModelDeadlineReminder(
                        $reminder->taxModel,
                        $deadline,
                        $reminder->days_before
                    )
                );

                NotificationLog::create([
                    'user_id' => $reminder->user_id,
                    'tax_model_id' => $reminder->tax_model_id,
                    'tax_model_reminder_id' => $reminder->id,
                    'notification_type' => 'email',
                    'sent_at' => now(),
                    'details' => [
                        'deadline_date' => $deadline->deadline_date->format('Y-m-d'),
                        'days_before' => $reminder->days_before,
                    ],
                ]);

                $sentCount++;

                $this->info("Sent reminder to {$reminder->user->email} for {$reminder->taxModel->name}");
            }
        }

        $this->info("Sent {$sentCount} reminder(s)");

        return Command::SUCCESS;
    }
}
