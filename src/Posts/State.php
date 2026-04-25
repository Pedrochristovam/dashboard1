<?php
declare(strict_types=1);

function posts_filtered_items(string $search): array
{
    return array_values(array_filter(
        posts_all(),
        static function (array $post) use ($search): bool {
            if ($search === '') {
                return true;
            }

            $haystack = text_lower(implode(' ', [
                (string) ($post['tool_name'] ?? ''),
                (string) ($post['description'] ?? ''),
                (string) ($post['learning_tips'] ?? ''),
                (string) ($post['prompts_used'] ?? ''),
                (string) ($post['category'] ?? ''),
                (string) ($post['author'] ?? ''),
                (string) ($post['department_label'] ?? ''),
                (string) ($post['status'] ?? ''),
                (string) (($post['document']['name'] ?? '')),
            ]));

            return str_contains($haystack, text_lower($search));
        }
    ));
}

function posts_build_state(): array
{
    posts_handle_preview_request();
    posts_ensure_session();
    [$errors, $formData] = posts_handle_actions();

    $search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
    $categoryFilter = isset($_GET['category']) ? trim((string) $_GET['category']) : '';
    $statusFilter = isset($_GET['status']) ? trim((string) $_GET['status']) : '';
    $departmentFilter = isset($_GET['department']) ? trim((string) $_GET['department']) : '';
    $authorFilter = isset($_GET['author']) ? trim((string) $_GET['author']) : '';
    $editingPost = auth_is_manager() && isset($_GET['edit']) ? posts_find_by_id((int) $_GET['edit']) : null;
    if ($editingPost !== null && $errors === []) {
        $formData = posts_form_data_from_post($editingPost);
    }

    $posts = array_values(array_filter(
        posts_filtered_items($search),
        static function (array $post) use ($categoryFilter, $statusFilter, $departmentFilter, $authorFilter): bool {
            if ($categoryFilter !== '' && (string) ($post['category'] ?? '') !== $categoryFilter) {
                return false;
            }

            if ($statusFilter !== '' && (string) ($post['status'] ?? '') !== $statusFilter) {
                return false;
            }

            if ($departmentFilter !== '' && (string) ($post['department'] ?? '') !== $departmentFilter) {
                return false;
            }

            if ($authorFilter !== '' && (string) ($post['author'] ?? '') !== $authorFilter) {
                return false;
            }

            return true;
        }
    ));
    $allPosts = posts_all();
    $videoPostsCount = count(array_filter(
        $allPosts,
        static fn(array $post): bool => trim((string) ($post['video_url'] ?? '')) !== ''
    ));
    $publishedCount = count(array_filter(
        $allPosts,
        static fn(array $post): bool => (string) ($post['status'] ?? '') === 'published'
    ));
    $draftCount = count(array_filter(
        $allPosts,
        static fn(array $post): bool => (string) ($post['status'] ?? '') === 'draft'
    ));
    $departmentCounts = array_count_values(array_map(
        static fn(array $post): string => (string) ($post['department_label'] ?? 'Nao identificado'),
        $allPosts
    ));
    arsort($departmentCounts);
    $authorOptions = array_values(array_unique(array_map(
        static fn(array $post): string => (string) ($post['author'] ?? ''),
        $allPosts
    )));
    sort($authorOptions);

    return [
        'errors' => $errors,
        'formData' => $formData,
        'search' => $search,
        'categoryFilter' => $categoryFilter,
        'statusFilter' => $statusFilter,
        'departmentFilter' => $departmentFilter,
        'authorFilter' => $authorFilter,
        'posts' => $posts,
        'categoryLabels' => posts_category_labels(),
        'statusLabels' => posts_status_labels(),
        'departmentOptions' => auth_department_options(),
        'authorOptions' => $authorOptions,
        'featuredCount' => count($allPosts),
        'videoPostsCount' => $videoPostsCount,
        'publishedCount' => $publishedCount,
        'draftCount' => $draftCount,
        'created' => isset($_GET['created']),
        'updated' => isset($_GET['updated']),
        'managed' => isset($_GET['managed']),
        'editingPost' => $editingPost,
        'isPublisherOpen' => $errors !== [] || $editingPost !== null,
        'automationContentCount' => count(array_filter(
            $allPosts,
            static fn(array $post): bool => (string) ($post['category'] ?? '') === 'automacao'
        )),
        'topDepartmentName' => $departmentCounts !== [] ? (string) array_key_first($departmentCounts) : 'Sem dados',
        'topDepartmentCount' => $departmentCounts !== [] ? (int) reset($departmentCounts) : 0,
    ];
}
