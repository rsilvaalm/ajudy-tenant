<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DatajudService
{
    private string $baseUrl = 'https://api-publica.datajud.cnj.jus.br';
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.datajud.key', 'cDZHYzlZa0JadVREZDJCendQbXY6SkJlTzNjLV9TRENyQk1RdnFKZGRQdw==');
    }

    /**
     * Consulta movimentações de um processo pelo número CNJ.
     * Retorna array de movimentações ou null em caso de erro.
     */
    public function getMovements(string $processNumber): ?array
    {
        $clean = $this->cleanNumber($processNumber);
        if (!$clean) return null;

        $endpoint = $this->resolveEndpoint($clean);
        if (!$endpoint) return null;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'APIKey ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->timeout(15)
            ->post("{$this->baseUrl}/{$endpoint}/_search", [
                'query' => [
                    'match' => [
                        'numeroProcesso' => $clean,
                    ],
                ],
            ]);

            if (!$response->successful()) {
                Log::warning('DataJud: resposta não ok', [
                    'status'  => $response->status(),
                    'process' => $processNumber,
                ]);
                return null;
            }

            $data = $response->json();
            $hits = $data['hits']['hits'] ?? [];

            if (empty($hits)) return [];

            $source = $hits[0]['_source'] ?? [];

            // Movimentações vêm em $source['movimentos']
            $movements = $source['movimentos'] ?? [];

            // Ordena por data decrescente
            usort($movements, function ($a, $b) {
                return strcmp($b['dataHora'] ?? '', $a['dataHora'] ?? '');
            });

            return [
                'movements'    => $movements,
                'process_data' => [
                    'classe'          => $source['classe']['nome'] ?? null,
                    'assunto'         => collect($source['assuntos'] ?? [])->pluck('nome')->implode(', '),
                    'orgao_julgador'  => $source['orgaoJulgador']['nome'] ?? null,
                    'tribunal'        => $source['tribunal'] ?? null,
                    'ultima_atualizacao' => $source['dataHoraUltimaAtualizacao'] ?? null,
                ],
            ];

        } catch (\Throwable $e) {
            Log::error('DataJud: erro na consulta', [
                'process' => $processNumber,
                'error'   => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Remove formatação do número CNJ e retorna apenas dígitos.
     * Ex: "1004017-79.2023.4.01.3302" → "10040177920234013302"
     */
    public function cleanNumber(string $number): ?string
    {
        $clean = preg_replace('/[^0-9]/', '', $number);
        if (strlen($clean) !== 20) return null;
        return $clean;
    }

    /**
     * Resolve o alias do endpoint DataJud a partir do número CNJ.
     *
     * Estrutura CNJ: NNNNNNN-DD.AAAA.J.TT.OOOO
     * Posições (sem pontuação, 20 dígitos):
     *   0-6  = sequencial
     *   7-8  = dígito verificador
     *   9-12 = ano
     *   13   = justiça (J)
     *   14-15= tribunal (TT)
     *   16-19= origem (OOOO)
     */
    public function resolveEndpoint(string $cleanNumber): ?string
    {
        $j  = (int) substr($cleanNumber, 13, 1);  // jurisdição
        $tt = (int) substr($cleanNumber, 14, 2);  // tribunal

        $map = $this->getEndpointMap();

        return $map[$j][$tt] ?? null;
    }

    /**
     * Mapa J → TT → alias do endpoint DataJud.
     */
    private function getEndpointMap(): array
    {
        return [
            // Justiça Federal (J=4)
            4 => [
                1  => 'api_publica_trf1',
                2  => 'api_publica_trf2',
                3  => 'api_publica_trf3',
                4  => 'api_publica_trf4',
                5  => 'api_publica_trf5',
                6  => 'api_publica_trf6',
            ],
            // Justiça do Trabalho (J=5)
            5 => [
                1  => 'api_publica_tst',
                2  => 'api_publica_trt2',
                3  => 'api_publica_trt3',
                4  => 'api_publica_trt4',
                5  => 'api_publica_trt5',
                6  => 'api_publica_trt6',
                7  => 'api_publica_trt7',
                8  => 'api_publica_trt8',
                9  => 'api_publica_trt9',
                10 => 'api_publica_trt10',
                11 => 'api_publica_trt11',
                12 => 'api_publica_trt12',
                13 => 'api_publica_trt13',
                14 => 'api_publica_trt14',
                15 => 'api_publica_trt15',
                16 => 'api_publica_trt16',
                17 => 'api_publica_trt17',
                18 => 'api_publica_trt18',
                19 => 'api_publica_trt19',
                20 => 'api_publica_trt20',
                21 => 'api_publica_trt21',
                22 => 'api_publica_trt22',
                23 => 'api_publica_trt23',
                24 => 'api_publica_trt24',
            ],
            // Justiça Eleitoral (J=6)
            6 => [
                1  => 'api_publica_tse',
                2  => 'api_publica_tre-ac',
                3  => 'api_publica_tre-al',
                4  => 'api_publica_tre-ap',
                5  => 'api_publica_tre-am',
                6  => 'api_publica_tre-ba',
                7  => 'api_publica_tre-ce',
                8  => 'api_publica_tre-df',
                9  => 'api_publica_tre-es',
                10 => 'api_publica_tre-go',
                11 => 'api_publica_tre-ma',
                12 => 'api_publica_tre-mt',
                13 => 'api_publica_tre-ms',
                14 => 'api_publica_tre-mg',
                15 => 'api_publica_tre-pa',
                16 => 'api_publica_tre-pb',
                17 => 'api_publica_tre-pr',
                18 => 'api_publica_tre-pe',
                19 => 'api_publica_tre-pi',
                20 => 'api_publica_tre-rn',
                21 => 'api_publica_tre-rs',
                22 => 'api_publica_tre-ro',
                23 => 'api_publica_tre-rr',
                24 => 'api_publica_tre-sc',
                25 => 'api_publica_tre-se',
                26 => 'api_publica_tre-sp',
                27 => 'api_publica_tre-to',
                28 => 'api_publica_tre-rj',
            ],
            // Justiça Estadual (J=8)
            8 => [
                1  => 'api_publica_tjac',
                2  => 'api_publica_tjal',
                3  => 'api_publica_tjap',
                4  => 'api_publica_tjam',
                5  => 'api_publica_tjba',
                6  => 'api_publica_tjce',
                7  => 'api_publica_tjdft',
                8  => 'api_publica_tjes',
                9  => 'api_publica_tjgo',
                10 => 'api_publica_tjma',
                11 => 'api_publica_tjmt',
                12 => 'api_publica_tjms',
                13 => 'api_publica_tjmg',
                14 => 'api_publica_tjpa',
                15 => 'api_publica_tjpb',
                16 => 'api_publica_tjpr',
                17 => 'api_publica_tjpe',
                18 => 'api_publica_tjpi',
                19 => 'api_publica_tjrn',
                20 => 'api_publica_tjrs',
                21 => 'api_publica_tjro',
                22 => 'api_publica_tjrr',
                23 => 'api_publica_tjsc',
                24 => 'api_publica_tjse',
                25 => 'api_publica_tjsp',
                26 => 'api_publica_tjto',
                27 => 'api_publica_tjrj',
            ],
            // STJ (J=3, TT=00)
            3 => [
                0 => 'api_publica_stj',
            ],
            // STF (J=1, TT=00) — ainda sem endpoint público disponível
            1 => [],
            // Justiça Militar (J=7 e J=9)
            7 => [
                1 => 'api_publica_stm',
            ],
        ];
    }
}
