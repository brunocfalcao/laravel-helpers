<?php

namespace Brunocfalcao\LaravelHelpers\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    protected $signature = 'user:create';
    protected $description = 'Create a new user';

    public function handle()
    {
        $defaultUserClass = Config::get('auth.providers.users.model', 'App\Models\User');

        if (!class_exists($defaultUserClass)) {
            $this->error('The default user class does not exist.');
            $userClass = $this->ask('Please provide the class name for the user (e.g., MyClass\User):');

            if (!class_exists($userClass) || !is_subclass_of($userClass, Model::class)) {
                $this->error("Invalid class name or class doesn't extend Eloquent Model.");
                return;
            }
        } else {
            $userClass = $defaultUserClass;
        }

        $name = $this->ask('Enter name:');
        $email = $this->ask('Enter email:');

        // Validate email uniqueness
        $user = App::make($userClass);
        while ($user::where('email', $email)->exists()) {
            $this->error('Email already exists in the database. Please try again with a different email.');
            $email = $this->ask('Enter email:');
        }

        $password = $this->secret('Enter password:');

        // Create the user in the database
        $user::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $this->info('User created successfully!');
    }
}
