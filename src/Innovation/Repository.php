<?php
declare(strict_types=1);

function innovation_session_key(): string
{
    return 'innovation_data';
}

function innovation_period_labels(): array
{
    return [
        'mensal' => 'Mensal',
        'trimestral' => 'Trimestral',
    ];
}

function innovation_default_dataset(): array
{
    $baseSectors = [
        ['id' => 'gerat', 'name' => 'GERAT'],
        ['id' => 'gelic', 'name' => 'GELIC'],
        ['id' => 'gecov', 'name' => 'GECOV'],
        ['id' => 'gecre', 'name' => 'GECRE'],
        ['id' => 'juridco', 'name' => 'JURIDCO'],
        ['id' => 'sutec', 'name' => 'SUTEC'],
        ['id' => 'diretoria', 'name' => 'DIRETORIA'],
        ['id' => 'rh', 'name' => 'RH'],
    ];

    $buildPeriod = static function () use ($baseSectors): array {
        return array_map(static function (array $sector): array {
            return [
                'id' => $sector['id'],
                'name' => $sector['name'],
                'level' => 'Iniciante',
                'progress' => 0,
                'initiatives' => 0,
                'updated_at' => '',
                'trend' => [0, 0, 0, 0, 0],
                'attention' => true,
                'summary' => innovation_default_summary(),
                'highlights' => ['Aguardando mapeamento', 'Sem documentos salvos', 'Necessita acompanhamento'],
                'documents' => [],
            ];
        }, $baseSectors);
    };

    return [
        'mensal' => $buildPeriod(),
        'trimestral' => $buildPeriod(),
    ];
}

function innovation_default_summary(): string
{
    return 'Sem dados lançados até o momento para este setor. Use esta visão para acompanhar a evolução assim que as iniciativas começarem a ser registradas.';
}

function innovation_document_progress_step(): int
{
    return 10;
}

function innovation_normalize_document(array $document, int $position): array
{
    $label = trim((string) ($document['label'] ?? ''));
    if ($label === '') {
        $label = 'Documento ' . ($position + 1);
    }

    return [
        'id' => (string) ($document['id'] ?? (string) ($position + 1)),
        'label' => $label,
        'content' => trim((string) ($document['content'] ?? '')),
        'created_at' => trim((string) ($document['created_at'] ?? '')),
        'updated_at' => trim((string) ($document['updated_at'] ?? '')),
    ];
}

function innovation_progress_from_documents(array $documents): int
{
    return min(100, count($documents) * innovation_document_progress_step());
}

function innovation_trend_from_documents(array $documents): array
{
    $trend = array_map(
        static fn(int $index): int => min(100, ($index + 1) * innovation_document_progress_step()),
        array_keys($documents)
    );

    $trend = array_slice($trend, -5);
    return array_slice(array_pad($trend, 5, 0), -5);
}

function innovation_progress_level(int $progress): string
{
    if ($progress >= 70) {
        return 'Avançado';
    }

    if ($progress >= 35) {
        return 'Intermediário';
    }

    return 'Iniciante';
}

function innovation_normalize_sector(array $sector): array
{
    $summary = trim((string) ($sector['summary'] ?? ''));
    $documents = isset($sector['documents']) && is_array($sector['documents']) ? $sector['documents'] : [];
    $documents = array_values(array_map(
        static fn(array $document, int $index): array => innovation_normalize_document($document, $index),
        $documents,
        array_keys($documents)
    ));

    if ($documents === [] && $summary !== '' && $summary !== innovation_default_summary()) {
        $documents[] = innovation_normalize_document([
            'id' => '1',
            'label' => 'Documento 1',
            'content' => $summary,
            'created_at' => date('Y-m-d H:i'),
            'updated_at' => date('Y-m-d H:i'),
        ], 0);
    }

    $progress = innovation_progress_from_documents($documents);
    $trend = innovation_trend_from_documents($documents);
    $latestDocument = $documents !== [] ? $documents[array_key_last($documents)] : null;

    $highlights = $documents !== []
        ? array_slice(array_map(static fn(array $document): string => (string) $document['label'], $documents), -3)
        : ['Aguardando mapeamento', 'Sem documentos salvos', 'Necessita acompanhamento'];

    return [
        'id' => (string) ($sector['id'] ?? ''),
        'name' => (string) ($sector['name'] ?? ''),
        'level' => innovation_progress_level($progress),
        'progress' => $progress,
        'initiatives' => count($documents),
        'updated_at' => (string) ($latestDocument['updated_at'] ?? ''),
        'trend' => $trend,
        'attention' => $progress < 50,
        'summary' => $latestDocument !== null && trim((string) $latestDocument['content']) !== ''
            ? trim((string) $latestDocument['content'])
            : innovation_default_summary(),
        'highlights' => $highlights,
        'documents' => $documents,
    ];
}

function innovation_ensure_session(): void
{
    app_ensure_session();

    if (!isset($_SESSION[innovation_session_key()]) || !is_array($_SESSION[innovation_session_key()])) {
        $_SESSION[innovation_session_key()] = innovation_default_dataset();
    }

    foreach (innovation_period_labels() as $period => $_label) {
        $periodData = $_SESSION[innovation_session_key()][$period] ?? [];
        if (!is_array($periodData)) {
            $periodData = [];
        }

        $_SESSION[innovation_session_key()][$period] = array_values(array_map(
            'innovation_normalize_sector',
            $periodData
        ));
    }
}

function innovation_period_data(string $period): array
{
    innovation_ensure_session();
    return $_SESSION[innovation_session_key()][$period] ?? [];
}

function innovation_replace_period_data(string $period, array $sectors): void
{
    $_SESSION[innovation_session_key()][$period] = array_values(array_map('innovation_normalize_sector', $sectors));
}
