<?php

namespace App\Http\Controllers;

use App\Models\Deadline;
use App\Models\User;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CalendarSubscriptionController extends Controller
{
    /**
     * Generate an iCal feed for a user's calendar subscription.
     */
    public function feed(string $token): StreamedResponse
    {
        $user = User::where('subscription_token', $token)->firstOrFail();

        $calendar = Calendar::create('Calendario Fiscal')
            ->productIdentifier('Calendario Fiscal')
            ->refreshInterval(60); // Refresh every hour

        $deadlines = $this->getDeadlinesForUser($user);

        foreach ($deadlines as $deadline) {
            $taxModel = $deadline->taxModel;

            if (! $taxModel) {
                continue;
            }

            $event = Event::create()
                ->name($taxModel->name.' - Modelo '.$taxModel->model_number)
                ->uniqueIdentifier('deadline-'.$deadline->id.'@calendariofiscal.app')
                ->startsAt($deadline->deadline_date)
                ->endsAt($deadline->deadline_date)
                ->fullDay();

            if ($deadline->deadline_time) {
                $event->startsAt($deadline->deadline_date->setTimeFrom($deadline->deadline_time))
                    ->endsAt($deadline->deadline_date->setTimeFrom($deadline->deadline_time)->addHour())
                    ->fullDay(false);
            }

            if ($taxModel->description) {
                $event->description($taxModel->description);
            }

            if ($taxModel->aeat_url) {
                $event->url($taxModel->aeat_url);
            }

            $calendar->event($event);
        }

        return response()->streamDownload(
            fn () => print $calendar->get(),
            'calendario_fiscal.ics',
            ['Content-Type' => 'text/calendar; charset=utf-8']
        );
    }

    /**
     * Get deadlines for a user based on their favorites and company profile.
     */
    protected function getDeadlinesForUser(User $user): \Illuminate\Support\Collection
    {
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;

        $query = Deadline::query()
            ->with('taxModel')
            ->whereIn('year', [$currentYear, $nextYear])
            ->where('deadline_date', '>=', now()->subMonths(3))
            ->where('deadline_date', '<=', now()->addYear());

        $deadlines = $query->get();

        // Filter deadlines based on user's favorite models and company type
        return $deadlines->filter(function (Deadline $deadline) use ($user) {
            $taxModel = $deadline->taxModel;

            if (! $taxModel) {
                return false;
            }

            // Include if user has favorited this model
            if ($user->hasFavorite($taxModel)) {
                return true;
            }

            // Include if model is applicable to user's company type
            if ($user->company_type) {
                $applicable = is_array($taxModel->applicable_to) ? $taxModel->applicable_to : [];

                return in_array($user->company_type, $applicable);
            }

            return false;
        })->sortBy('deadline_date');
    }
}
