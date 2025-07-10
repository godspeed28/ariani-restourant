<?php
require 'functions.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location: post_logout.php');
    exit();
}
$query = "SELECT * FROM ads WHERE is_active = 1";
$result = $conn->query($query);
$currentDate = new DateTime();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="img/garpu.jpg" />
    <link rel="stylesheet" href="css/style.css">
    <title>Aryani GO</title>
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

    .img-hover-zoom {
        width: 100%;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .img-hover-zoom:hover {
        transform: scale(1.1);
        opacity: 0.8;
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

    nav ul li a {
        text-decoration: none;
        color: black;
        transition: color 0.3s ease;
    }

    nav ul li a.active {
        color: red;
        font-weight: bold;
    }

    @media (max-width: 576px) {
        .custom-img {
            height: auto;
        }

        .overlay-text {
            position: absolute;
            top: 45%;
            left: 50%;
            font-size: x-small;
        }
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

    p {
        margin-bottom: 0rem;
    }
</style>

<body class="animate__animated animate__fadeIn animate__delay-0.10s">
    <!-- Start Navbar -->
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark py-3 sticky-top">
        <div class="container-fluid">
            <p style="font-weight:bold;"> <a class="navbar-brand custom-font">&nbsp;&nbsp;<img src="img/garpu.jpg" alt="" style="width:50px; color:white; border-radius:50%;" class="gambar"> &nbsp;&nbsp;Aryani GO </a></p>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navmenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item" style="padding-right:10px;">
                        <a href="page/add_cart.php">
                            <!-- Ikon Keranjang -->
                            <div class="cart-icon" style="margin-top: 5px;">
                                <h5><i class="bi bi-cart" style="color:orange;"></i></h5>
                                <span id="cart-quantity" class="badge bg-danger"> <?= $total_items; ?></span>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#header">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#aboutus">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                            <form method="POST" action="logout.php">
                                <!-- Tombol Logout -->
                                <a href="logout.php" onclick="return confirm(' yakin ingin keluar?'); logout()" type="submit" style="color:red;margin-right: 10px; margin-left:5px;"><acronym title="Logout"><i class="fa-solid fa-right-from-bracket" style="margin-top:12px;"></i></acronym></a>
                            </form>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- End Navbar -->
    <!-- Header -->
    <section id="header" data-aos="fade-up" data-aos-duration="1000">
        <div><img src="img/header2.jpg" alt="" class="img-fluid custom-img" style=" filter: brightness(50%);">
            <div class="overlay-text animate__animated animate__fadeIn animate__delay-1s">

                <h3 style="color:silver;" class="animate__animated animate__fadeIn animate__delay-1s ">Selamat datang,
                    <?php if (isset($_COOKIE['id']) && isset($_COOKIE['key'])): ?>
                        <?= $_COOKIE['username']; ?>!</h3>
            <?php endif; ?>
            <?php if (!isset($_COOKIE['id']) && !isset($_COOKIE['key'])): ?>
                <?= $_SESSION['username']; ?>!</h3>
            <?php endif; ?>
            <h2 class="text-light animate__animated animate__fadeIn animate__delay-2s">Hangatkan Harimu di Rumah Makan Aryani Kami!</h2>
            <p class="animate__animated animate__fadeIn animate__delay-2s" style="color:silver">Nikmati sajian hangat dan lezat yang selalu siap menemanimu kapan saja...</p>
            <button class="btn btn-outline-light animate__animated animate__fadeIn animate__delay-2s" type="submit" style="margin-top: 5px;"><a class="href-none" href="page/menu.php">Ayo Pesan</a></button>
            </div>
        </div>
    </section>
    <!-- End Header -->
    <!-- Services -->
    <section class="services pt-5" id="services" data-aos="fade-up" data-aos-duration="1000">
        <div class="container">
            <div class="row text-center g-4">
                <h5 class="section-title ff-secondary text-center text-warning fw-normal">Our <i class="fas fa-hands-helping"></i> Services</h5>
                <div class="col-md">
                    <div class="card bg-dark text-white border">
                        <img src="img/wallet.svg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Harga Bersahabat</h5>
                            <p class="card-text">Makanan enak, porsi melimpah, harga bersahabat bagi mahasiswa yang ingin mengisi perut tanpa harus khawatir tentang anggaran. </p>
                            <a href="page/menu.php" class="btn btn-warning" style="margin-top: 5px;">Cek Harga</a>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card bg-warning-subtle">
                        <img src="img/food.svg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Menu Andalan</h5>
                            <p class="card-text">Jelajahi menu andalan kami! Dari mi instan, nasi telur, hingga lauk pauk khas yang bisa kamu padukan sesuka hati.</p>
                            <a href="page/menu.php" class="btn btn-secondary" style="margin-top: 5px;">Lihat Menu</a>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card bg-light text-dark border-0">
                        <img src="img/relax.svg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Santai & Nyaman</h5>
                            <p class="card-text">Nikmati suasana santai dan nyaman di warmindo kami. Tempat yang pas buat nongkrong sambil menikmati makanan lezat.</p>
                            <a href="https://maps.app.goo.gl/gr8VaVu7KdCrywaL9" class="btn btn-warning" target="_blank" style="margin-top: 5px;">Kunjungi Kami</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Services -->
    <!-- About Us -->
    <section id="aboutus" data-aos="fade-right" data-aos-duration="1000">
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-6 text-start">
                                <img class="img-hover-zoom img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s" src="img/warungmakan6.jpg" style="margin-top: -5px; height: 330px;">
                            </div>
                            <div class="col-6 text-start">
                                <img class="img-fluid rounded w-75 wow zoomIn img-hover-zoom" data-wow-delay="0.3s" src="img/warungmakan7.jpg" style="margin-top: 8%; height: 266px;">
                            </div>
                            <div class="col-6 text-end">
                                <img class="img-hover-zoom img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.5s" src="img/warungmakan3.jpg" style="height:320px;">
                            </div>
                            <div class="col-6 text-end">
                                <img class="img-hover-zoom img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.7s" src="img/warungmakan5.jpg" style="margin-top: -36px; height:400px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" style="margin-top:-40px;">
                        <h5 class="section-title ff-secondary text-start text-warning fw-normal">About Us</h5>
                        <h2 class="mb-4"><i class="fa fa-utensils text-warning me-2"></i>Aryani Restoran</h2>
                        <p class="mb-4">Warung Makan Aryani adalah usaha kuliner rumahan yang telah melayani pelanggan dengan cita rasa khas selama lebih dari 4 tahun. Didirikan dan dikelola oleh Ibu Imbangriana, warung kami menyajikan hidangan tradisional yang lezat dengan bahan-bahan segar pilihan. </p>
                        <p class="mb-4">Komitmen kami adalah menghadirkan pengalaman makan yang hangat, nyaman, dan penuh kelezatan. Warung Makan Aryani siap menjadi tempat pilihan Anda untuk menikmati makanan rumahan yang autentik dan memuaskan!</p>
                        <div class="row g-4 mb-4" data-aos="fade-up" data-aos-duration="1000">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center border-start border-5 border-warning px-3">
                                    <h1 id="counter" class="flex-shrink-0 display-5 text-warning mb-0">1</h1>
                                    <div class="ps-4">
                                        <p class="mb-0">Years of</p>
                                        <h6 class="text-uppercase mb-0">Experience</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End About Us -->
    <?php while ($row = $result->fetch_assoc()) : ?>
        <div class="ad-container" data-aos="fade-right" data-aos-duration="1000">
            <div class="ad-content" data-aos="fade-up" data-aos-duration="2000">

                <div class="snow"></div>
                <img src="img/<?= $row['image_url'] ?>" alt="<?= $row['title'] ?>" class="ad-image" style="border-radius:20px; padding:2px;border: 2px solid red;">
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
                            Semarang Tengah, Pendirikan Kidul, Jl. Nakula 1 No. 32
                        </li>
                        <li class="list-group-item" style="border-bottom: 1px solid #ccc;">
                            <i class="bi bi-telephone"></i>
                            <span class="fw-bold">Mobile Phone:</span>
                            (+62) 812-3626-2924
                        </li>
                        <li class="list-group-item" style="border-bottom: 1px solid #ccc;">
                            <i class="bi bi-instagram"></i>
                            <span class="fw-bold">Instagram:</span>
                            @warungmakanaryani
                        </li>
                        <li class="list-group-item" style="border-bottom: 1px solid #ccc;">
                            <i class="bi bi-envelope"></i>
                            <span class="fw-bold">E-Mail:</span>
                            warungmakanaryani@gmail.com
                        </li>
                    </ul>
                </div>
                <div class="col-md">
                    <img class="img-fluid d-none d-lg-block" src="img/contact.svg" alt="contact">
                </div>
            </div>
        </div>
        </div>
    </section>
    <!--End Contact Us -->
    <!-- Footer -->
    <footer class="p-5 bg-dark text-white text-center position-relative" data-aos="zoom-in" data-aos-duration="1000">
        <div class="container">
            <p class="lead">Copyright &copy; 2025 AryaniGO.com</p>
            <a href="" class="position-absolute bottom-0 end-0 p-5">
                <i class="bi bi-arrow-up-circle h1" style="color: orange;"></i>
            </a>
        </div>
    </footer>
    <!-- End footer -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="js/script.js"></script>

    <script>
        AOS.init();
        AOS.init({
            once: true,
            duration: 1000,
        });
        document.addEventListener("DOMContentLoaded", () => {
            const counterElement = document.getElementById("counter");
            const aboutUsSection = document.querySelector("#aboutus");
            let hasAnimated = false;

            const animateCounter = (start, end, duration) => {
                let startTime = null;

                const step = (currentTime) => {
                    if (!startTime) startTime = currentTime;
                    const progress = Math.min((currentTime - startTime) / duration, 1);
                    counterElement.textContent = Math.floor(progress * (end - start) + start);

                    if (progress < 1) {
                        requestAnimationFrame(step);
                    }
                };

                requestAnimationFrame(step);
            };

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

        function updateCartQuantity() {
            fetch('page/get_cart_quantity.php')
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