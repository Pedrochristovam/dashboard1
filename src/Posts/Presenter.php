<?php
declare(strict_types=1);

function posts_category_labels(): array
{
    return [
        '' => 'Todos os Temas',
        'automacao' => 'Automacao',
        'design' => 'Design',
        'texto' => 'Texto & NLP',
        'dados' => 'Dados & BI',
    ];
}

function posts_status_labels(): array
{
    return [
        '' => 'Todos os status',
        'draft' => 'Rascunho',
        'published' => 'Publicado',
        'archived' => 'Arquivado',
    ];
}
