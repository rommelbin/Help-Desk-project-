<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Hash;
use App\Models\Code;
use Illuminate\Support\Facades\Mail;

class SendResetPasswordCode extends Job
{
    protected array $attributes;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $to_email = $this->attributes['email'];
        $code = (string)rand(1000000000, 9999999999);
        $codeAttributes = [
            'name' => Hash::make($code),
            'user_id' => $this->attributes['id'],
            'counter' => '0'
        ];
        $codeModel = Code::create($codeAttributes);
        $data = array(
            'body' => 'Ваш код для восстановления пароля в системе HelpDesk:',
            'code' => $code,
        );

        Mail::send('emails.code', $data, function ($message) use ($to_email) {
            $message->to($to_email)
                ->subject('Восстановление пароля в системе Help Desk');
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });

    }
}
