</main>
<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>चल या!!! PATRAO</h3>
            <p>On Time. Every Time.</p>
            <p>Goa's most trusted ride-hailing service — student discounts, airport transfers & more.</p>
            <div class="footer-neon-line"></div>
        </div>
        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php?page=home"><i class="fas fa-chevron-right"></i> Home</a></li>
                <li><a href="index.php?page=booking"><i class="fas fa-chevron-right"></i> Book a Ride</a></li>
                <li><a href="index.php?page=login"><i class="fas fa-chevron-right"></i> Login</a></li>
                <li><a href="index.php?page=register"><i class="fas fa-chevron-right"></i> Register</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Special Offers</h3>
            <ul>
                <li><a href="#"><i class="fas fa-graduation-cap"></i> Student Discounts (10% off)</a></li>
                <li><a href="#"><i class="fas fa-paw"></i> Pet-Friendly Rides</a></li>
                <li><a href="#"><i class="fas fa-plane"></i> Airport Transfers</a></li>
                <li><a href="#"><i class="fas fa-clock"></i> 24/7 Available</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Contact Us</h3>
            <p><i class="fas fa-phone"></i> +91 98765 43210</p>
            <p><i class="fas fa-envelope"></i> support@chalyapatrao.com</p>
            <p><i class="fas fa-map-marker-alt"></i> Panaji, Goa 403001</p>
            <div class="social-icons">
                <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 चल या!!! PATRAO. All rights reserved. | Made with <i class="fas fa-heart" style="color:#ff006e;"></i> in Goa</p>
    </div>
</footer>

<script>
// Hamburger menu
const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('navMenu');
if (hamburger) {
    hamburger.addEventListener('click', function() {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });
}

// Dark/Light mode toggle
const themeToggle = document.getElementById('themeToggle');
const themeIcon = document.getElementById('themeIcon');
const html = document.documentElement;

// Load saved theme
const savedTheme = localStorage.getItem('chalya_theme') || 'dark';
html.setAttribute('data-theme', savedTheme);
themeIcon.className = savedTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';

themeToggle.addEventListener('click', function() {
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('chalya_theme', next);
    themeIcon.className = next === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
});
</script>
</body>
</html>
