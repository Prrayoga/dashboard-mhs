/**
 * =====================================================
 * JAVASCRIPT LAYOUT: hamburger menu & dropdown user
 * =====================================================
 * Semua perubahan tampilan dilakukan dengan MENAMBAH /
 * MENGHAPUS class di elemen. Tampilannya sendiri diatur
 * oleh CSS (assets/css/layout.css), bukan oleh JavaScript.
 * =====================================================
 */
(function () {
  var body = document.body;
  var tombolMenu = document.getElementById("btn-hamburger");
  var overlay = document.getElementById("overlay");
  var tombolUser = document.getElementById("dropdown-user-btn");
  var menuUser = document.getElementById("dropdown-user-menu");
  // Apakah layar sedang berukuran HP/tablet kecil?
  function layarKecil() {
    return window.innerWidth <= 768;
  }
  // --- Hamburger ---
  // Layar lebar : sidebar terbuka secara default -> klik = menutup (sidebar-collapsed)
  // Layar kecil : sidebar tertutup secara default -> klik = membuka (sidebar-open)
  tombolMenu.addEventListener("click", function () {
    if (layarKecil()) {
      body.classList.toggle("sidebar-open");
    } else {
      body.classList.toggle("sidebar-collapsed");
    }
  });
  // Klik area gelap (overlay) di luar sidebar -> sidebar menutup
  overlay.addEventListener("click", function () {
    body.classList.remove("sidebar-open");
  });
  // --- Dropdown user ---
  tombolUser.addEventListener("click", function (e) {
    e.stopPropagation(); // supaya klik ini tidak ikut memicu penutupan di bawah
    menuUser.classList.toggle("tampil");
  });
  // Klik di mana saja di luar dropdown -> dropdown menutup
  document.addEventListener("click", function () {
    menuUser.classList.remove("tampil");
  });
  // Tombol Escape menutup sidebar (layar kecil) dan dropdown
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      body.classList.remove("sidebar-open");
      menuUser.classList.remove("tampil");
    }
  });
  // Jika jendela diperbesar kembali ke ukuran desktop, pastikan
  // sisa status "sidebar-open" dari layar kecil dibersihkan
  window.addEventListener("resize", function () {
    if (!layarKecil()) {
      body.classList.remove("sidebar-open");
    }
  });
})();
