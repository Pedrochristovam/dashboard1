<?php
declare(strict_types=1);

function automation_session_key(): string
{
    return 'automation_requests';
}

function automation_default_requests(): array
{
    return [
        [
            'id' => 1,
            'title' => 'Automatizar consolidação de planilhas mensais',
            'requester' => 'Pedro',
            'sector' => 'gerat',
            'type' => 'automacao_processo',
            'priority' => 'alta',
            'frequency' => 'mensal',
            'activity' => 'Consolidar indicadores recebidos de várias áreas em um único arquivo.',
            'need' => 'Hoje a equipe faz cópia manual de dados, ajustes de fórmula e revisão de inconsistências todos os meses.',
            'expected_result' => 'Gerar um fluxo padronizado com menos retrabalho e mais velocidade para entrega da diretoria.',
            'deadline' => '2026-03-28',
            'status' => 'triagem',
            'created_at' => '2026-03-18 09:10',
        ],
        [
            'id' => 2,
            'title' => 'Painel de acompanhamento de demandas jurídicas',
            'requester' => 'Camila',
            'sector' => 'juridco',
            'type' => 'painel_relatorio',
            'priority' => 'media',
            'frequency' => 'semanal',
            'activity' => 'Acompanhar volume de solicitações, prazos e responsáveis por frente.',
            'need' => 'As informações estão espalhadas em e-mails e planilhas sem uma visão executiva consolidada.',
            'expected_result' => 'Ter um painel simples com status, volumes e alertas de prazo por responsável.',
            'deadline' => '2026-04-05',
            'status' => 'planejado',
            'created_at' => '2026-03-17 16:35',
        ],
        [
            'id' => 3,
            'title' => 'Assistente para responder dúvidas recorrentes de RH',
            'requester' => 'Marina',
            'sector' => 'rh',
            'type' => 'ia_copiloto',
            'priority' => 'alta',
            'frequency' => 'diaria',
            'activity' => 'Responder dúvidas internas sobre benefícios, férias, atestados e políticas.',
            'need' => 'A equipe gasta muito tempo respondendo questões repetidas e consultando documentos internos.',
            'expected_result' => 'Reduzir o tempo de atendimento e liberar a equipe para temas mais estratégicos.',
            'deadline' => '2026-03-30',
            'status' => 'em_execucao',
            'created_at' => '2026-03-18 11:20',
        ],
    ];
}

function automation_ensure_session(): void
{
    app_ensure_session();

    if (!isset($_SESSION[automation_session_key()])) {
        $_SESSION[automation_session_key()] = automation_default_requests();
    }
}

function automation_all(): array
{
    automation_ensure_session();
    return $_SESSION[automation_session_key()];
}

function automation_replace_all(array $requests): void
{
    $_SESSION[automation_session_key()] = array_values($requests);
}
