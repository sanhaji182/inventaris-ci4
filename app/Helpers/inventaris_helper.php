<?php

/**
 * Format Rupiah: 1500000 -> "Rp 1.500.000"
 */
function rupiah($nilai): string
{
    return 'Rp ' . number_format((float) $nilai, 0, ',', '.');
}

/**
 * Badge status unit.
 */
function badge_unit(string $status): string
{
    $map = [
        'tersedia' => 'badge-soft-success',
        'terjual'  => 'badge-soft-info',
        'rusak'    => 'badge-soft-danger',
        'hilang'   => 'badge-soft-secondary',
    ];

    return $map[$status] ?? 'badge-soft-secondary';
}
