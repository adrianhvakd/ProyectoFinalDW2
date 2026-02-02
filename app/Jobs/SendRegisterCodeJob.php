<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRegisterCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $email;

    public int $code;

    public function __construct(string $email, int $code)
    {
        $this->email = $email;
        $this->code = $code;
    }

    public function handle(): void
    {
        Mail::send('auth.emails.register-code', [
            'code' => $this->code,
        ], function ($message) {
            $message
                ->to($this->email)
                ->subject('Código de confirmación de registro');
        });
    }
}
