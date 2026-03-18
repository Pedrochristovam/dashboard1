<?php
declare(strict_types=1);

function innovation_level_badge(string $level): string
{
    return match (text_lower($level)) {
        'avançado', 'avancado' => 'advanced',
        'intermediário', 'intermediario' => 'intermediate',
        default => 'starter',
    };
}
