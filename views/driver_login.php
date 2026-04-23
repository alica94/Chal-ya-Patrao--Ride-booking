<?php include 'views/layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2><i class="fas fa-car" style="font-size:1.4rem;"></i> Driver Login</h2>
            <p>Access your driver dashboard</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form class="auth-form" method="POST" action="index.php?page=driver_login">
            <div class="form-group">
                <label><i class="fas fa-phone"></i> Mobile Number</label>
                <input type="tel" name="phone" placeholder="Registered mobile number" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" placeholder="Your password" required>
            </div>
            <button type="submit" name="driver_login" class="cta-button"><i class="fas fa-sign-in-alt"></i> Login as Driver</button>
            <div style="text-align:center;margin-top:16px;color:var(--text-secondary);font-size:0.9rem;">
                New driver? <a href="index.php?page=driver_register" class="auth-link">Register here</a>
            </div>
            <div style="text-align:center;margin-top:6px;">
                <small style="color:var(--text-muted);">Pre-set: Phone 9876543210 or 9876543211 / Pass: driver123</small>
            </div>
        </form>
    </div>
</div>
<?php include 'views/layout/footer.php'; ?>
