<?php
declare(strict_types=1);

function posts_default_form_data(): array
{
    return [
        'tool_name' => '',
        'description' => '',
        'video_url' => '',
        'tool_url' => '',
        'category' => 'automacao',
    ];
}

function posts_handle_preview_request(): void
{
    if (!isset($_GET['preview'])) {
        return;
    }

    header('Content-Type: application/json; charset=utf-8');
    $previewUrl = isset($_GET['url']) ? (string) $_GET['url'] : '';
    echo json_encode(get_link_preview($previewUrl), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function posts_handle_actions(): array
{
    $errors = [];
    $formData = posts_default_form_data();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return [$errors, $formData];
    }

    $dashboardContext = isset($_POST['dashboard_context']) ? (string) $_POST['dashboard_context'] : 'posts';
    if ($dashboardContext !== 'posts') {
        return [$errors, $formData];
    }

    $action = isset($_POST['action']) ? (string) $_POST['action'] : 'create';

    if ($action === 'delete') {
        $postId = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
        $posts = array_values(array_filter(
            posts_all(),
            static fn(array $post): bool => (int) $post['id'] !== $postId
        ));
        posts_replace_all($posts);

        $redirectQuery = ['view' => 'posts'];
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $redirectQuery['search'] = (string) $_GET['search'];
        }

        app_redirect($redirectQuery);
    }

    $formData = [
        'tool_name' => trim((string) ($_POST['tool_name'] ?? '')),
        'description' => trim((string) ($_POST['description'] ?? '')),
        'video_url' => trim((string) ($_POST['video_url'] ?? '')),
        'tool_url' => trim((string) ($_POST['tool_url'] ?? '')),
        'category' => trim((string) ($_POST['category'] ?? 'automacao')),
    ];

    if ($formData['tool_name'] === '') {
        $errors[] = 'Informe o nome da ferramenta.';
    }

    if ($formData['description'] === '') {
        $errors[] = 'Informe uma descrição detalhada.';
    }

    if ($formData['video_url'] === '' && $formData['tool_url'] === '') {
        $errors[] = 'Informe pelo menos um link: video ou ferramenta.';
    }

    if ($formData['video_url'] !== '' && !filter_var($formData['video_url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'Informe uma URL válida para o video.';
    }

    if ($formData['tool_url'] !== '' && !filter_var($formData['tool_url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'Informe uma URL válida para a ferramenta.';
    }

    $allowedCategories = ['automacao', 'design', 'texto', 'dados'];
    if (!in_array($formData['category'], $allowedCategories, true)) {
        $errors[] = 'Selecione uma categoria válida.';
    }

    if ($errors !== []) {
        return [$errors, $formData];
    }

    $preview = $formData['video_url'] !== '' ? get_link_preview($formData['video_url']) : [
        'thumbnail' => '',
        'title' => '',
    ];

    $posts = posts_all();
    $nextId = $posts !== [] ? max(array_column($posts, 'id')) + 1 : 1;

    array_unshift($posts, [
        'id' => $nextId,
        'tool_name' => $formData['tool_name'],
        'description' => $formData['description'],
        'summary' => truncate_text($formData['description'], 125),
        'video_url' => $formData['video_url'],
        'tool_url' => $formData['tool_url'],
        'category' => $formData['category'],
        'author' => 'Pedro',
        'published_at' => date('Y-m-d H:i'),
        'thumbnail' => $preview['thumbnail'] !== '' ? $preview['thumbnail'] : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80',
        'preview_title' => $preview['title'],
    ]);

    posts_replace_all($posts);

    app_redirect([
        'view' => 'posts',
        'created' => '1',
    ]);
}
