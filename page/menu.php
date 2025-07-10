<?php

require '../functions.php';

session_start();

// cek cookie
if (isset($_COOKIE['id']) && isset($_COOKIE['key'])) {
    $_SESSION['role'] = true;
}
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location: ../post_logout.php');
    exit();
}
if (isset($_SESSION['midtrans_success']) && $_SESSION['midtrans_success']) {
    unset($_SESSION['midtrans_success']);
    echo "<script>alert('Pembayaran berhasil! Terima kasih telah berbelanja.');</script>";
}

$menu = query("SELECT * FROM menu ORDER BY id DESC");

$user_pesanan = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT * FROM sales_report WHERE user_id = ? AND delete_at IS NULL ORDER BY id DESC");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_pesanan = $result->fetch_all(MYSQLI_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Link untuk Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="../img/garpu.jpg" />
    <link rel="stylesheet" href="../css/style.css">
    <title>Menu - Aryani GO</title>
</head>
<style>
    .text-right-padding {
        padding-right: 100px;
    }

    #services img {
        height: 150px;
    }

    .href-none {
        text-decoration: none;
        color: inherit;
    }

    .text-custom {
        text-decoration: none;
        color: darkgrey;
        padding-right: 10px;
        transition: color 0.3s ease;
    }

    .text-custom:hover {
        color: #ccc;
    }

    nav ul li a {
        text-decoration: none;
        color: black;
        transition: color 0.3s ease;
    }

    nav ul li a.active {
        color: white;
        font-weight: bold;
    }

    .gambar {
        width: 200px;
        height: auto;
        position: relative;
        animation: bounceFromTop 1s ease forwards;
    }

    @keyframes bounceFromTop {
        0% {
            transform: translateY(-200px);
            opacity: 0;
        }

        40% {
            transform: translateY(0);
            opacity: 1;
        }

        70% {
            transform: translateY(-20px);
        }

        100% {
            transform: translateY(0);
        }
    }

    .cart-icon {
        position: relative;
        display: inline-block;
    }

    #cart-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        padding: 5px;
        border-radius: 50%;
    }

    .padding {
        padding-top: 0px;
        padding-bottom: 0px;
    }

    @media (max-width: 768px) {
        .padding {
            padding-top: 0px;
            padding-bottom: 15px;

        }
    }
</style>

