<?php

namespace App\Infrastructure\Attendance\CrossChex;

use Carbon\Carbon;
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
     * Obtener una página específica
     */
    private function getRecordsPage(Carbon $start, Carbon $end, int $page, string $token): array
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
                'begin_time' => $start->toIso8601String(),
                'end_time'   => $end->toIso8601String(),
                'order'      => 'asc',
                'page'       => $page,
                'per_page'   => 200,
            ],
        ]);

        // Rate limit crosschex: 30 sec entre requests
        if ($response->json('payload.type') === 'FREQUENT_REQUEST') {
            sleep(30);
            return $this->getRecordsPage($start, $end, $page, $token);
        }

        if (! $response->successful()) {
            throw new RuntimeException("CrossChex error (HTTP): " . $response->status());
        }

        // if ($response->json('header.code') !== 0) {
        //     throw new RuntimeException("CrossChex API error: " . $response->json('header.message'));
        // }

        return $response->json();
        /** @var array<int, array<string,mixed>> $list */
        // $list = Arr::get($response->json(), 'payload.list', []);

        // return $list;
    }

    /**
     * Obtener TODAS las páginas entre un intervalo
     */
    public function getAllRecords(Carbon $start, Carbon $end): array
    {
        $token = $this->getToken();

        $all = [];
        $page = 1;

        do {
            $response = $this->getRecordsPage($start, $end, $page, $token);

            $records = Arr::get($response, 'payload.list', []);
            sleep(1);
            
            $all = array_merge($all, $records);

            $pageCount = Arr::get($response, 'payload.pageCount', 1);

            $page++;
        } while ($page <= $pageCount);

        return $all;
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
