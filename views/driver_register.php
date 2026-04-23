<?php include 'views/layout/header.php'; ?>
<div class="auth-container" style="max-width:560px;">
    <div class="auth-card">
        <div class="auth-header">
            <h2><i class="fas fa-id-card"></i> Driver Registration</h2>
            <p>Join Chal Ya Patrao as a driver. Approval required.</p>
        </div>
        <?php if (!empty($error)): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <?php if (!empty($success)): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?> <a href="index.php?page=driver_login" class="auth-link">Login</a></div><?php endif; ?>
        <form class="auth-form" method="POST" action="index.php?page=driver_register">
            <h3 style="font-size:0.9rem; font-weight:700; color:var(--accent); margin-bottom:12px; text-transform:uppercase; letter-spacing:1px;">Personal Details</h3>
            <div class="form-group"><label><i class="fas fa-user"></i> Full Name</label><input type="text" name="full_name" required></div>
            <div class="form-group"><label><i class="fas fa-phone"></i> Mobile Number</label><input type="tel" name="phone" pattern="[0-9]{10}" required></div>
            <div class="form-group"><label><i class="fas fa-venus-mars"></i> Gender</label>
                <select name="gender" required><option value="" disabled selected>Select gender</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
            </div>
            <div class="form-group"><label><i class="fas fa-id-card"></i> License Number</label><input type="text" name="license_number" required></div>
            <div class="form-group"><label><i class="fas fa-lock"></i> Password</label><input type="password" name="password" required oninput="checkStrength(this.value)"><div class="password-strength-bar" id="strengthBar"></div><div class="password-strength-text" id="strengthText"></div></div>
            <h3 style="font-size:0.9rem; font-weight:700; color:var(--accent); margin:16px 0 12px; text-transform:uppercase; letter-spacing:1px;">Vehicle Details</h3>
            <div class="form-group"><label><i class="fas fa-car"></i> Vehicle Type</label>
                <select name="vehicle_type" required><option value="car">Car</option><option value="bike">Bike</option><option value="taxi">Taxi</option></select>
            </div>
            <div class="form-group"><label><i class="fas fa-hashtag"></i> Registration Number</label><input type="text" name="registration_number" placeholder="GA-01-AB-1234" required></div>
            <div class="form-group"><label><i class="fas fa-car-side"></i> Vehicle Model</label><input type="text" name="vehicle_model" placeholder="e.g. Maruti Swift Dzire" required></div>
            <div class="form-group"><label><i class="fas fa-palette"></i> Color</label><input type="text" name="vehicle_color" required></div>
            <div class="form-group"><label><i class="fas fa-calendar"></i> Insurance Expiry</label><input type="date" name="insurance_expiry" required></div>
            <div class="form-group" style="display:flex; align-items:center; gap:10px;">
                <input type="checkbox" name="is_pet_friendly" id="petfriendly" style="width:auto;">
                <label for="petfriendly" style="margin:0; cursor:pointer;"><i class="fas fa-paw"></i> Pet-Friendly Vehicle</label>
            </div>
            <button type="submit" name="driver_register" class="cta-button"><i class="fas fa-paper-plane"></i> Submit Registration</button>
            <div style="text-align:center; margin-top:14px; font-size:0.88rem; color:var(--text-secondary);">Already registered? <a href="index.php?page=driver_login" class="auth-link">Login</a></div>
        </form>
    </div>
</div>
<script>
function checkStrength(v) {
    const bar = document.getElementById('strengthBar');
    const txt = document.getElementById('strengthText');
    if (!v) { bar.className='password-strength-bar'; txt.textContent=''; return; }
    let score = 0;
    if (v.length >= 8) score++;
    if (v.length >= 12) score++;
    if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    if (score <= 2) { bar.className='password-strength-bar weak'; txt.className='password-strength-text weak'; txt.textContent='⚠ Weak'; }
    else if (score <= 3) { bar.className='password-strength-bar fair'; txt.className='password-strength-text fair'; txt.textContent='✦ Fair'; }
    else { bar.className='password-strength-bar strong'; txt.className='password-strength-text strong'; txt.textContent='✓ Strong!'; }
}
</script>
<?php include 'views/layout/footer.php'; ?>
