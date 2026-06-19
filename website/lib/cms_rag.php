<?php

/**
 * Dimensiones RAG sugeridas por observatorio (deben coincidir con filtros en PostgreSQL / Streamlit).
 */
function cms_rag_dimension_options(): array
{
    return [
        'Económica',
        'Social',
        'Ambiental',
        'CTI',
        'Género',
        'General',
    ];
}

function cms_rag_suggested_dimension_for_observatory_id(int $observatoryId): string
{
    return match ($observatoryId) {
        1 => 'Económica',
        2 => 'Social',
        3 => 'Ambiental',
        4 => 'CTI',
        5 => 'Género',
        default => 'General',
    };
}