<body class="animate__animated animate__fadeIn animate__delay-0.5s">
    <!-- Start Navbar -->
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark padding sticky-top">
        <div class="container-fluid">
            <p style="font-weight:bold;margin-top: 18px;"> <a class="navbar-brand custom-font">&nbsp;&nbsp;<img src="../img/garpu.jpg" alt="" style="width:50px; color:white; border-radius:50%;" class="gambar"> &nbsp;&nbsp;Aryani GO </a></p>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navmenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="margin-left: 925px;
    margin-top: 7px;">
                    <li><a href="add_cart.php" style="padding-right:10px;">
                            <div class="cart-icon" style=" margin-top: -1px;">
                                <h5><i class="bi bi-cart" style="color:orange;"></i></h5>
                                <span id="cart-quantity" class="badge bg-danger"> <?= $total_items; ?></span>
                            </div>
                        </a></li>&nbsp;
                    <li class="nav-item">
                        <a href="../index.php" class="text-custom">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="text-custom" href="#contact">Contact</a>
                    </li>

                </ul>
                <form class="d-flex" role="search">
                    <input type="search" id="search" placeholder="Cari menu" class="form-control me-3" autofocus>
                </form>
                <style>
                    /* Media query untuk layar kecil */
                    @media (max-width: 768px) {
                        .navbar-brand {
                            font-size: 18px;
                            margin-left: 0;
                        }

                        .navbar-nav {
                            margin-left: 0 !important;
                            margin-top: 15px !important;
                            text-align: start;
                        }

                        .navbar-nav .nav-item {
                            margin-bottom: 45px;
                            margin-top: -40px;
                        }

                        .cart-icon {
                            display: flex;
                            justify-content: start;
                            align-items: start;
                        }

                        #search {
                            width: 100%;
                            margin-top: -40px;
                        }
                    }
                </style>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->
    <!-- Start Menu -->
    <section id="Menu" data-aos="fade-right" data-aos-duration="1000">
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-start">
                    <h5 class="section-title ff-secondary text-warning fw-normal">Food <i class="fas fa-bars"></i> Menu</h5>
                    <br>
                </div>
                <div class="row">
                    <?php $i = 1; ?>
                    <?php foreach ($menu as $row) : ?>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card shadow-sm border-0 rounded-3" style="overflow: hidden; transition: transform 0.3s ease-in-out; font-size: 0.9rem;">
                                <!-- Gambar Menu -->
                                <img src="../img/<?= $row['gambar'] ?>" class="card-img-top" style="height: 150px; object-fit: cover; transition: transform 0.3s ease;" alt="<?= $row['nama'] ?>">
                                <div class="card-body p-2">
                                    <!-- Nama Menu -->
                                    <h6 class="card-title text-start font-weight-bold mb-1" style="color: #333;"><?= $row['nama'] ?></h6>
                                    <!-- Harga -->
                                    <p class="card-text text-start text-primary mb-1">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                                    <!-- Stok -->
                                    <p class="card-text text-muted mb-2"><strong>Stok:</strong> <?= $row['stok'] ?></p>
                                    <!-- Form untuk Menambah ke Keranjang -->
                                    <form action='add_cart.php' method='POST' class="d-flex align-items-center">
                                        <input type='hidden' name='id_produk' value="<?= $row['id'] ?>">
                                        <input type='number' name='kuantitas' value='1' min='1' max='<?= $row['stok'] ?>' class="form-control form-control-sm me-2" style="width: 60px;">
                                        <input type="hidden" name="stok" value="<?= $row['stok'] ?>">
                                        <button type="submit" class="btn btn-dark btn-sm">Add to <i class="fas fa-cart-plus"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
        </div>
    </section>
    <!-- End Menu -->
    <?php

    $query = "SELECT * FROM ads WHERE is_active = 1";
    $result = $conn->query($query);
    $currentDate = new DateTime();
    ?>

    <?php while ($row = $result->fetch_assoc()) : ?>
        <div class="ad-container" data-aos="fade-right" data-aos-duration="1000">
            <div class="ad-content" data-aos="fade-up" data-aos-duration="2000">

                <div class="snow"></div>
                <img src="../img/<?= $row['image_url'] ?>" alt="<?= $row['title'] ?>" class="ad-image" style="border-radius:20px; padding:2px;border: 2px solid red;">
                <h2 class="ad-title" style="padding-top: 10px;"><?= $row['title'] ?></h2>
                <p class="ad-text"><?= $row['content'] ?></p>
                <p class="ad-date">
                    🎄 Ends on: <span class="date"><?= $row['end_date'] ?></span>
                </p>
                <p class="ad-timer" id="timer-<?= $row['id'] ?>">⏳ Countdown: <span id="time-left-<?= $row['id'] ?>"></span></p>

            </div>
        </div>
    <?php endwhile; ?>
    <style>
        .depan {
            z-index: 9999;
        }
    </style>
    <!-- Icon Pesanan -->
    <div class="position-fixed bottom-0 end-0 p-3 depan">
        <button class="btn btn-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#modalPesanan">
            <i class="fas fa-receipt"></i>
        </button>
    </div>
    <!-- Modal Pesanan -->
    <div class="modal fade" id="modalPesanan" tabindex="-1" aria-labelledby="pesananLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="pesananLabel">
                        <i class="fa-solid fa-receipt"></i> Pesanan Anda
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (count($user_pesanan) > 0): ?>
                        <ul class="list-group">
                            <?php foreach ($user_pesanan as $index => $pesanan): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start" id="pesanan-<?= $pesanan['id'] ?>">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">
                                            <i class="fa-solid fa-box"></i> <?= htmlspecialchars($pesanan['nama_produk']) ?>
                                        </div>
                                        <span class="text-muted"><i class="fas fa-user"></i>
                                            Nama Penerima: <?= htmlspecialchars($pesanan['nama_penerima']) ?></span><br>
                                        <span class="text-muted"><i class="fas fa-map-marker-alt"></i>
                                            Alamat: <?= htmlspecialchars($pesanan['alamat']) ?></span><br>
                                        <span class="text-muted"><i class="fa-solid fa-info-circle"></i>
                                            <?php
                                            if (isset($pesanan['stat']) && $pesanan['stat'] == "Sedang Diproses") {
                                                $abe = 'fa-hourglass-start';
                                                $class = "fas";
                                                $color = "blue";
                                            } elseif (isset($pesanan['stat']) && $pesanan['stat'] == "Sedang Diantar") {
                                                $abe = 'fa-truck';
                                                $class = "fas";
                                                $color = "skyblue";
                                            } elseif (isset($pesanan['stat']) && $pesanan['stat'] == "Selesai") {
                                                $abe = 'fa-check-circle';
                                                $class = "fas";
                                                $color = "green";
                                            } elseif (isset($pesanan['stat']) && $pesanan['stat'] == "Dibatalkan") {
                                                $abe = 'fa-times-circle';
                                                $class = "fas";
                                                $color = "red";
                                            }
                                            ?>
                                            Status Pengiriman : <i class="<?= $class ?> <?= $abe ?>" style="color:<?= $color ?>;"></i> <?= htmlspecialchars($pesanan['stat']) ?></span><br>
                                        <span class="text-muted"><i class="fas fa-wallet"></i>
                                            <?php
                                            if (isset($pesanan['status_pembayaran']) && $pesanan['status_pembayaran'] == "Belum bayar") {
                                                $abe2 = 'fa-clock';
                                                $class2 = "fas";
                                                $class3 = "skyblue";
                                            } elseif (isset($pesanan['status_pembayaran']) && $pesanan['status_pembayaran'] == "Sudah bayar") {
                                                $abe2 = 'fa-check-circle';
                                                $class2 = "fas";
                                                $class3 = "green";
                                            } elseif (isset($pesanan['status_pembayaran']) && $pesanan['status_pembayaran'] == "Kedaluwarsa") {
                                                $abe2 = 'fa-exclamation-circle';
                                                $class2 = "fas";
                                                $class3 = "red";
                                            }
                                            ?>
                                            Status Pembayaran: <i class="<?= $class2 ?> <?= $abe2 ?>" style="color:<?= $class3 ?>;"></i> <?= htmlspecialchars($pesanan['status_pembayaran']) ?></span><br>
                                        <span class="text-success"><i class="fa-solid fa-money-bill-wave"></i> Total: Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></span><br>
                                        <span class="text-muted"><i class="fa-solid fa-credit-card"></i>
                                            Metode Pembayaran: <?= htmlspecialchars($pesanan['metode_pembayaran']) ?></span><br>
                                        <span class="text-muted"><i class="fas fa-calendar-alt"></i>
                                            Tanggal Transaksi: <?= htmlspecialchars($pesanan['tanggal_transaksi']) ?></span>
                                    </div>
                                    <?php if ($pesanan['stat'] == "Selesai" || $pesanan['stat'] == "Dibatalkan" || $pesanan['status_pembayaran'] == "Kedaluwarsa"): ?>
                                        <a href="hapus.php?id=<?= $pesanan['id']; ?>" class="btn btn-danger btn-sm delete-btn">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                </li>
                                <?php if ($index === array_key_last($user_pesanan)): ?>
                                    <li class="list-group-item text-end">
                                        <span class="text-primary"><i class="fa-solid fa-truck"></i> Ongkir: Rp. 7000</span>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-info text-center" role="alert">
                            <i class="fa-solid fa-circle-info"></i> Anda belum memiliki pesanan.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .ad-date {
            font-size: 1.2rem;
            color: #d32f2f;
            /* Merah Natal */
            background-color: #fff3e0;
            /* Warna krem lembut */
            padding: 10px;
            border: 2px solid #4caf50;
            /* Hijau Natal */
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: inline-block;
            margin-bottom: 10px;
            font-family: 'Poppins', sans-serif;
        }

        .ad-timer {
            font-size: 1.1rem;
            color: #4caf50;
            /* Hijau Natal */
            background: #fff;
            padding: 10px;
            border: 2px solid #d32f2f;
            /* Merah Natal */
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-family: 'Poppins', sans-serif;
            display: inline-block;
            position: relative;
            margin-bottom: 20px;
        }

        .ad-date:before,
        .ad-timer:before {
            content: "🎅";
            /* Ikon Natal */
            margin-right: 8px;
            font-size: 1.4rem;
        }

        .date {
            font-weight: bold;
            text-decoration: underline;
        }

        #ad-timer {
            animation: blink 1s infinite;
        }

        /* Efek berkedip untuk timer */
        @keyframes blink {

            0%,
            100% {
                color: #4caf50;
            }

            50% {
                color: #d32f2f;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .snow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9999;
        }

        .snowflake {
            position: absolute;
            top: -10px;
            width: 10px;
            height: 10px;
            background-color: #fff;
            border-radius: 50%;
            opacity: 0.8;
            animation: snowfall 5s linear infinite;
        }

        @keyframes snowfall {
            to {
                transform: translateY(100vh);
            }
        }

        .ad-container {
            position: relative;
            margin: 30px auto;
            padding: 20px;
            background: linear-gradient(120deg, #f7d6e0, #ffc8b0);
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            animation: float 5s infinite ease-in-out;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .fireworks {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
    </style>
    <script>
        function createSnowflakes() {
            const snowContainer = document.querySelector('.snow');
            const numFlakes = 50;

            for (let i = 0; i < numFlakes; i++) {
                const flake = document.createElement('div');
                flake.classList.add('snowflake');
                flake.style.left = Math.random() * 100 + 'vw';
                flake.style.animationDuration = Math.random() * 3 + 3 + 's'; // Random duration for each flake
                flake.style.animationDelay = Math.random() * 5 + 's'; // Random delay for each flake
                snowContainer.appendChild(flake);
            }
        }

        createSnowflakes();
        document.addEventListener("DOMContentLoaded", () => {
            // Ambil semua elemen dengan ID 'timer-*'
            const timers = document.querySelectorAll("[id^='timer-']");
            timers.forEach((timer) => {
                const adId = timer.id.split("-")[1];
                const endDate = new Date(timer.previousElementSibling.textContent.split("Ends on: ")[1].trim());

                const updateTimer = () => {
                    const now = new Date();
                    const timeDiff = endDate - now;

                    if (timeDiff <= 0) {
                        timer.textContent = "Expired";
                        return;
                    }

                    const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

                    timer.textContent = `Time left: ${days}d ${hours}h ${minutes}m ${seconds}s`;
                };

                updateTimer(); // Initial call
                setInterval(updateTimer, 1000); // Update every second
            });
        });

        document.addEventListener("DOMContentLoaded", () => {
            const adContainer = document.querySelector(".ad-container");
            const canvas = adContainer.querySelector(".fireworks");
            const ctx = canvas.getContext("2d");
            const particles = [];

            canvas.width = adContainer.offsetWidth;
            canvas.height = adContainer.offsetHeight;

            function createParticle(x, y) {
                particles.push({
                    x: x,
                    y: y,
                    size: Math.random() * 3 + 1,
                    speedX: Math.random() * 4 - 2,
                    speedY: Math.random() * 4 - 2,
                    color: `hsl(${Math.random() * 360}, 100%, 50%)`,
                    life: 100,
                });
            }

            function updateParticles() {
                particles.forEach((particle, index) => {
                    particle.x += particle.speedX;
                    particle.y += particle.speedY;
                    particle.size *= 0.95;
                    particle.life -= 2;

                    if (particle.life <= 0) particles.splice(index, 1);
                });
            }

            function drawParticles() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                particles.forEach((particle) => {
                    ctx.beginPath();
                    ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
                    ctx.fillStyle = particle.color;
                    ctx.fill();
                });
            }

            function animate() {
                updateParticles();
                drawParticles();
                requestAnimationFrame(animate);
            }

            canvas.addEventListener("click", (e) => {
                const rect = canvas.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                for (let i = 0; i < 50; i++) {
                    createParticle(x, y);
                }
            });

            animate();
        });
    </script>


    <!-- Contact Us -->
    <section class="p-5" id="contact" data-aos="zoom-in" data-aos-duration="1000">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md">
                    <h5 class="section-title ff-secondary text-center text-warning fw-normal">Contact <i class="fas fa-comment-dots"></i> <!-- Ikon Obrolan --> Us</h5>
                    <br>
                    <ul class="list--group list-group-flush lead">
                        <li class="list-group-item" style="border-bottom: 1px solid #ccc;">
                            <i class="bi bi-geo-alt"></i>
                            <span class="fw-bold">Location:</span>
                            Semarang Tengah, Pendirikan Kidul, Nakula Raya No. 36
                        </li>
                        <li class="list-group-item" style="border-bottom: 1px solid #ccc;">
                            <i class="bi bi-telephone"></i>
                            <span class="fw-bold">Mobile Phone:</span>
                            (+62) 812-3626-2924
                        </li>
                        <li class="list-group-item" style="border-bottom: 1px solid #ccc;">
                            <i class="bi bi-instagram"></i>
                            <span class="fw-bold">Instagram:</span>
                            @abe_kolin
                        </li>
                        <li class="list-group-item" style="border-bottom: 1px solid #ccc;">
                            <i class="bi bi-envelope"></i>
                            <span class="fw-bold">E-Mail:</span>
                            albertog4taz28@gmail.com
                        </li>
                    </ul>
                </div>
                <div class="col-md">
                    <img class="img-fluid d-none d-lg-block" src="../img/contact.svg" alt="contact">
                </div>
            </div>
        </div>
        </div>
    </section>
    <!-- End Contact Us -->
    <!-- Footer -->
    <footer class="p-5 bg-dark text-white text-center position-relative" data-aos="zoom-in" data-aos-duration="1000">
        <div class="container">
            <p class="lead">Copyright &copy; 2025 AryaniGO.com</p>
            <a href="" class="position-absolute bottom-0 end-0 p-5">
                <i class="bi bi-arrow-up-circle h1" style="color: orange;"></i>
            </a>
        </div>
    </footer>
    <!-- End Footer -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="../js/script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const sections = document.querySelectorAll("section");
            const navLinks = document.querySelectorAll("nav ul li a");
            const observerOptions = {
                threshold: 0.6,
            };
            const observerCallback = (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        navLinks.forEach((link) => link.classList.remove("active"));
                        const activeLink = document.querySelector(`nav a[href="#${entry.target.id}"]`);
                        if (activeLink) {
                            activeLink.classList.add("active");
                        }
                    }
                });
            };
            const observer = new IntersectionObserver(observerCallback, observerOptions);
            sections.forEach((section) => observer.observe(section));
        });
        AOS.init();
        AOS.init({
            once: true,
            duration: 1000,
        });
        document.addEventListener("DOMContentLoaded", () => {
            const counterElement = document.getElementById("counter");
            const aboutUsSection = document.querySelector("#aboutus");
            let hasAnimated = false;
            const onSectionVisible = (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !hasAnimated) {
                        hasAnimated = true;
                        animateCounter(0, 5, 2000);
                        observer.unobserve(aboutUsSection);
                    }
                });
            };
            const observer = new IntersectionObserver(onSectionVisible, {
                threshold: 0.5,
            });
            observer.observe(aboutUsSection);
        });
        document.addEventListener("DOMContentLoaded", () => {
            const sections = document.querySelectorAll("section");
            const navLinks = document.querySelectorAll("nav ul li a");
            const observerOptions = {
                threshold: 0.6,
            };
            const observerCallback = (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        navLinks.forEach((link) => link.classList.remove("active"));
                        const activeLink = document.querySelector(`nav a[href="#${entry.target.id}"]`);
                        if (activeLink) {
                            activeLink.classList.add("active");
                        }
                    }
                });
            };
            const observer = new IntersectionObserver(observerCallback, observerOptions);
            sections.forEach((section) => observer.observe(section));
        });
        document.getElementById('search').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase(); // Get the input value
            const cards = document.querySelectorAll('.card'); // Select all cards

            cards.forEach(card => {
                const cardText = card.innerText.toLowerCase(); // Get the card's text content
                if (cardText.includes(searchValue)) {
                    card.parentElement.style.display = ''; // Show card (parent is `.col-md-3`)
                } else {
                    card.parentElement.style.display = 'none'; // Hide card
                }
            });
        });


        function updateCartQuantity() {
            fetch('get_cart_quantity.php')
                .then(response => response.text())
                .then(quantity => {
                    const cartBadge = document.getElementById('cart-quantity');
                    if (quantity === '0') {
                        cartBadge.style.display = 'none'; // Sembunyikan badge jika jumlahnya 0
                    } else {
                        cartBadge.style.display = 'inline-block'; // Tampilkan badge jika jumlahnya lebih dari 0
                        cartBadge.textContent = quantity;
                    }
                })
                .catch(error => console.error('Error fetching cart quantity:', error));
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateCartQuantity(); // Memanggil updateCartQuantity ketika halaman selesai dimuat
        });
    </script>
</body>

</html>