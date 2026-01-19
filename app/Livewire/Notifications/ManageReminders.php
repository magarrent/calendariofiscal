<?php

namespace App\Livewire\Notifications;

use App\Models\TaxModel;
use App\Models\TaxModelReminder;
use Livewire\Component;

class ManageReminders extends Component
{
    public ?int $taxModelId = null;

    public array $reminders = [];

    public array $presetDays = [1, 7, 15, 30];

    public bool $allEnabled = true;

    public function mount(?int $taxModelId = null): void
    {
        $this->taxModelId = $taxModelId;
        $this->loadReminders();
    }

    public function loadReminders(): void
    {
        if ($this->taxModelId) {
            $existingReminders = auth()->user()
                ->taxModelReminders()
                ->where('tax_model_id', $this->taxModelId)
                ->get()
                ->keyBy('days_before');

            foreach ($this->presetDays as $days) {
                $reminder = $existingReminders->get($days);
                $this->reminders[$days] = [
                    'id' => $reminder?->id,
                    'enabled' => $reminder?->enabled ?? false,
                    'days_before' => $days,
                ];
            }
        } else {
            foreach ($this->presetDays as $days) {
                $this->reminders[$days] = [
                    'id' => null,
                    'enabled' => false,
                    'days_before' => $days,
                ];
            }
        }

        $this->updateAllEnabledState();
    }

    public function updatedReminders($value, $key): void
    {
        // Parse the nested key (e.g., "1.enabled" -> days = 1, field = "enabled")
        if (str_contains($key, '.enabled')) {
            $days = (int) str_replace('.enabled', '', $key);

            if ($this->taxModelId) {
                if ($this->reminders[$days]['enabled']) {
                    $reminder = TaxModelReminder::updateOrCreate(
                        [
                            'user_id' => auth()->id(),
                            'tax_model_id' => $this->taxModelId,
                            'days_before' => $days,
                        ],
                        [
                            'enabled' => true,
                            'notification_type' => 'email',
                        ]
                    );
                    $this->reminders[$days]['id'] = $reminder->id;
                } elseif ($this->reminders[$days]['id']) {
                    TaxModelReminder::find($this->reminders[$days]['id'])?->update(['enabled' => false]);
                }
            }

            $this->updateAllEnabledState();
        }
    }

    public function toggleAll(): void
    {
        $this->allEnabled = ! $this->allEnabled;

        foreach ($this->reminders as $days => $reminder) {
            $this->reminders[$days]['enabled'] = $this->allEnabled;

            if ($this->taxModelId) {
                if ($this->allEnabled) {
                    $newReminder = TaxModelReminder::updateOrCreate(
                        [
                            'user_id' => auth()->id(),
                            'tax_model_id' => $this->taxModelId,
                            'days_before' => $days,
                        ],
                        [
                            'enabled' => true,
                            'notification_type' => 'email',
                        ]
                    );
                    $this->reminders[$days]['id'] = $newReminder->id;
                } elseif ($this->reminders[$days]['id']) {
                    TaxModelReminder::find($this->reminders[$days]['id'])?->update(['enabled' => false]);
                }
            }
        }
    }

    public function applyToCategory(string $category): void
    {
        $taxModels = TaxModel::where('category', $category)->get();

        foreach ($taxModels as $taxModel) {
            foreach ($this->reminders as $days => $reminder) {
                if ($reminder['enabled']) {
                    TaxModelReminder::updateOrCreate(
                        [
                            'user_id' => auth()->id(),
                            'tax_model_id' => $taxModel->id,
                            'days_before' => $days,
                        ],
                        [
                            'enabled' => true,
                            'notification_type' => 'email',
                        ]
                    );
                }
            }
        }

        session()->flash('message', "Recordatorios aplicados a todos los modelos de la categoría {$category}");
    }

    protected function updateAllEnabledState(): void
    {
        $enabledCount = collect($this->reminders)->where('enabled', true)->count();
        $this->allEnabled = $enabledCount === count($this->reminders);
    }

    public function render()
    {
        return view('livewire.notifications.manage-reminders');
    }
}
