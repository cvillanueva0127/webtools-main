<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cubiertos Food Hub</title>
    <link rel="stylesheet" href="../CSS/Store.css" />
    <link rel="icon" type="image/jpg" href="../IMAGES/logo.jpg" />
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  </head>
  <body>
    <header class="glass-header">
      <div class="nav-container">
        <a href="../HTML/main.php" class="logo">
          <img src="../IMAGES/logo2.jpg" alt="Cubiertos Logo" />
          <div class="logo-text"><h2>Cubiertos</h2><span>FOOD HUB</span></div>
        </a>
        <nav class="navbar">
          <a href="../HTML/main.php">Home</a>
          <a href="../HTML/about.php">About</a>
          <a href="../HTML/Store.php" class="active">Stores</a>
          <a href="../HTML/contacts.php">Contacts</a>
        </nav>
        <?php if ($isLoggedIn): ?>
          <a href="../PHP/profile.php" class="login-btn">Profile</a>
        <?php else: ?>
          <a href="../HTML/login.html" class="login-btn">Log in</a>
        <?php endif; ?>
        <div class="menu-toggle" id="menuToggle">☰</div>
      </div>
    </header>

    <section class="store-hero">
      <div class="store-hero-content">
        <span class="mini-badge"> ✨ PREMIUM DINING EXPERIENCE </span>
        <h1>Discover Our <span>Beautiful Store</span></h1>
        <p>Experience delicious meals, relaxing ambiance, beachfront dining, and unforgettable moments only here at Cubiertos Food Hub.</p>
        <div class="hero-buttons">
          <a href="#store-section" class="hero-btn">Explore Store</a>
          <a href="contacts.php" class="hero-btn secondary-btn">Contact Us</a>
        </div>
        <div class="hero-stats">
          <div class="hero-stat"><h3>10K+</h3><p>Happy Customers</p></div>
          <div class="hero-stat"><h3>4.9★</h3><p>Customer Rating</p></div>
          <div class="hero-stat"><h3>Fresh</h3><p>Daily Ingredients</p></div>
        </div>
      </div>
    </section>

    <section class="why-section">
      <div class="why-left"><img src="../IMAGES/restobg.jpg" /></div>
      <div class="why-right">
        <span class="mini-tag">WHY CUSTOMERS LOVE US</span>
        <h2>More Than Just <span>A Restaurant</span></h2>
        <p>Cubiertos Food Hub offers a complete dining experience with delicious meals, relaxing ambiance, quality service, and memorable moments for families and friends.</p>
        <div class="feature-list">
          <div class="feature-box"><span></span><div><h4>Premium Meals</h4><p>Fresh ingredients prepared daily.</p></div></div>
          <div class="feature-box"><span></span><div><h4>Relaxing Atmosphere</h4><p>Beachfront and cozy dining experience.</p></div></div>
          <div class="feature-box"><span></span><div><h4>Top Rated Service</h4><p>Friendly staff and quality customer service.</p></div></div>
        </div>
      </div>
    </section>

    <div class="socials">
      <a href="https://www.facebook.com/profile.php?id=61555258696901" target="_blank"><img src="../IMAGES/Facebook.png" alt="Facebook" /></a>
      <a href="https://www.instagram.com/cubiertos2024/" target="_blank"><img src="../IMAGES/Instagram.png" alt="Instagram" /></a>
      <a href="https://mail.google.com/mail/u/0/#sent?compose=CllgCJTNqrcXTJzgQrjqbjtCXnqKDjzdGRPvdqdcFsXlDWgKNhcCtqcDSQcFtPLcvbmcdswCCcL" target="_blank"><img src="../IMAGES/Mail.png" alt="Mail" /></a>
    </div>

    <div class="title-container">
      <h1 class="section-title" id="store-section">OUR STORE EXPERIENCE</h1>
      <div class="title-bar"></div>
    </div>

    <section class="store-container">
      <div class="store-grid">
        <div class="card"><img src="../IMAGES/Store3.jpg" /><h3>Beachfront Dining</h3><p>Cozy beachfront setting, where sea breeze and ocean views create a relaxing and refreshing dining experience.</p><span>Contact Info: 0981 027 0704</span></div>
        <div class="card"><img src="../IMAGES/relaxed.jpg" /><h3>Relaxed and Cozy Atmosphere</h3><p>Chic tropical design with a calm and inviting ambiance, perfect for casual dining and unwinding.</p><span>Contact Info: 0981 027 0704</span></div>
        <div class="card"><img src="../IMAGES/inviting.jpg" /><h3>Inviting Bar & Alfresco Area</h3><p>A cozy and stylish space where guests can enjoy drinks, good food, and a laid-back outdoor atmosphere.</p><span>Contact Info: 0981 027 0704</span></div>
        <div class="card"><img src="../IMAGES/delightful.jpg" /><h3>Delightful Everyday Dining</h3><p>Freshly prepared meals made with quality ingredients, served in a warm and welcoming environment.</p><span>Contact Info: 0981 027 0704</span></div>
      </div>
      <div class="info-panel">
        <h2>Find Us Here</h2>
        <img src="../IMAGES/findus.jpg" class="banner" />
        <h3 class="store-name">Cubiertos Food Hub</h3>
        <p>Virac, 4800 Catanduanes Philippines</p>
        <p>Open: 9:00 AM – 10:00 PM</p>
        <p>+63 981 027 0704</p>
        <div class="buttons">
          <a href="https://www.google.com/maps/dir//CUBIERTOS,+Virac,+4800+Catanduanes/@13.5821383,124.2334262,17z" target="_blank"><button class="primary">Get Directions</button></a>
          <a href="https://www.google.com/maps/place/CUBIERTOS/@13.5821383,124.2354647,17z" target="_blank"><button class="secondary">View in Maps</button></a>
        </div>
        <a href="contacts.php"><button class="contact">Contact Us</button></a>
        <iframe src="https://www.google.com/maps?q=Cebu%20City&output=embed" width="100%" height="250" style="border:0" loading="lazy"></iframe>
      </div>
    </section>

    <footer>
      <div class="footer-top">
        <div class="footer-brand">
          <img src="../IMAGES/logo.jpg" alt="Cubiertos Food Hub" />
          <p class="footer-tagline">"Savor the flavors where every bite tells a story."</p>
        </div>
        <div class="footer-links">
          <a href="../HTML/main.php">Home</a>
          <a href="../HTML/about.php">About</a>
          <a href="../HTML/Store.php">Our Stores</a>
          <a href="../HTML/contacts.php">Contacts</a>
          <?php if ($isLoggedIn): ?>
            <a href="../PHP/profile.php">Profile</a>
          <?php else: ?>
            <a href="../HTML/login.html">Log in</a>
          <?php endif; ?>
        </div>
        <div class="footer-contact">
          <strong>Get in touch</strong>
          Food &amp; Drink · Virac, Philippines 4800<br />
          Contact: 0981 027 0704
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2025 Cubiertos.food.hub — All rights reserved.</span>
      </div>
    </footer>

    <script>
      window.addEventListener("scroll", function () {
        const header = document.querySelector(".glass-header");
        if (header) header.classList.toggle("scrolled", window.scrollY > 50);
      });
      const menuToggle = document.getElementById("menuToggle");
      const navbar = document.querySelector(".navbar");
      menuToggle.addEventListener("click", () => { navbar.classList.toggle("show"); });
    </script>
  </body>
</html>