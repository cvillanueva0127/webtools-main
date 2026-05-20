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

    <link rel="icon" type="image/jpg" href="../IMAGES/logo.jpg">

    <link rel="stylesheet" href="../CSS/main.css" />

    <link
      href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Poppins:wght@400;600;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <!-- HEADER -->
    <header class="glass-header">
      <div class="nav-container">
        <a href="../HTML/main.php" class="logo">
          <img src="../IMAGES/logo2.jpg" alt="Cubiertos Logo" />
          <div class="logo-text">
            <h2>Cubiertos</h2>
            <span>FOOD HUB</span>
          </div>
        </a>

        <nav class="navbar">
          <a href="../HTML/main.php" class="active">Home</a>
          <a href="../HTML/about.php">About</a>
          <a href="../HTML/Store.php">Stores</a>
          <a href="../HTML/contacts.php">Contacts</a>
        </nav>

        <?php if ($isLoggedIn): ?>
          <!-- Logged in: show Profile button -->
          <a href="../PHP/profile.php" class="login-btn">Profile</a>
        <?php else: ?>
          <!-- Not logged in: show Log In button -->
          <a href="../HTML/login.html" class="login-btn">Log in</a>
        <?php endif; ?>

        <div class="menu-toggle" id="menuToggle">☰</div>
      </div>
    </header>

    <!-- dito yung socials -->
    <div class="socials">
      <a href="https://www.facebook.com/profile.php?id=61555258696901" target="_blank">
        <img src="../IMAGES/Facebook.png" alt="Facebook" />
      </a>
      <a href="https://www.instagram.com/cubiertos2024/" target="_blank">
        <img src="../IMAGES/Instagram.png" alt="Instagram" />
      </a>
      <a href="https://mail.google.com/mail/u/0/#sent?compose=CllgCJTNqrcXTJzgQrjqbjtCXnqKDjzdGRPvdqdcFsXlDWgKNhcCtqcDSQcFtPLcvbmcdswCCcL" target="_blank">
        <img src="../IMAGES/Mail.png" alt="Mail" />
      </a>
    </div>

    <!-- main page -->
    <section id="mainpage">
      <section class="mainpage">
        <div class="mainpage-context">
          <h2>
            Experience a <span><br />Flavorful Food</span><br />
            with a whole twist
          </h2>
          <p>
            Every journey has its ups and downs <br />and Cubiertos is no
            exception.
          </p>
          <?php if ($isLoggedIn): ?>
            <a href="../PHP/homepage.php" class="book-btn">BOOK NOW!</a>
          <?php else: ?>
            <a href="../HTML/login.html" class="book-btn">BOOK NOW!</a>
          <?php endif; ?>
        </div>
      </section>
    </section>

    <!-- menu slideshow -->
    <section id="page2">
      <div class="w3-content w3-section">
        <div class="mySlides">
          <img src="../IMAGES/slide1.png" alt="Slide 1" />
          <div class="menu-promo-overlay">
            <span>LIMITED OFFER</span>
            <h2>Get 15% OFF on your first booking</h2>
            <p>Enjoy delicious meals and reserve your table online today.</p>
            <a href="login.html" class="promo-btn">Claim Promo</a>
          </div>
        </div>

        <div class="mySlides" style="display: none">
          <img src="../IMAGES/slide2.png" alt="Slide 2" />
          <div class="menu-promo-overlay">
            <span>CHEF'S SPECIAL</span>
            <h2>Try our new Creamy Pasta</h2>
            <p>A rich and flavorful twist on a classic favorite.</p>
            <a href="menu.php" class="promo-btn">See Menu</a>
          </div>
        </div>

        <div class="mySlides" style="display: none">
          <img src="../IMAGES/slide3.png" alt="Slide 3" />
          <div class="menu-promo-overlay">
            <span>MUST TRY</span>
            <h2>The Ultimate Burger Combo</h2>
            <p>Served with perfectly seasoned fries.</p>
            <a href="menu.html" class="promo-btn">See Menu</a>
          </div>
        </div>

        <div class="mySlides">
          <img src="../IMAGES/slide1.png" alt="Slide 1" />
          <div class="menu-promo-overlay">
            <h2>Pasta, Sandwich Wrap & Green Drink</h2>
            <p>A satisfying full meal — baked pasta, a fresh sandwich wrap, toasted bread, and a cool refreshing green drink.</p>
            <a href="menu.html" class="promo-btn">See Menu</a>
          </div>
        </div>

        <div class="mySlides" style="display: none">
          <img src="../IMAGES/slide2.png" alt="Slide 2" />
          <div class="menu-promo-overlay">
            <h2>Carbonara, Pesto Penne & Potato Mojos</h2>
            <p>Creamy carbonara topped with a sunny-side egg, herby pesto penne, crispy potato mojos, and warm garlic bread.</p>
            <a href="menu.html" class="promo-btn">See Menu</a>
          </div>
        </div>

        <div class="mySlides" style="display: none">
          <img src="../IMAGES/slide3.png" alt="Slide 3" />
          <div class="menu-promo-overlay">
            <h2>Baked Pasta, Creamy Mushroom & Iced Drink</h2>
            <p>Rich baked pasta in tomato sauce, a creamy mushroom dish, toasted bread, and a sweet strawberry iced drink.</p>
            <a href="menu.html" class="promo-btn">See Menu</a>
          </div>
        </div>

        <div class="mySlides" style="display: none">
          <img src="../IMAGES/slide4.png" alt="Slide 4" />
          <div class="menu-promo-overlay">
            <h2>Carbonara with Egg, Wrap & Toasted Bread</h2>
            <p>Silky carbonara topped with a sunny-side egg, a hearty wrap, and toasted bread drizzled with creamy sauce.</p>
            <a href="menu.html" class="promo-btn">See Menu</a>
          </div>
        </div>

        <div class="mySlides" style="display: none">
          <img src="../IMAGES/slide5.png" alt="Slide 5" />
          <div class="menu-promo-overlay">
            <h2>Crispy Fries, Fried Chicken & Onion Rings</h2>
            <p>Golden seasoned fries, crispy fried chicken, crunchy onion rings, and a creamy dipping sauce on the side.</p>
            <a href="menu.html" class="promo-btn">See Menu</a>
          </div>
        </div>

        <div class="menu-prompt">
          <h2>Ready to taste the difference? Our menu is waiting.</h2>
          <a href="menu.php" class="menu-btn">OUR MENU</a>
        </div>
      </div>
    </section>

    <!-- best sellers -->
    <section id="best-sellers" class="section best-sellers">
      <h2 class="section-title">OUR BEST SELLERS</h2>
      <div class="title-bar"></div>
      <div class="page3">
        <div class="card">
          <img src="../IMAGES/Pastareview.jpg" alt="Classic Carbonara" />
          <h3>Classic Carbonara with Egg on the top</h3>
          <p>This meal includes a creamy pasta duo served with toasted bread: one plate features spaghetti topped with an egg in white sauce, while the other has penne pasta tossed in a flavorful pesto-style sauce with bits of meat or seafood. Served with crispy potato chips on the side.</p>
        </div>
        <div class="card">
          <img src="../IMAGES/Fries.jpg" alt="Burger and Fries" />
          <h3>Lunch Meal Burger & Fries</h3>
          <p>This meal features a hearty serving of baked pasta with rich tomato sauce and melted cheese, paired with toasted bread on the side. Alongside it is a half sandwich with lettuce and meat filling on a white plate, and a refreshing iced matcha green tea drink.</p>
        </div>
      </div>
    </section>

    <!-- STATS SECTION -->
    <section class="quick-stats">
      <div class="quick-box"><h2>1K+</h2><p>Happy Customers</p></div>
      <div class="quick-box"><h2>25+</h2><p>Food Choices</p></div>
      <div class="quick-box"><h2>4.9 ★</h2><p>Customer Rating</p></div>
      <div class="quick-box"><h2>Fast</h2><p>Booking Service</p></div>
    </section>

    <!-- REVIEWS -->
    <section id="reviews" class="section best-sellers">
      <h2 class="section-title">REVIEWS</h2>
      <div class="title-bar"></div>
      <main class="reviews-container">
        <div class="review-card">
          <div class="review-content">
            <div class="review-image-container">
              <img src="../IMAGES/classic carbonara.jpg" alt="Classic Carbonara" class="review-image" />
            </div>
            <div class="review-details">
              <p class="meal-name">Breakfast Meal Pasta with Egg, Garlic Bread & Wrap</p>
              <p class="reviewer-info"><span class="rating">★ 4.8</span> Reviewed by Anna M.</p>
              <p class="review-text">"Their creamy pasta topped with a perfectly cooked sunny-side egg is both hearty and flavorful..."</p>
              <div class="review-actions">
                <i class="far fa-thumbs-up action-icon"></i>
                <i class="far fa-heart action-icon"></i>
                <i class="fas fa-reply action-icon"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="review-card">
          <div class="review-content">
            <div class="review-image-container">
              <img src="../IMAGES/burger.jpg" alt="Lunch Meal" class="review-image" />
            </div>
            <div class="review-details">
              <p class="meal-name">Lunch Meal Burger & Fries</p>
              <p class="reviewer-info"><span class="rating">★ 4.7</span> Reviewed by Mark D.</p>
              <p class="review-text">"The burger is juicy with soft buns, served with golden, crispy fries and tangy dip. Generous serving size makes it sharable and perfect for a casual lunch out."</p>
              <div class="review-actions">
                <i class="far fa-thumbs-up action-icon"></i>
                <i class="far fa-heart action-icon"></i>
                <i class="fas fa-reply action-icon"></i>
              </div>
            </div>
          </div>
        </div>
      </main>
    </section>

    <!-- GALLERY -->
    <section class="food-gallery">
      <h2 class="section-title">FOOD GALLERY</h2>
      <div class="title-bar"></div>
      <div class="gallery-grid">
        <img src="../IMAGES/mojos 1.png" alt="" />
        <img src="../IMAGES/Fries.jpg" alt="" />
        <img src="../IMAGES/slide1.png" alt="" />
        <img src="../IMAGES/slide2.png" alt="" />
        <img src="../IMAGES/slide3.png" alt="" />
        <img src="../IMAGES/slide4.png" alt="" />
      </div>
    </section>

    <!-- HERO CALL-TO-ACTION -->
    <section class="hero">
      <div class="hero-content">
        <h1><span>WANT TO ELEVATE YOUR</span><br />ONLINE EXPERIENCE?</h1>
        <p>Ready to get started? Our online booking is open. Book your appointment today!</p>
        <?php if ($isLoggedIn): ?>
          <a href="../PHP/homepage.php" class="book">BOOK NOW!</a>
        <?php else: ?>
          <a href="../HTML/login.html" class="book">BOOK NOW!</a>
        <?php endif; ?>
      </div>
      <div class="hero-image">
        <img src="../IMAGES/team.png" alt="Team Image" />
      </div>
    </section>

    <!-- FOOTER -->
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
        const header = document.querySelector("header");
        header.classList.toggle("scrolled", window.scrollY > 50);
      });

      let index = 0;
      const slides = document.getElementsByClassName("mySlides");
      function showSlides() {
        for (let i = 0; i < slides.length; i++) {
          slides[i].style.display = "none";
        }
        index++;
        if (index > slides.length) index = 1;
        slides[index - 1].style.display = "block";
        setTimeout(showSlides, 2000);
      }
      showSlides();

      const menuToggle = document.getElementById("menuToggle");
      const navbar = document.querySelector(".navbar");
      menuToggle.addEventListener("click", () => {
        navbar.classList.toggle("show");
      });
    </script>
  </body>
</html>