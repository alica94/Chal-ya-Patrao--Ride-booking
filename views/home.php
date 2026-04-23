<?php include 'views/layout/header.php'; ?>

<!-- Hero -->
<section class="hero-section">
    <h1 class="hero-title">
        Goa's Fastest<br>
        <span class="neon-text">चल या!!! PATRAO</span>
    </h1>
    <p class="hero-sub">Premium rides, student discounts, pet-friendly cars — all across Goa. On Time. Every Time.</p>
    <div class="hero-actions">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php?page=booking" class="btn btn-primary btn-lg"><i class="fas fa-car"></i> Book a Ride</a>
            <a href="index.php?page=my_rides" class="btn btn-secondary btn-lg"><i class="fas fa-list"></i> My Rides</a>
        <?php else: ?>
            <a href="index.php?page=register" class="btn btn-primary btn-lg"><i class="fas fa-user-plus"></i> Get Started</a>
            <a href="index.php?page=login" class="btn btn-secondary btn-lg"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php endif; ?>
    </div>
</section>

<!-- Slideshow -->
<div class="slideshow-container" style="margin-bottom:50px;">
    <div class="slide active">
        <img src="https://images.overdrive.in/wp-content/odgallery/2018/11/48713_Maruti_Suzuki_Ertiga_2019_004.JPG" alt="Premium Rides">
        <div class="slide-caption">⚡ Premium Rides at Affordable Prices</div>
    </div>
    <div class="slide">
        <img src="https://wrench.com/blog/content/images/2019/07/cars-for-college-students.jpg" alt="Student Rides">
        <div class="slide-caption">🎓 Special Student Discounts — Use Code STUDENT10</div>
    </div>
    <div class="slide">
        <img src="https://tommygoatourandtravels.com/assets/images/service/taxi-service-in-goa.jpg" alt="Goa Taxi">
        <div class="slide-caption">🌴 Trusted Goan Drivers — Safe & On Time</div>
    </div>
    <button class="slide-nav prev" onclick="changeSlide(-1)">&#10094;</button>
    <button class="slide-nav next" onclick="changeSlide(1)">&#10095;</button>
</div>

<!-- Features -->
<h2 class="section-title"><span>Our Special Features</span></h2>
<div class="features-grid" style="margin-bottom:60px;">
    <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-paw"></i></div>
        <h3>Pet-Friendly Rides</h3>
        <p>Travel with your furry friends. Specially selected pet-friendly cars across Goa.</p>
        <a href="index.php?page=booking" class="feature-btn">Book Now</a>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-graduation-cap"></i></div>
        <h3>Student Discounts</h3>
        <p>Register as a student and get unlimited 10% off on every ride. No expiry!</p>
        <a href="index.php?page=register" class="feature-btn">Register Now</a>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-plane"></i></div>
        <h3>Airport Transfers</h3>
        <p>Pre-booked airport pickups and drops. Never miss a flight with Chal Ya!</p>
        <a href="index.php?page=booking" class="feature-btn">Book Transfer</a>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-clock"></i></div>
        <h3>24/7 Available</h3>
        <p>Round the clock availability across all of Goa. Day, night, rain or shine.</p>
        <a href="index.php?page=booking" class="feature-btn">Book Now</a>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
        <h3>OTP Verified Rides</h3>
        <p>Every ride is secured with OTP verification for your complete safety.</p>
        <a href="index.php?page=booking" class="feature-btn">Ride Safe</a>
    </div>
    <div class="feature-card">
        <div class="feature-icon"><i class="fas fa-map-marked-alt"></i></div>
        <h3>Live Tracking</h3>
        <p>Track your driver in real-time on an interactive map directly in the app.</p>
        <a href="index.php?page=booking" class="feature-btn">Track Now</a>
    </div>
</div>

<script>
let idx = 0;
const slides = document.querySelectorAll('.slide');
function showSlide(n) {
    slides.forEach(s => s.classList.remove('active'));
    idx = (n + slides.length) % slides.length;
    slides[idx].classList.add('active');
}
function changeSlide(d) { showSlide(idx + d); }
setInterval(() => showSlide(idx + 1), 4000);
</script>

<?php include 'views/layout/footer.php'; ?>
