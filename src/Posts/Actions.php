<?php
declare(strict_types=1);

function posts_default_form_data(): array
{
    return [
        'post_id' => '',
        'tool_name' => '',
        'description' => '',
        'video_url' => '',
        'tool_url' => '',
        'article_url' => '',
        'learning_tips' => '',
        'prompts_used' => '',
        'category' => 'automacao',
        'status' => 'published',
    ];
}

function posts_redirect_query_from_request(array $extra = []): array
{
    $requestedView = app_active_view(isset($_GET['view']) ? (string) $_GET['view'] : 'posts');
    $query = ['view' => $requestedView];
    foreach (['search', 'category', 'status', 'department', 'author', 'edit', 'tab'] as $key) {
        if (isset($_GET[$key]) && trim((string) $_GET[$key]) !== '') {
            $query[$key] = (string) $_GET[$key];
        }
    }

    return array_merge($query, $extra);
}

function posts_find_by_id(int $postId): ?array
{
    foreach (posts_all() as $post) {
        if ((int) ($post['id'] ?? 0) === $postId) {
            return $post;
        }
    }

    return null;
}

function posts_form_data_from_post(array $post): array
{
    return [
        'post_id' => (string) ($post['id'] ?? ''),
        'tool_name' => (string) ($post['tool_name'] ?? ''),
        'description' => (string) ($post['description'] ?? ''),
        'video_url' => (string) ($post['video_url'] ?? ''),
        'tool_url' => (string) ($post['tool_url'] ?? ''),
        'article_url' => (string) ($post['article_url'] ?? ''),
        'learning_tips' => (string) ($post['learning_tips'] ?? ''),
        'prompts_used' => (string) ($post['prompts_used'] ?? ''),
        'category' => (string) ($post['category'] ?? 'automacao'),
        'status' => (string) ($post['status'] ?? 'published'),
    ];
}

function posts_upload_error_message(int $error): string
{
    return match ($error) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'O arquivo enviado excede o limite permitido pelo servidor.',
        UPLOAD_ERR_PARTIAL => 'O upload do arquivo nao foi concluido. Tente novamente.',
        UPLOAD_ERR_NO_TMP_DIR => 'O servidor nao encontrou a pasta temporaria para upload.',
        UPLOAD_ERR_CANT_WRITE => 'O servidor nao conseguiu gravar o arquivo enviado.',
        UPLOAD_ERR_EXTENSION => 'O upload foi interrompido por uma extensao do PHP.',
        default => 'Nao foi possivel processar o arquivo enviado.',
    };
}

function posts_normalize_upload_name(string $filename, string $fallback): string
{
    $clean = trim(basename($filename));
    return $clean !== '' ? $clean : $fallback;
}

function posts_document_upload(): ?array
{
    $file = $_FILES['support_document'] ?? null;
    return is_array($file) ? $file : null;
}

function posts_photo_uploads(): array
{
    $files = $_FILES['gallery_photos'] ?? null;
    if (!is_array($files) || !isset($files['name']) || !is_array($files['name'])) {
        return [];
    }

    $uploads = [];
    $count = count($files['name']);
    for ($index = 0; $index < $count; $index++) {
        $uploads[] = [
            'name' => (string) ($files['name'][$index] ?? ''),
            'type' => (string) ($files['type'][$index] ?? ''),
            'tmp_name' => (string) ($files['tmp_name'][$index] ?? ''),
            'error' => (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE),
            'size' => (int) ($files['size'][$index] ?? 0),
        ];
    }

    return array_values(array_filter(
        $uploads,
        static fn(array $upload): bool => $upload['error'] !== UPLOAD_ERR_NO_FILE || trim($upload['name']) !== ''
    ));
}

function posts_validate_document_upload(?array $file, array &$errors): bool
{
    if ($file === null || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return false;
    }

    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error !== UPLOAD_ERR_OK) {
        $errors[] = posts_upload_error_message($error);
        return false;
    }

    $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt'];
    $extension = strtolower((string) pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if ($extension === '' || !in_array($extension, $allowedExtensions, true)) {
        $errors[] = 'Anexe um documento em PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX ou TXT.';
        return false;
    }

    if ((int) ($file['size'] ?? 0) > 10 * 1024 * 1024) {
        $errors[] = 'O documento deve ter no maximo 10 MB.';
        return false;
    }

    return true;
}

