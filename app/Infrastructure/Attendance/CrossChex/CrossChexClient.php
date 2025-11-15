<?php

namespace App\Infrastructure\Attendance\CrossChex;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use RuntimeException;

class CrossChexClient
{
    public function __construct(
        private ?string $baseUrl = null,
        private ?string $apiKey = null,
        private ?string $apiSecret = null,
    ) {
        $this->baseUrl   ??= config('crosschex.base_url');
        $this->apiKey    ??= config('crosschex.api_key');
        $this->apiSecret ??= config('crosschex.api_secret');
    }

    public function getToken(): string
    {
        // dd($this->baseUrl, $this->apiKey, $this->apiSecret);

        $response = Http::post($this->baseUrl, [
            'header' => [
                'nameSpace'  => 'authorize.token',
                'nameAction' => 'token',
                'version'    => '1.0',
                'requestId'  => uniqid('req-auth-', true),
                'timestamp'  => now()->toIso8601String(),
            ],
            'payload' => [
                'api_key'    => $this->apiKey,
                'api_secret' => $this->apiSecret,
            ],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('CrossChex: error al obtener token (' . $response->status() . ').');
        }

        $token = Arr::get($response->json(), 'payload.token');

        if (! $token) {
            throw new RuntimeException('CrossChex: token no presente en la respuesta.');
        }

        return $token;
    }

    /**
     * @return array<int, array<string,mixed>>
     */
    public function getRecords(string $token, string $beginIso, string $endIso): array
    {
        $response = Http::post($this->baseUrl, [
            'header' => [
                'nameSpace'  => 'attendance.record',
                'nameAction' => 'getrecord',
                'version'    => '1.0',
                'requestId'  => uniqid('req-rec-', true),
                'timestamp'  => now()->toIso8601String(),
            ],
            'authorize' => [
                'type'  => 'token',
                'token' => $token,
            ],
            'payload' => [
                'begin_time' => $beginIso,
                'end_time'   => $endIso,
                'order'      => 'asc',
                'page'       => 1,
                'per_page'   => 100,
            ],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('CrossChex: error al obtener registros (' . $response->status() . ').');
        }

        /** @var array<int, array<string,mixed>> $list */
        $list = Arr::get($response->json(), 'payload.list', []);

        return $list;
    }
}
