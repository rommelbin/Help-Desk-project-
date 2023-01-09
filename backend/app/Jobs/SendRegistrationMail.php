<?php

namespace App\Jobs;

use App\Models\BaseModel;
use Carbon\Carbon as Carbon;
use Firebase\JWT\JWT;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class SendRegistrationMail extends Job
{
    protected BaseModel $model;
    protected array $attributes;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model, $attributes)
    {
        $this->attributes = $attributes;
        $this->model = $model->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $to_email = $this->attributes['email'];

        $payload = [
            'iss' => "help-desk", // Issuer of the token
            'sub' => $this->model->id, // Subject of the token
            'exp' => Carbon::now()->addDays(1)->timestamp,
        ];
        $token = JWT::encode($payload, env('JWT_SECRET'));
        $link = 'https://help-desk.academy.smartworld.team/auth/registration?Authorization=';
        $link .= $token;
        $data = array(
            'body' => 'Приветствуем вас в корпоративной системе HelpDesk! Для активации в системе пройдите по ссылке ниже в течении 24 часов. При истечении срока ссылки обратитесь к администратору.',
            'link' => $link,
        );

        Mail::send('emails.mail', $data, function ($message) use ($to_email) {
            $message->to($to_email)
                ->subject('Регистрация в системе Help Desk');
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }


}
