<?php

namespace App\Livewire\Settings;

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Profile extends Component
{
    use ProfileValidationRules;

    public string $name = '';

    public string $email = '';

    public string $company_type = '';

    public string $notification_frequency = 'weekly';

    public array $notification_types = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->company_type = $user->company_type ?? '';
        $this->notification_frequency = $user->notification_frequency ?? 'weekly';
        $this->notification_types = $user->notification_types ?? [];
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate(array_merge(
            $this->profileRules($user->id),
            [
                'company_type' => ['nullable', 'string', 'in:autonomo,pyme,large_corp'],
                'notification_frequency' => ['required', 'string', 'in:daily,weekly,monthly,never'],
                'notification_types' => ['nullable', 'array'],
                'notification_types.*' => ['string', 'in:deadline_reminder,new_model,model_update,summary'],
            ]
        ));

        // Convert empty company_type to null for enum compatibility
        if (isset($validated['company_type']) && $validated['company_type'] === '') {
            $validated['company_type'] = null;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }

    public function exportUserData(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = Auth::user();

        $data = [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'company_type' => $user->company_type,
                'notification_frequency' => $user->notification_frequency,
                'notification_types' => $user->notification_types,
                'created_at' => $user->created_at->toISOString(),
                'updated_at' => $user->updated_at->toISOString(),
            ],
            'favorites' => $user->favoriteTaxModels()->get()->map(function ($model) {
                return [
                    'model_number' => $model->model_number,
                    'name' => $model->name,
                    'category' => $model->category,
                ];
            })->toArray(),
            'deadlines' => $user->deadlines()->get()->map(function ($deadline) {
                return [
                    'title' => $deadline->title,
                    'description' => $deadline->description,
                    'deadline_date' => $deadline->deadline_date->toDateString(),
                    'deadline_time' => $deadline->deadline_time?->format('H:i'),
                    'year' => $deadline->year,
                ];
            })->toArray(),
            'model_completions' => $user->modelCompletions()->get()->map(function ($model) {
                return [
                    'model_number' => $model->model_number,
                    'name' => $model->name,
                    'year' => $model->pivot->year,
                    'completed' => $model->pivot->completed,
                    'completed_at' => $model->pivot->completed_at?->toISOString(),
                ];
            })->toArray(),
            'model_notes' => \App\Models\UserModelNote::where('user_id', $user->id)
                ->with('taxModel')
                ->get()
                ->map(function ($note) {
                    return [
                        'model_number' => $note->taxModel->model_number,
                        'model_name' => $note->taxModel->name,
                        'note' => $note->note,
                        'filing_number' => $note->filing_number,
                    ];
                })->toArray(),
        ];

        $filename = 'user_data_'.now()->format('Y-m-d_His').'.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }
}
