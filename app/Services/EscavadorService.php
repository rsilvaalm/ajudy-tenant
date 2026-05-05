<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EscavadorService
{
    private string $baseUrl = 'https://api.escavador.com/api/v1';

    /**
     * Retorna a chave do Escavador do tenant atual,
     * buscando na tabela tenants do landlord.
     */
    private function getApiKey(): ?string
    {
        $tenantId = tenant('id');

        $key = DB::connection('landlord')
            ->table('tenants')
            ->where('id', $tenantId)
            ->value('escavador_api_key');

        return !empty($key) ? $key : null;
    }

    /**
     * Verifica se o tenant está configurado e habilitado.
     */
    public function isConfigured(): bool
    {
        return !empty($this->getApiKey());
    }

    /**
     * Busca publicações de um processo pelo número CNJ.
     * Retorna array com ['publications' => [...], 'credits_used' => int]
     * ou lança exceção em caso de erro.
     */
    public function getPublications(string $processNumber): array
    {
        $apiKey = $this->getApiKey();

        if (!$apiKey) {
            throw new \RuntimeException('Token do Escavador não configurado para este tenant. Configure no painel do Ajudy.');
        }

        $clean = preg_replace('/[^0-9.\-\/]/', '', trim($processNumber));

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept'        => 'application/json',
            ])
            ->timeout(20)
            ->get("{$this->baseUrl}/diarios/busca", [
                'q'        => $clean,
                'per_page' => 50,
            ]);

            // Créditos consumidos nesta requisição
            $creditsUsed = (int) ($response->header('Creditos-Utilizados') ?? 1);

            if ($response->status() === 401) {
                throw new \RuntimeException('Token do Escavador inválido ou expirado. Verifique no painel do Ajudy.');
            }

            if ($response->status() === 402) {
                throw new \RuntimeException('Créditos insuficientes no token do Escavador.');
            }

            if ($response->status() === 429) {
                throw new \RuntimeException('Limite de requisições atingido. Tente novamente em alguns minutos.');
            }

            if (!$response->successful()) {
                Log::warning('Escavador: resposta não ok', [
                    'tenant'  => tenant('id'),
                    'status'  => $response->status(),
                    'process' => $processNumber,
                    'body'    => $response->body(),
                ]);
                throw new \RuntimeException('Erro ao consultar o Escavador (HTTP ' . $response->status() . ').');
            }

            $data  = $response->json();
            $items = $data['items'] ?? $data['data'] ?? [];

            $publications = collect($items)->map(function ($item) {
                return [
                    'external_id'      => (string) ($item['id'] ?? ''),
                    'publication_date' => $item['data'] ?? $item['date'] ?? null,
                    'source'           => $item['fonte'] ?? $item['source'] ?? null,
                    'diario'           => $item['nome_diario'] ?? $item['diario'] ?? null,
                    'caderno'          => $item['nome_caderno'] ?? $item['caderno'] ?? null,
                    'content'          => $item['texto'] ?? $item['content'] ?? $item['snippet'] ?? '',
                ];
            })->filter(fn($p) => !empty($p['content']))->values()->toArray();

            return [
                'publications' => $publications,
                'credits_used' => $creditsUsed,
                'total'        => count($publications),
            ];

        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Escavador: erro inesperado', [
                'tenant'  => tenant('id'),
                'process' => $processNumber,
                'error'   => $e->getMessage(),
            ]);
            throw new \RuntimeException('Erro inesperado ao consultar publicações.');
        }
    }
}
