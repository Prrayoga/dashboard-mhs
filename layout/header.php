<?php
$judul_halaman = $judul_halaman ?? 'Halaman';
$menu_aktif = $menu_aktif ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <!-- viewport WAJIB ada agar tampilan responsif di HP -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($judul_halaman) ?> - SIMAHASISWA</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/css/layout.css">
</head>

<body>
    <div class="app">
        <?php require __DIR__ . '/sidebar.php'; ?>
        <!-- Lapisan gelap di belakang sidebar (hanya muncul di layar HP) -->
        <div class="overlay" id="overlay"></div>
        <div class="main">
            <?php require __DIR__ . '/navbar.php'; ?>
            <main class="content">
                <div class="page-header">
                    <h1><?= htmlspecialchars($judul_halaman) ?></h1>
                    <div class="breadcrumb">
                        <a href="dashboard.php">Home</a> / <?= htmlspecialchars($judul_halaman) ?>
                    </div>
                </div>