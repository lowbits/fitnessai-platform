<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\SetPasswordNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;

class SendPasswordResetLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:send-password-reset {email} {--force : Skip the confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email a user a signed link to set a new password, even if they already have one';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No user found with email '{$email}'.");

            return Command::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm("Send a password reset link to {$user->name} <{$user->email}>?")) {
            $this->info('Aborted.');

            return Command::SUCCESS;
        }

        $status = Password::broker(config('fortify.passwords'))->sendResetLink(
            ['email' => $user->email],
            function ($user, $token) {
                $user->notify(new SetPasswordNotification($token));

                return Password::RESET_LINK_SENT;
            }
        );

        if ($status !== Password::RESET_LINK_SENT) {
            $this->error('Could not send the link: '.__($status));

            return Command::FAILURE;
        }

        $this->info("Password reset link sent to {$user->email}. It expires in 24 hours.");

        return Command::SUCCESS;
    }
}
