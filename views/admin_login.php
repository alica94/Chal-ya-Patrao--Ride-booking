<?php include 'views/layout/header.php'; ?>
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2><i class="fas fa-user-shield"></i> Admin Login</h2>
            <p>Restricted access — admins only</p>
        </div>
        <?php if (!empty($error)): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form class="auth-form" method="POST" action="index.php?page=admin_login">
            <div class="form-group"><label><i class="fas fa-envelope"></i> Admin Email</label><input type="email" name="email" placeholder="admin@chalya.in" required></div>
            <div class="form-group"><label><i class="fas fa-lock"></i> Password</label><input type="password" name="password" placeholder="Admin password" required></div>
            <button type="submit" name="admin_login" class="cta-button"><i class="fas fa-sign-in-alt"></i> Login as Admin</button>
            <div style="text-align:center; margin-top:12px; font-size:0.8rem; color:var(--text-muted);">Default: admin@chalya.in / admin123</div>
        </form>
    </div>
</div>
<?php include 'views/layout/footer.php'; ?>
