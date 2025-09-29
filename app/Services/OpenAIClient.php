<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OpenAIClient
{
    protected Client $http;

    public function __construct()
    {
        $this->http = new Client(config('openai.http'));
    }

    /** طلب محادثة مبسط */
    public function chat(array $messages, ?string $model = null): array
    {
        $payload = [
            'model'    => $model ?? config('openai.default'),
            'messages' => $messages,
        ];

        $headers = array_merge([
            'Authorization'        => 'Bearer '.config('openai.api_key'),
            'OpenAI-Organization'  => config('openai.organization'),
            'Content-Type'         => 'application/json',
        ], config('openai.headers', []));

        try {
            $res = $this->http->post('chat/completions', [
                'headers' => $headers,
                'json'    => $payload,
            ]);
            return json_decode((string) $res->getBody(), true) ?? [];
        } catch (GuzzleException $e) {
            // ارجع خطأ منسق بدلاً من كراش
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
}
