<?php
declare(strict_types=1);

function posts_session_key(): string
{
    return 'posts';
}

function posts_default_items(): array
{
    return [
        [
            'id' => 1,
            'tool_name' => 'Midjourney v6 Masterclass',
            'description' => 'Aprenda a dominar a nova versão do Midjourney para criar artes ultra-realistas com prompts otimizados para publicidade, branding e campanhas internas.',
            'summary' => 'Domine a nova versão do Midjourney com prompts para branding e campanhas corporativas.',
            'video_url' => 'https://www.youtube.com/watch?v=K4TOrB7at0Y',
            'tool_url' => 'https://www.midjourney.com/',
            'category' => 'design',
            'author' => 'João D.',
            'published_at' => '2026-03-14 10:15',
            'thumbnail' => 'https://img.youtube.com/vi/K4TOrB7at0Y/hqdefault.jpg',
            'preview_title' => 'Guia completo: Como usar IA para Automação em 2024',
        ],
        [
            'id' => 2,
            'tool_name' => 'Integração OpenAI + Make',
            'description' => 'Workflow completo para automatizar atendimento ao cliente via WhatsApp usando GPT-4, roteamento por intenção e respostas contextualizadas em linguagem natural.',
            'summary' => 'Automatize atendimento com GPT-4, WhatsApp e fluxos inteligentes no Make.',
            'video_url' => '',
            'tool_url' => 'https://www.make.com/en',
            'category' => 'automacao',
            'author' => 'Maria I.',
            'published_at' => '2026-03-18 08:40',
            'thumbnail' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=900&q=80',
            'preview_title' => '',
        ],
        [
            'id' => 3,
            'tool_name' => 'Análise de Dados com PandasAI',
            'description' => 'Converse com seus DataFrames e conecte análises a scripts Python para gerar insights rapidamente em cenários de dados comerciais e operacionais.',
            'summary' => 'Use linguagem natural para consultar DataFrames e acelerar análises internas.',
            'video_url' => '',
            'tool_url' => 'https://github.com/Sinaptik-AI/pandas-ai',
            'category' => 'dados',
            'author' => 'Ricardo B.',
            'published_at' => '2026-03-17 17:05',
            'thumbnail' => 'https://images.unsplash.com/photo-1527430253228-e93688616381?auto=format&fit=crop&w=900&q=80',
            'preview_title' => '',
        ],
        [
            'id' => 4,
            'tool_name' => 'Prompt Engineering Avançado',
            'description' => 'Vídeo aula sobre as melhores técnicas de few-shot, chain-of-thought e estruturação de contexto para extrair o máximo de modelos generativos em texto.',
            'summary' => 'Boas práticas de prompt engineering para obter respostas mais consistentes e úteis.',
            'video_url' => 'https://www.youtube.com/watch?v=3i1lNJPY-4Q',
            'tool_url' => '',
            'category' => 'texto',
            'author' => 'Ana M.',
            'published_at' => '2026-03-16 13:20',
            'thumbnail' => 'https://img.youtube.com/vi/3i1lNJPY-4Q/hqdefault.jpg',
            'preview_title' => 'Vídeo aula sobre prompt engineering avançado',
        ],
    ];
}

function posts_normalize_item(array $post): array
{
    if (!isset($post['video_url'], $post['tool_url'])) {
        $legacyUrl = trim((string) ($post['url'] ?? ''));
        $isYoutube = $legacyUrl !== '' && parse_youtube_video_id($legacyUrl) !== null;
        $post['video_url'] = $isYoutube ? $legacyUrl : '';
        $post['tool_url'] = $isYoutube ? '' : $legacyUrl;
    }

    if (!isset($post['summary']) || trim((string) $post['summary']) === '') {
        $post['summary'] = truncate_text((string) ($post['description'] ?? ''), 125);
    }

    if (!isset($post['thumbnail']) || trim((string) $post['thumbnail']) === '') {
        $preview = get_link_preview((string) $post['video_url']);
        $post['thumbnail'] = $preview['thumbnail'] !== ''
            ? $preview['thumbnail']
            : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80';
    }

    if (!isset($post['preview_title'])) {
        $post['preview_title'] = '';
    }

    return $post;
}

function posts_ensure_session(): void
{
    app_ensure_session();

    if (!isset($_SESSION[posts_session_key()])) {
        $_SESSION[posts_session_key()] = posts_default_items();
    }

    $_SESSION[posts_session_key()] = array_values(array_map('posts_normalize_item', $_SESSION[posts_session_key()]));
}

function posts_all(): array
{
    posts_ensure_session();
    return $_SESSION[posts_session_key()];
}

function posts_replace_all(array $posts): void
{
    $_SESSION[posts_session_key()] = array_values(array_map('posts_normalize_item', $posts));
}
