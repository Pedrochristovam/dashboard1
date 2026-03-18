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
                (string) ($post['category'] ?? ''),
                (string) ($post['author'] ?? ''),
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
    $posts = posts_filtered_items($search);
    $allPosts = posts_all();
    $videoPostsCount = count(array_filter(
        $allPosts,
        static fn(array $post): bool => trim((string) ($post['video_url'] ?? '')) !== ''
    ));

    return [
        'errors' => $errors,
        'formData' => $formData,
        'search' => $search,
        'posts' => $posts,
        'categoryLabels' => posts_category_labels(),
        'featuredCount' => count($allPosts),
        'videoPostsCount' => $videoPostsCount,
        'created' => isset($_GET['created']),
        'isPublisherOpen' => $errors !== [],
        'automationContentCount' => count(array_filter(
            $allPosts,
            static fn(array $post): bool => (string) ($post['category'] ?? '') === 'automacao'
        )),
    ];
}
