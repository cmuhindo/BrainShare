<?php

namespace App\Services;

use GuzzleHttp\Client;

class GeminiService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GOOGLE_API_KEY');
    }

    public function generateContent($prompt)
    {
        $response = $this->client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'key' => $this->apiKey,
            ],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt,
                            ]
                        ]
                    ]
                ]
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
