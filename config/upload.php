<?php

/**
 * =====================================================
 * HELPER UPLOAD FOTO
 * =====================================================
 * Logic upload ditaruh di satu file supaya create.php dan
 * edit.php tidak perlu menulis ulang kode yang sama
 * (prinsip DRY, seperti koneksi.php dan auth.php).
 * =====================================================
 */
// Folder tujuan upload (satu tingkat di atas folder config/)
define('FOLDER_UPLOAD', __DIR__ . '/../uploads/');
/**
 * Memproses satu file foto dari $_FILES['foto'].
 *
 * @param array $file isi $_FILES['foto']
 * @param string $error diisi pesan error (by reference) jika gagal
 * @return string|null nama file baru jika sukses,
 * null jika tidak ada file ATAU jika gagal
 * (bedakan lewat isi $error: kosong = tidak ada file)
 */
function upload_foto(array $file, string &$error): ?string
{
    // 1. Tidak ada file yang dipilih -> BUKAN error, foto memang opsional
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    // 2. Ada masalah saat PHP menerima file
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        $error = "Ukuran file melebihi batas yang diizinkan server.";
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Upload gagal (kode error " . $file['error'] . ").";
        return null;
    }
    // 3. Cek ukuran (maksimal 2 MB, satuan byte)
    $ukuran_maks = 2 * 1024 * 1024;
    if ($file['size'] > $ukuran_maks) {
        $error = "Ukuran foto maksimal 2 MB.";
        return null;
    }
    // 4. Cek ekstensi nama file
    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ekstensi, ['jpg', 'jpeg', 'png'])) {
        $error = "Foto harus berformat JPG, JPEG, atau PNG.";
        return null;
    }
    // 5. Cek ISI file benar-benar gambar. Ekstensi mudah dipalsukan
    // (misal file .php diganti namanya menjadi .jpg), sedangkan
    // getimagesize() membaca isi file dan mengembalikan false
    // jika bukan gambar.
    $info = @getimagesize($file['tmp_name']);
    if ($info === false || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
        $error = "File yang diunggah bukan gambar JPG/PNG yang valid.";
        return null;
    }
    // 6. Buat nama file BARU yang unik. Nama asli dari pengguna
    // tidak dipakai (bisa mengandung karakter berbahaya, atau
    // bentrok dengan file orang lain yang namanya sama).
    $nama_baru = 'mhs_' . uniqid() . '.' . $ekstensi;
    // 7. Pindahkan dari folder sementara PHP ke folder uploads/
    if (!move_uploaded_file($file['tmp_name'], FOLDER_UPLOAD . $nama_baru)) {
        $error = "Gagal menyimpan file ke folder uploads. Pastikan folder uploads ada.";
        return null;
    }
    return $nama_baru;
}
/**
 * Menghapus file foto dari folder uploads/ (jika ada).
 * basename() memastikan hanya nama file yang dipakai, sehingga
 * tidak mungkin menghapus file di luar folder uploads/.
 */
function hapus_foto(?string $nama_file): void
{
    if (empty($nama_file)) {
        return;
    }
    $path = FOLDER_UPLOAD . basename($nama_file);
    if (file_exists($path)) {
        unlink($path);
    }
}


// ===== DOKUMEN (PDF, Word, Excel, TXT) =====

define('FOLDER_DOKUMEN', __DIR__ . '/../uploads/dokumen/');

const EKSTENSI_DOKUMEN = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];

/**
 * Memeriksa isi file melalui byte-byte awalnya (signature).
 * Ekstensi mudah dipalsukan, sedangkan signature lebih sulit.
 */
function cek_isi_dokumen(string $path, string $ekstensi): bool
{
    $awal = (string) file_get_contents($path, false, null, 0, 8);

    switch ($ekstensi) {
        case 'pdf':
            // PDF selalu diawali %PDF
            return strncmp($awal, '%PDF', 4) === 0;

        case 'docx':
        case 'xlsx':
            // Format baru Office = file ZIP, diawali "PK"
            return strncmp($awal, "PK\x03\x04", 4) === 0;

        case 'doc':
        case 'xls':
            // Format lama Office (OLE)
            return $awal === "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";

        case 'txt':
            // Teks biasa tidak boleh berisi byte NUL
            $isi = (string) file_get_contents($path, false, null, 0, 4096);

            return strpos($isi, "\0") === false;
    }

    return false;
}

/**
 * Mengunggah dokumen dengan validasi ekstensi, ukuran, dan isi file.
 */
function upload_dokumen(array $file, string &$error): ?string
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // Dokumen opsional
    }

    if (
        $file['error'] === UPLOAD_ERR_INI_SIZE ||
        $file['error'] === UPLOAD_ERR_FORM_SIZE
    ) {
        $error = "Ukuran dokumen melebihi batas yang diizinkan server.";
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Upload dokumen gagal (kode error " . $file['error'] . ").";
        return null;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $error = "Ukuran dokumen maksimal 5 MB.";
        return null;
    }

    $ekstensi = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    if (!in_array($ekstensi, EKSTENSI_DOKUMEN, true)) {
        $error = "Dokumen harus berformat PDF, Word (doc/docx), Excel (xls/xlsx), atau TXT.";
        return null;
    }

    if (!cek_isi_dokumen($file['tmp_name'], $ekstensi)) {
        $error = "Isi file tidak sesuai dengan ekstensinya (file rusak atau bukan dokumen asli).";
        return null;
    }

    $nama_baru = 'dok_' . uniqid() . '.' . $ekstensi;

    if (!move_uploaded_file(
        $file['tmp_name'],
        FOLDER_DOKUMEN . $nama_baru
    )) {
        $error = "Gagal menyimpan dokumen. Pastikan folder uploads/dokumen ada.";
        return null;
    }

    return $nama_baru;
}

/**
 * Menghapus dokumen berdasarkan nama file.
 */
function hapus_dokumen(?string $nama_file): void
{
    if (empty($nama_file)) {
        return;
    }

    $path = FOLDER_DOKUMEN . basename($nama_file);

    if (file_exists($path)) {
        unlink($path);
    }
}
