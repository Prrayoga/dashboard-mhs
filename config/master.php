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

// Menjalankan query prepared statement, mengembalikan SATU baris (atau null)
// ambil_baris($koneksi, "SELECT ... WHERE id = ?", "i", $id);
function ambil_baris(mysqli $koneksi, string $sql, string $tipe = '', ...$param): ?array
{
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($tipe !== '') {
        mysqli_stmt_bind_param($stmt, $tipe, ...$param);
    }
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}

// Sama, tetapi mengembalikan SEMUA baris sebagai array
function ambil_semua(mysqli $koneksi, string $sql, string $tipe = '', ...$param): array
{
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($tipe !== '') {
        mysqli_stmt_bind_param($stmt, $tipe, ...$param);
    }
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

// Password acak 8 karakter (huruf dan angka heksadesimal) yang aman secara kriptografis
function buat_password_acak(int $panjang = 8): string
{
    return substr(bin2hex(random_bytes($panjang)), 0, $panjang);
}
