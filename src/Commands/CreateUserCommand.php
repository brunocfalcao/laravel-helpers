<?php

namespace Brunocfalcao\LaravelHelpers\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class CreateUserCommand extends Command
{
    protected $signature = 'user:create
                            {--name= : The name for the user}
                            {--email= : The email for the user}
                            {--password= : The password for the user}
                            {--random : Generate random name, email, and password}';

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

        $faker = Faker::create();

        if ($this->option('random')) {
            $name = $faker->name;
            $email = $this->generateUniqueRandomEmail($userClass, $faker);
            $password = $faker->words(1, true); // Generate a single word as the password
        } else {
            $name = $this->option('name') ?: $this->ask('Enter name:', $faker->name);
            $email = $this->option('email') ?: $this->ask('Enter email:', $faker->unique()->safeEmail);

            // Validate email uniqueness
            $user = App::make($userClass);
            while ($user::where('email', $email)->exists()) {
                $this->error('Email already exists in the database. Please try again with a different email.');
                $email = $this->ask('Enter email:', $faker->unique()->safeEmail);
            }

            $password = $this->option('password') ?: $this->secret('Enter password:');
        }

        // Create the user in the database
        $userClass::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            // Add any additional fields here, if needed.
        ]);

        $this->info('User created successfully!');

        if ($this->option('random')) {
            $this->info("Generated name: $name");
            $this->info("Generated email: $email");
            $this->info("Generated password: $password");
        }
    }

    protected function generateUniqueRandomEmail($userClass, $faker)
    {
        $email = $faker->unique()->safeEmail;

        while ($userClass::where('email', $email)->exists()) {
            $email = $faker->unique()->safeEmail;
        }

        return $email;
    }
}
