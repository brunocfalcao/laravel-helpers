<?php

namespace Brunocfalcao\LaravelHelpers\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class ChangeUserPasswordCommand extends Command
{
    protected $signature = 'change:password
                            {--email= : The email for the user}
                            {--password= : The password for the user}
                            {--id= : The password for the user id}';

    protected $description = 'Change a user password';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $id = $this->option('id');

        // Get the User model from auth config
        $userModel = config('auth.providers.users.model');

        // Validate input
        $validator = Validator::make([
            'email' => $email,
            'id' => $id,
        ], [
            'email' => 'email|nullable',
            'id' => 'numeric|nullable',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException('Invalid input: '.$validator->errors());
        }

        // Check if email or ID is provided
        if (! $email && ! $id) {
            throw new InvalidArgumentException('Either email or ID is required.');
        }

        // Prompt for password if not provided
        if (! $password) {
            do {
                $password = $this->secret('Please enter a new password:');
                if (empty($password)) {
                    $this->warn('Password cannot be empty. Please try again.');
                }
            } while (empty($password));
        }

        // Find user by email or ID
        $user = null;
        if ($email) {
            $user = $userModel::where('email', $email)->first();
        } elseif ($id) {
            $user = $userModel::find($id);
        }

        if (! $user) {
            $this->error('User not found.');

            return;
        }

        // Update password
        $user->password = Hash::make($password);
        $user->save();

        $this->info('Password changed successfully.');
    }
}
