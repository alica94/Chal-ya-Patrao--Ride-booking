<?php include 'views/layout/header.php'; ?>

<div class="auth-container" style="max-width:520px;">
    <div class="auth-card">
        <div class="auth-header">
            <h2><i class="fas fa-user-plus" style="font-size:1.4rem;"></i> Create Account</h2>
            <p>Join Chal Ya Patrao for faster bookings &amp; exclusive benefits</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                &nbsp;<a href="index.php?page=login" class="auth-link">Login here →</a>
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="index.php?page=register" id="registerForm">

            <div class="form-group">
                <label><i class="fas fa-user"></i> Full Name</label>
                <input type="text" name="full_name" placeholder="Your full name" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-users"></i> I am a</label>
                <select name="user_type" id="user_type" required onchange="handleUserType(this.value)">
                    <option value="" disabled selected>Select your profile</option>
                    <option value="student">Student</option>
                    <option value="resident">Working Professional / Resident</option>
                    <option value="tourist">Tourist</option>
                    <option value="elderly">Senior Citizen</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" id="emailInput"
                       placeholder="yourname@email.com" required>
                <div id="emailHint" style="display:none; margin-top:6px; font-size:0.82rem;
                     color:#e67e22; background:#fff8f0; border:1px solid #f0c080;
                     border-radius:6px; padding:7px 10px;">
                    <i class="fas fa-graduation-cap"></i>
                    Students <strong>must</strong> use their official Goa college email
                    (e.g. <code>alc000@chowgules.ac.in</code>) to unlock
                    <strong>10% student discount coupons</strong>.
                </div>
                <div id="emailValid" style="display:none; margin-top:6px; font-size:0.82rem;
                     color:#27ae60; background:#f0fff4; border:1px solid #a8e6bc;
                     border-radius:6px; padding:7px 10px;">
                    <i class="fas fa-check-circle"></i>
                    College email verified — student coupon eligible!
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-phone"></i> Mobile Number</label>
                <input type="tel" name="phone" placeholder="10-digit mobile number"
                       pattern="[0-9]{10}" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-calendar-alt"></i> Date of Birth</label>
                <input type="date" name="date_of_birth" id="dobInput"
                       max="<?php echo date('Y-m-d', strtotime('-5 years')); ?>"
                       min="<?php echo date('Y-m-d', strtotime('-120 years')); ?>"
                       required onchange="showAge(this.value)">
                <div id="ageHint" style="font-size:0.82rem; margin-top:5px; color:var(--text-secondary);"></div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Create a strong password" required
                       oninput="checkStrength(this.value)">
                <div class="password-strength-bar" id="strengthBar"></div>
                <div class="password-strength-text" id="strengthText"></div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-language"></i> Preferred Language</label>
                <select name="preferred_language">
                    <option value="english">English</option>
                    <option value="hindi">Hindi</option>
                    <option value="konkani">Konkani</option>
                </select>
            </div>

            <button type="submit" name="register" class="cta-button">
                <i class="fas fa-rocket"></i> Register &amp; Continue
            </button>
            <div style="text-align:center; margin-top:16px; color:var(--text-secondary); font-size:0.9rem;">
                Already have an account? <a href="index.php?page=login" class="auth-link">Login here</a>
            </div>
        </form>
    </div>
</div>

<script>
// ✅ Only recognised Goa college domains allowed
const ALLOWED_COLLEGES = [
    'chowgules.ac.in', 'dempocollege.ac.in', 'rosarymargao.ac.in',
    'goa.bits-pilani.ac.in', 'unigoa.ac.in', 'gim.ac.in',
    'iitgoa.ac.in', 'nit.goa.ac.in', 'govcollegepanji.ac.in',
    'agnel.ac.in', 'drait.ac.in', 'srosc.ac.in',
];

function handleUserType(type) {
    const emailInput = document.getElementById('emailInput');
    const emailHint  = document.getElementById('emailHint');
    const emailValid = document.getElementById('emailValid');

    if (type === 'student') {
        emailHint.style.display = 'block';
        emailInput.placeholder  = 'rollno@chowgules.ac.in';
        validateStudentEmail(emailInput.value);
        emailInput.addEventListener('input', () => validateStudentEmail(emailInput.value));
    } else {
        emailHint.style.display  = 'none';
        emailValid.style.display = 'none';
        emailInput.placeholder   = 'yourname@email.com';
    }
}

// ✅ Validates against specific allowed colleges only
function validateStudentEmail(email) {
    const hint  = document.getElementById('emailHint');
    const valid = document.getElementById('emailValid');
    if (!email) { hint.style.display = 'block'; valid.style.display = 'none'; return; }
    const domain = (email.split('@')[1] || '').toLowerCase();
    if (ALLOWED_COLLEGES.includes(domain)) {
        hint.style.display  = 'none';
        valid.style.display = 'block';
    } else {
        hint.style.display  = 'block';
        valid.style.display = 'none';
    }
}

// ✅ Block form submit if student uses non-allowed college email
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const userType = document.getElementById('user_type').value;
    const email    = document.getElementById('emailInput').value.trim().toLowerCase();

    if (userType === 'student') {
        const domain = (email.split('@')[1] || '').toLowerCase();
        if (!ALLOWED_COLLEGES.includes(domain)) {
            e.preventDefault();
            alert('⚠ Students must register with a recognised Goa college email (e.g. alc000@chowgules.ac.in).\n\nOnly official college email holders can access the 10% student discount coupon.');
            document.getElementById('emailInput').focus();
        }
    }
});

function showAge(dob) {
    const hint = document.getElementById('ageHint');
    if (!dob) { hint.textContent = ''; return; }
    const birth = new Date(dob);
    const now   = new Date();
    let age = now.getFullYear() - birth.getFullYear();
    const m = now.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && now.getDate() < birth.getDate())) age--;
    if (age < 5) {
        hint.innerHTML = '<span style="color:var(--neon-pink);">⚠ Age must be at least 5 years</span>';
    } else if (age >= 60) {
        hint.innerHTML = `<span style="color:var(--neon-green);">✓ Age: ${age} years — Senior Citizen discount may apply</span>`;
    } else {
        hint.innerHTML = `<span style="color:var(--neon-green);">✓ Age: ${age} years</span>`;
    }
}

function checkStrength(v) {
    const bar = document.getElementById('strengthBar');
    const txt = document.getElementById('strengthText');
    if (!v) { bar.className = 'password-strength-bar'; txt.textContent = ''; return; }
    let score = 0;
    if (v.length >= 8)  score++;
    if (v.length >= 12) score++;
    if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    if (score <= 2) {
        bar.className = 'password-strength-bar weak';
        txt.className = 'password-strength-text weak';
        txt.textContent = '⚠ Weak password — add numbers, symbols & uppercase';
    } else if (score <= 3) {
        bar.className = 'password-strength-bar fair';
        txt.className = 'password-strength-text fair';
        txt.textContent = '✦ Fair password — almost there!';
    } else {
        bar.className = 'password-strength-bar strong';
        txt.className = 'password-strength-text strong';
        txt.textContent = '✓ Strong password!';
    }
}
</script>

<?php include 'views/layout/footer.php'; ?>