function posts_validate_photo_uploads(array $uploads, array &$errors): bool
{
    if ($uploads === []) {
        return false;
    }

    if (count($uploads) > 8) {
        $errors[] = 'Anexe no maximo 8 fotos por publicacao.';
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $hasValidUpload = false;

    foreach ($uploads as $upload) {
        $error = (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            $errors[] = posts_upload_error_message($error);
            continue;
        }

        $extension = strtolower((string) pathinfo((string) ($upload['name'] ?? ''), PATHINFO_EXTENSION));
        if ($extension === '' || !in_array($extension, $allowedExtensions, true)) {
            $errors[] = 'As fotos devem estar em JPG, JPEG, PNG, GIF ou WEBP.';
            continue;
        }

        if ((int) ($upload['size'] ?? 0) > 8 * 1024 * 1024) {
            $errors[] = 'Cada foto deve ter no maximo 8 MB.';
            continue;
        }

        $hasValidUpload = true;
    }

    return $hasValidUpload;
}

function posts_store_document_upload(array $file): array
{
    $extension = strtolower((string) pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    $path = app_store_uploaded_file($file, 'uploads/posts/documents', $extension);

    return [
        'name' => posts_normalize_upload_name((string) $file['name'], 'documento.' . $extension),
        'path' => $path,
        'url' => app_public_url($path),
    ];
}

function posts_store_photo_uploads(array $uploads): array
{
    $photos = [];
    foreach ($uploads as $upload) {
        if ((int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            continue;
        }

        $extension = strtolower((string) pathinfo((string) $upload['name'], PATHINFO_EXTENSION));
        $path = app_store_uploaded_file($upload, 'uploads/posts/images', $extension);
        $photos[] = [
            'path' => $path,
            'url' => app_public_url($path),
        ];
    }

    return $photos;
}

function posts_delete_uploads(array $post): void
{
    $document = $post['document'] ?? null;
    if (is_array($document)) {
        app_delete_public_file(isset($document['path']) ? (string) $document['path'] : null);
    }

    foreach ((array) ($post['photos'] ?? []) as $photo) {
        if (is_array($photo)) {
            app_delete_public_file(isset($photo['path']) ? (string) $photo['path'] : null);
        } elseif (is_string($photo)) {
            app_delete_public_file($photo);
        }
    }
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
    $currentUser = auth_current_user();

    if (in_array($action, ['delete', 'update_status', 'create', 'update'], true) && $currentUser === null) {
        return [['Entre com nome, departamento e perfil para gerenciar publicações.'], $formData];
    }

    if (in_array($action, ['delete', 'update_status', 'update'], true) && !auth_is_manager()) {
        return [['A gestão editorial desta área é exclusiva para gestores.'], $formData];
    }

    if ($action === 'delete') {
        $postId = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
        $existingPosts = posts_all();
        foreach ($existingPosts as $existingPost) {
            if ((int) ($existingPost['id'] ?? 0) === $postId) {
                posts_delete_uploads($existingPost);
                break;
            }
        }

        $posts = array_values(array_filter(
            $existingPosts,
            static fn(array $post): bool => (int) $post['id'] !== $postId
        ));
        posts_replace_all($posts);
        analytics_record_event('post_deleted', 'posts', 'post', (string) $postId);

        app_redirect(posts_redirect_query_from_request());
    }

    if ($action === 'update_status') {
        $postId = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
        $status = trim((string) ($_POST['status'] ?? 'published'));
        if (!array_key_exists($status, posts_status_labels())) {
            app_redirect(posts_redirect_query_from_request());
        }

        $posts = posts_all();
        foreach ($posts as &$post) {
            if ((int) ($post['id'] ?? 0) !== $postId) {
                continue;
            }

            $post['status'] = $status;
            break;
        }
        unset($post);

        posts_replace_all($posts);
        analytics_record_event('post_status_changed', 'posts', 'post', (string) $postId, ['status' => $status]);
        app_redirect(posts_redirect_query_from_request(['managed' => '1']));
    }

    $formData = [
        'post_id' => trim((string) ($_POST['post_id'] ?? '')),
        'tool_name' => trim((string) ($_POST['tool_name'] ?? '')),
        'description' => trim((string) ($_POST['description'] ?? '')),
        'video_url' => trim((string) ($_POST['video_url'] ?? '')),
        'tool_url' => trim((string) ($_POST['tool_url'] ?? '')),
        'article_url' => trim((string) ($_POST['article_url'] ?? '')),
        'learning_tips' => trim((string) ($_POST['learning_tips'] ?? '')),
        'prompts_used' => trim((string) ($_POST['prompts_used'] ?? '')),
        'category' => trim((string) ($_POST['category'] ?? 'automacao')),
        'status' => trim((string) ($_POST['status'] ?? 'published')),
    ];

    if ($formData['tool_name'] === '') {
        $errors[] = 'Informe o nome da ferramenta.';
    }

    if ($formData['description'] === '') {
        $errors[] = 'Informe uma descrição detalhada.';
    }

    $documentUpload = posts_document_upload();
    $photoUploads = posts_photo_uploads();
    $hasDocumentUpload = posts_validate_document_upload($documentUpload, $errors);
    $hasPhotoUploads = posts_validate_photo_uploads($photoUploads, $errors);

    if (
        $formData['video_url'] === ''
        && $formData['tool_url'] === ''
        && $formData['article_url'] === ''
        && !$hasDocumentUpload
        && !$hasPhotoUploads
    ) {
        $errors[] = 'Informe pelo menos um recurso: video, ferramenta, artigo, documento ou fotos.';
    }

    if ($formData['video_url'] !== '' && !filter_var($formData['video_url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'Informe uma URL válida para o video.';
    }

    if ($formData['tool_url'] !== '' && !filter_var($formData['tool_url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'Informe uma URL válida para a ferramenta.';
    }

    if ($formData['article_url'] !== '' && !filter_var($formData['article_url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'Informe uma URL válida para a matéria ou referência.';
    }

    $allowedCategories = ['automacao', 'design', 'texto', 'dados'];
    if (!in_array($formData['category'], $allowedCategories, true)) {
        $errors[] = 'Selecione uma categoria válida.';
    }

    if (!array_key_exists($formData['status'], posts_status_labels())) {
        $errors[] = 'Selecione um status válido para a publicação.';
    }

    if ($errors !== []) {
        return [$errors, $formData];
    }

    $preview = $formData['video_url'] !== '' ? get_link_preview($formData['video_url']) : [
        'thumbnail' => '',
        'title' => '',
    ];

    $posts = posts_all();
    $isUpdate = $action === 'update' && $formData['post_id'] !== '';
    $postId = $isUpdate ? (int) $formData['post_id'] : ($posts !== [] ? max(array_column($posts, 'id')) + 1 : 1);
    $existingPost = $isUpdate ? posts_find_by_id($postId) : null;
    $document = null;
    $photos = [];

    try {
        if ($hasDocumentUpload && $documentUpload !== null) {
            $document = posts_store_document_upload($documentUpload);
        }

        if ($hasPhotoUploads) {
            $photos = posts_store_photo_uploads($photoUploads);
        }
    } catch (RuntimeException $exception) {
        if ($document !== null) {
            app_delete_public_file((string) ($document['path'] ?? ''));
        }

        foreach ($photos as $photo) {
            app_delete_public_file((string) ($photo['path'] ?? ''));
        }

        $errors[] = $exception->getMessage();
        return [$errors, $formData];
    }

    $postRecord = [
        'id' => $postId,
        'tool_name' => $formData['tool_name'],
        'description' => $formData['description'],
        'summary' => truncate_text($formData['description'], 125),
        'video_url' => $formData['video_url'],
        'tool_url' => $formData['tool_url'],
        'article_url' => $formData['article_url'],
        'learning_tips' => $formData['learning_tips'],
        'prompts_used' => $formData['prompts_used'],
        'category' => $formData['category'],
        'status' => $formData['status'],
        'author' => (string) ($currentUser['name'] ?? 'Colaborador'),
        'department' => (string) ($currentUser['department'] ?? 'outro'),
        'published_at' => $existingPost !== null ? (string) ($existingPost['published_at'] ?? date('Y-m-d H:i')) : date('Y-m-d H:i'),
        'thumbnail' => $photos !== []
            ? (string) $photos[0]['url']
            : ($preview['thumbnail'] !== '' ? $preview['thumbnail'] : (string) ($existingPost['thumbnail'] ?? 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80')),
        'preview_title' => $preview['title'] !== ''
            ? $preview['title']
            : ($photos !== []
                ? sprintf('Galeria com %d foto%s anexada%s', count($photos), count($photos) === 1 ? '' : 's', count($photos) === 1 ? '' : 's')
                : ($document !== null ? 'Documento complementar anexado ao post' : (string) ($existingPost['preview_title'] ?? ''))),
        'document' => $document ?? ($existingPost['document'] ?? null),
        'photos' => $photos !== [] ? $photos : ($existingPost['photos'] ?? []),
    ];

    if ($isUpdate && $existingPost !== null) {
        if ($document !== null && is_array($existingPost['document'] ?? null)) {
            app_delete_public_file((string) ($existingPost['document']['path'] ?? ''));
        }

        if ($photos !== []) {
            foreach ((array) ($existingPost['photos'] ?? []) as $oldPhoto) {
                if (is_array($oldPhoto)) {
                    app_delete_public_file((string) ($oldPhoto['path'] ?? ''));
                }
            }
        }

        foreach ($posts as &$post) {
            if ((int) ($post['id'] ?? 0) !== $postId) {
                continue;
            }

            $post = $postRecord;
            break;
        }
        unset($post);
    } else {
        array_unshift($posts, $postRecord);
    }

    posts_replace_all($posts);
    analytics_record_event($isUpdate ? 'post_updated' : 'post_created', 'posts', 'post', (string) $postId, [
        'title' => $formData['tool_name'],
        'status' => $formData['status'],
    ]);

    app_redirect(posts_redirect_query_from_request([
        $isUpdate ? 'updated' : 'created' => '1',
        'edit' => '',
    ]));
}
