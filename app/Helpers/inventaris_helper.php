<?php

if (! function_exists('rupiah')) {
    function rupiah($angka): string
    {
        return 'Rp ' . number_format((float) $angka, 0, ',', '.');
    }
}

if (! function_exists('hitung_margin')) {
    /**
     * Hitung selisih laba/rugi dan persentase keuntungan terhadap harga beli (modal)
     */
    function hitung_margin(float $hargaBeli, float $hargaJual): array
    {
        $selisih = $hargaJual - $hargaBeli;
        $persen = ($hargaBeli > 0) ? ($selisih / $hargaBeli) * 100 : 0;

        return [
            'selisih' => $selisih,
            'persen'  => round($persen, 1),
            'status'  => ($selisih >= 0) ? 'untung' : 'rugi',
        ];
    }
}

if (! function_exists('badge_margin')) {
    function badge_margin(float $hargaBeli, float $hargaJual): string
    {
        $res = hitung_margin($hargaBeli, $hargaJual);
        if ($res['selisih'] >= 0) {
            return '<span class="badge badge-soft-success font-monospace">+' . rupiah($res['selisih']) . ' (+' . $res['persen'] . '%)</span>';
        }
        return '<span class="badge badge-soft-danger font-monospace">' . rupiah($res['selisih']) . ' (' . $res['persen'] . '%)</span>';
    }
}
