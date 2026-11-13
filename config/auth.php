<?php
/**
 * =====================================================
 * HELPER AUTENTIKASI DAN OTORISASI (SESSION + ROLE)
 * =====================================================
 * Pemakaian di awal halaman:
 *     require 'config/auth.php';
 *     wajib_role('admin');         // hanya admin
 *     wajib_role('mahasiswa');     // hanya mahasiswa
 *     wajib_login();               // semua yang sudah login
 * =====================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------- Membaca identitas dari session ----------

function sudah_login()
{
    return isset($_SESSION['user_id']);
}

function role_user(): string
{
    return $_SESSION['role'] ?? '';
}

function adalah_admin(): bool
{
    return role_user() === 'admin';
}

function adalah_mahasiswa(): bool
{
    return role_user() === 'mahasiswa';
}

// ID mahasiswa milik akun yang sedang login (0 jika bukan akun mahasiswa)
function id_mahasiswa_login(): int
{
    return (int) ($_SESSION['mahasiswa_id'] ?? 0);
}

// Halaman beranda sesuai role
function halaman_beranda(): string
{
    return adalah_admin() ? 'dashboard.php' : 'mhs_dashboard.php';
}

// ---------- Penjaga halaman ----------

// Belum login -> dilempar ke halaman login
function wajib_login()
{
    if (!sudah_login()) {
        header("Location: login.php?pesan=belum_login");
        exit;
    }
}

// Harus login DAN rolenya termasuk yang diizinkan, selain itu 403
function wajib_role(string ...$role_diizinkan): void
{
    wajib_login();

    if (!in_array(role_user(), $role_diizinkan, true)) {
        http_response_code(403);
        require __DIR__ . '/../403.php';
        exit;
    }
}