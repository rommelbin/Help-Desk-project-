<?php
declare(strict_types=1);

namespace App\Exceptions;

use GuzzleHttp\Client;

class SendExceptionsService
{
    private Client $client;
    private string $token;
    private int $channel_id;

    public function __construct()
    {
        $this->client = new Client();
        $this->channel_id = -1001258893898;
        $this->token = "5007422124:AAHlhhsWZcG_mqJ_o8jrjSf9WnDQcuZJcFI";
    }

    public function sendException(\Exception $exception): bool
    {
        $text = $this->prepareText($exception);
        $response = $this->client->request('GET', "https://api.telegram.org/bot$this->token/sendMessage?chat_id=$this->channel_id&text=$text");
        return !$response->getStatusCode() == 200;
    }

    public function prepareText(\Exception $exception): string
    {
        $message = $exception->getMessage();
        $line = $exception->getLine();
        $file = $exception->getFile();
        $error_code = $exception->getCode();
        $date = date("l jS \of F Y h:i:s A");
        return "Ошибка! $message\nError Code: $error_code \nLine: $line \nFile: $file \n$date";
    }
}
