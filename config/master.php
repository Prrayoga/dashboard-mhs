<?php
function daftar_jurusan(mysqli $koneksi): mysqli_result
{
    return mysqli_query($koneksi, "SELECT id, kode_jurusan, nama_jurusan FROM jurusan ORDER BY nama_jurusan");
}

function jurusan_ada(mysqli $koneksi, int $jurusan_id): bool
{
    $stmt = mysqli_prepare($koneksi, "SELECT id FROM jurusan WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $jurusan_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $ada = mysqli_stmt_num_rows($stmt) === 1;
    mysqli_stmt_close($stmt);

    return $ada;
}

function huruf_mutu(?float $nilai): string
{
    if ($nilai === null) {
        return '-';
    }

    if ($nilai >= 85) return 'A';
    if ($nilai >= 75) return 'B';
    if ($nilai >= 60) return 'C';
    if ($nilai >= 50) return 'D';

    return 'E';
}
