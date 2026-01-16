<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'terms' => ['required', 'accepted'],
            'privacy' => ['required', 'accepted'],
        ], [
            'terms.required' => 'You must accept the terms of service.',
            'terms.accepted' => 'You must accept the terms of service.',
            'privacy.required' => 'You must accept the privacy policy.',
            'privacy.accepted' => 'You must accept the privacy policy.',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'terms_accepted' => true,
            'privacy_accepted' => true,
            'terms_accepted_at' => now(),
            'privacy_accepted_at' => now(),
        ]);
    }
}
