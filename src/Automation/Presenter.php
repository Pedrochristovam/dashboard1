<?php
declare(strict_types=1);

function automation_sector_options(): array
{
    return [
        'gerat' => 'GERAT',
        'gelic' => 'GELIC',
        'gecov' => 'GECOV',
        'gecre' => 'GECRE',
        'juridco' => 'JURIDCO',
        'sutec' => 'SUTEC',
        'diretoria' => 'DIRETORIA',
        'rh' => 'RH',
        'outro' => 'Outro setor',
    ];
}

function automation_type_options(): array
{
    return [
        'automacao_processo' => 'Automação de processo',
        'integracao_sistemas' => 'Integração entre sistemas',
        'painel_relatorio' => 'Painel ou relatório',
        'ia_copiloto' => 'IA de apoio ou copiloto',
        'ajuda_operacional' => 'Ajuda operacional específica',
        'treinamento' => 'Treinamento ou orientação',
    ];
}

function automation_priority_options(): array
{
    return [
        'baixa' => 'Baixa',
        'media' => 'Média',
        'alta' => 'Alta',
        'critica' => 'Crítica',
    ];
}

function automation_frequency_options(): array
{
    return [
        'sob_demanda' => 'Sob demanda',
        'diaria' => 'Diária',
        'semanal' => 'Semanal',
        'mensal' => 'Mensal',
    ];
}

function automation_status_labels(): array
{
    return [
        'novo' => 'Novo',
        'triagem' => 'Em triagem',
        'planejado' => 'Planejado',
        'em_execucao' => 'Em execução',
        'aguardando_area' => 'Aguardando área',
        'concluido' => 'Concluído',
    ];
}

function automation_status_badge(string $status): string
{
    return match ($status) {
        'triagem' => 'review',
        'planejado' => 'planned',
        'em_execucao' => 'progress',
        'aguardando_area' => 'neutral',
        'concluido' => 'done',
        default => 'new',
    };
}

function automation_priority_badge(string $priority): string
{
    return match ($priority) {
        'critica' => 'critical',
        'alta' => 'high',
        'media' => 'medium',
        default => 'low',
    };
}
