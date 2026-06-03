<?php

namespace App\Data;

use App\Models\Bank;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class BankData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $code,
        public readonly string $type,
        public readonly ?string $flipcode,
        public readonly ?string $espaycode,
        public readonly ?string $linkitacode,
        public readonly ?string $logo
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            name: (string) $normalized['name'],
            code: (string) $normalized['code'],
            type: (string) $normalized['type'],
            flipcode: $normalized['flipcode'],
            espaycode: $normalized['espaycode'],
            linkitacode: $normalized['linkitacode'],
            logo: $normalized['logo'],
        );
    }

    public static function fromModel(Bank $bank): self
    {
        return self::from($bank);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string|null>
     */
    protected static function normalize(array $data): array
    {
        return [
            'name' => trim((string) ($data['name'] ?? '')),
            'code' => trim((string) ($data['code'] ?? '')),
            'type' => trim((string) ($data['type'] ?? 'bank')),
            'flipcode' => isset($data['flipcode']) && $data['flipcode'] !== '' ? trim((string) $data['flipcode']) : null,
            'espaycode' => isset($data['espaycode']) && $data['espaycode'] !== '' ? trim((string) $data['espaycode']) : null,
            'linkitacode' => isset($data['linkitacode']) && $data['linkitacode'] !== '' ? trim((string) $data['linkitacode']) : null,
            'logo' => isset($data['logo']) && $data['logo'] !== '' ? trim((string) $data['logo']) : null,
        ];
    }
}
