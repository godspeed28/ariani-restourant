// Smooth scroll untuk navigasi menu
document.querySelectorAll(".nav-link").forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const targetId = this.getAttribute("href").substring(1);
    const targetElement = document.getElementById(targetId);
    if (targetElement) {
      window.scrollTo({
        top: targetElement.offsetTop - 80,
        behavior: "smooth",
      });
    }
  });
});
// Hover effect pada kartu services
document.querySelectorAll(".services .card").forEach((card) => {
  card.addEventListener("mouseenter", function () {
    this.style.transform = "scale(1.05)";
    this.style.transition = "0.3s";
  });
  card.addEventListener("mouseleave", function () {
    this.style.transform = "scale(1)";
  });
});
// Scroll ke atas saat ikon panah di footer di-klik
document
  .querySelector(".bi-arrow-up-circle")
  .addEventListener("click", function (e) {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });

 
  function hapusPesanan(id) {
console.log("Tombol hapus ditekan untuk pesanan ID: " + id); // Cek apakah fungsi terpanggil
var pesananElement = document.getElementById('pesanan-' + id);
if (pesananElement) {
   pesananElement.style.display = 'none'; // Menyembunyikan elemen dari tampilan
}
}
  
