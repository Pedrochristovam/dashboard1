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
            'requester_department' => 'gerat',
            'sector' => 'gerat',
            'type' => 'automacao_processo',
            'priority' => 'alta',
            'frequency' => 'mensal',
            'activity' => 'Consolidar indicadores recebidos de várias áreas em um único arquivo.',
            'need' => 'Hoje a equipe faz cópia manual de dados, ajustes de fórmula e revisão de inconsistências todos os meses.',
            'expected_result' => 'Gerar um fluxo padronizado com menos retrabalho e mais velocidade para entrega da diretoria.',
            'deadline' => '2026-03-28',
            'status' => 'triagem',
            'assignee' => 'Equipe de Automação',
            'comments' => [
                [
                    'id' => '1',
                    'author' => 'Equipe de Automação',
                    'message' => 'Demanda recebida e priorizada para avaliação inicial.',
                    'created_at' => '2026-03-18 10:00',
                ],
            ],
            'timeline' => [
                [
                    'id' => '1',
                    'type' => 'status',
                    'label' => 'Status definido como Em triagem',
                    'author' => 'Equipe de Automação',
                    'created_at' => '2026-03-18 10:00',
                ],
            ],
            'created_at' => '2026-03-18 09:10',
        ],
        [
            'id' => 2,
            'title' => 'Painel de acompanhamento de demandas jurídicas',
            'requester' => 'Camila',
            'requester_department' => 'juridco',
            'sector' => 'juridco',
            'type' => 'painel_relatorio',
            'priority' => 'media',
            'frequency' => 'semanal',
            'activity' => 'Acompanhar volume de solicitações, prazos e responsáveis por frente.',
            'need' => 'As informações estão espalhadas em e-mails e planilhas sem uma visão executiva consolidada.',
            'expected_result' => 'Ter um painel simples com status, volumes e alertas de prazo por responsável.',
            'deadline' => '2026-04-05',
            'status' => 'planejado',
            'assignee' => 'BI Corporativo',
            'comments' => [],
            'timeline' => [
                [
                    'id' => '1',
                    'type' => 'status',
                    'label' => 'Status definido como Planejado',
                    'author' => 'BI Corporativo',
                    'created_at' => '2026-03-17 18:00',
                ],
            ],
            'created_at' => '2026-03-17 16:35',
        ],
        [
            'id' => 3,
            'title' => 'Assistente para responder dúvidas recorrentes de RH',
            'requester' => 'Marina',
            'requester_department' => 'rh',
            'sector' => 'rh',
            'type' => 'ia_copiloto',
            'priority' => 'alta',
            'frequency' => 'diaria',
            'activity' => 'Responder dúvidas internas sobre benefícios, férias, atestados e políticas.',
            'need' => 'A equipe gasta muito tempo respondendo questões repetidas e consultando documentos internos.',
            'expected_result' => 'Reduzir o tempo de atendimento e liberar a equipe para temas mais estratégicos.',
            'deadline' => '2026-03-30',
            'status' => 'em_execucao',
            'assignee' => 'Squad IA Interna',
            'comments' => [
                [
                    'id' => '1',
                    'author' => 'Squad IA Interna',
                    'message' => 'Base de políticas e FAQ já está sendo estruturada.',
                    'created_at' => '2026-03-18 12:10',
                ],
            ],
            'timeline' => [
                [
                    'id' => '1',
                    'type' => 'status',
                    'label' => 'Status definido como Em execução',
                    'author' => 'Squad IA Interna',
                    'created_at' => '2026-03-18 12:10',
                ],
            ],
            'created_at' => '2026-03-18 11:20',
        ],
    ];
}

function automation_normalize_comment(array $comment, int $position): array
{
    return [
        'id' => (string) ($comment['id'] ?? (string) ($position + 1)),
        'author' => trim((string) ($comment['author'] ?? 'Equipe')),
        'message' => trim((string) ($comment['message'] ?? '')),
        'created_at' => trim((string) ($comment['created_at'] ?? '')),
    ];
}

function automation_normalize_timeline_item(array $item, int $position): array
{
    return [
        'id' => (string) ($item['id'] ?? (string) ($position + 1)),
        'type' => trim((string) ($item['type'] ?? 'status')),
        'label' => trim((string) ($item['label'] ?? 'Atualização registrada')),
        'author' => trim((string) ($item['author'] ?? 'Equipe')),
        'created_at' => trim((string) ($item['created_at'] ?? '')),
    ];
}

function automation_normalize_request(array $request): array
{
    $sector = trim((string) ($request['sector'] ?? 'outro'));
    if (!array_key_exists($sector, automation_sector_options())) {
        $sector = 'outro';
    }

    $status = trim((string) ($request['status'] ?? 'novo'));
    if (!array_key_exists($status, automation_status_labels())) {
        $status = 'novo';
    }

    $requesterDepartment = trim((string) ($request['requester_department'] ?? $sector));
    if (!array_key_exists($requesterDepartment, automation_sector_options())) {
        $requesterDepartment = 'outro';
    }

    $comments = array_values(array_map(
        'automation_normalize_comment',
        isset($request['comments']) && is_array($request['comments']) ? $request['comments'] : [],
        array_keys(isset($request['comments']) && is_array($request['comments']) ? $request['comments'] : [])
    ));

    $timeline = array_values(array_map(
        'automation_normalize_timeline_item',
        isset($request['timeline']) && is_array($request['timeline']) ? $request['timeline'] : [],
        array_keys(isset($request['timeline']) && is_array($request['timeline']) ? $request['timeline'] : [])
    ));

    if ($timeline === []) {
        $timeline[] = automation_normalize_timeline_item([
            'id' => '1',
            'type' => 'status',
            'label' => 'Solicitação criada',
            'author' => (string) ($request['requester'] ?? 'Equipe'),
            'created_at' => (string) ($request['created_at'] ?? ''),
        ], 0);
    }

    return [
        'id' => (int) ($request['id'] ?? 0),
        'title' => trim((string) ($request['title'] ?? '')),
        'requester' => trim((string) ($request['requester'] ?? '')),
        'requester_department' => $requesterDepartment,
        'requester_department_label' => (string) automation_sector_options()[$requesterDepartment],
        'sector' => $sector,
        'type' => trim((string) ($request['type'] ?? 'automacao_processo')),
        'priority' => trim((string) ($request['priority'] ?? 'media')),
        'frequency' => trim((string) ($request['frequency'] ?? 'sob_demanda')),
        'activity' => trim((string) ($request['activity'] ?? '')),
        'need' => trim((string) ($request['need'] ?? '')),
        'expected_result' => trim((string) ($request['expected_result'] ?? '')),
        'deadline' => trim((string) ($request['deadline'] ?? '')),
        'status' => $status,
        'assignee' => trim((string) ($request['assignee'] ?? 'Não atribuído')),
        'comments' => $comments,
        'timeline' => $timeline,
        'created_at' => trim((string) ($request['created_at'] ?? '')),
    ];
}

function automation_ensure_session(): void
{
    app_ensure_session();

    if (!isset($_SESSION[automation_session_key()])) {
        $_SESSION[automation_session_key()] = automation_default_requests();
    }

    $_SESSION[automation_session_key()] = array_values(array_map('automation_normalize_request', $_SESSION[automation_session_key()]));
}

function automation_all(): array
{
    automation_ensure_session();
    return $_SESSION[automation_session_key()];
}

function automation_replace_all(array $requests): void
{
    $_SESSION[automation_session_key()] = array_values(array_map('automation_normalize_request', $requests));
}
