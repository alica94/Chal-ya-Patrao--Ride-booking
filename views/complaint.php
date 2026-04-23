<?php include 'views/layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fas fa-flag"></i> Complaints</h1>
    <p>File a complaint and track its resolution</p>
</div>

<?php if (!empty($error)): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if (!empty($success)): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div><?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:start;">
    <!-- File Complaint Form -->
    <div class="booking-card">
        <h2 style="font-size:1.1rem; font-weight:700; margin-bottom:20px; color:var(--accent);"><i class="fas fa-pen"></i> File a New Complaint</h2>
        <form method="POST" action="index.php?page=complaint">
            <div class="form-group">
                <label><i class="fas fa-hashtag"></i> Ride ID (optional)</label>
                <input type="number" name="ride_id" placeholder="e.g. 42" min="0"
                    value="<?php echo isset($_GET['ride_id']) ? intval($_GET['ride_id']) : ''; ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Subject</label>
                <input type="text" name="subject" placeholder="Brief subject of complaint" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Description</label>
                <textarea name="description" rows="5" placeholder="Describe your issue in detail..." required style="resize:vertical;"></textarea>
            </div>
            <button type="submit" name="file_complaint" class="btn btn-primary btn-full">
                <i class="fas fa-paper-plane"></i> Submit Complaint
            </button>
        </form>
    </div>

    <!-- My Complaints List -->
    <div>
        <h2 style="font-size:1.1rem; font-weight:700; margin-bottom:16px; color:var(--accent);"><i class="fas fa-list"></i> My Complaints</h2>
        <?php if (empty($complaints)): ?>
            <div class="alert alert-info"><i class="fas fa-check"></i> No complaints filed yet. We hope everything is going well!</div>
        <?php else: ?>
            <?php foreach ($complaints as $c): ?>
            <div class="complaint-card">
                <div class="complaint-header">
                    <div>
                        <div class="complaint-subject"><?php echo htmlspecialchars($c['subject']); ?></div>
                        <div class="complaint-meta">
                            #<?php echo intval($c['complaint_id']); ?>
                            <?php if ($c['ride_id']): ?> · Ride #<?php echo intval($c['ride_id']); ?><?php endif; ?>
                            · <?php echo date('d M Y', strtotime($c['created_at'])); ?>
                        </div>
                    </div>
                    <span class="status-badge status-<?php echo $c['status'] === 'open' ? 'open' : 'resolved'; ?>">
                        <?php echo htmlspecialchars($c['status']); ?>
                    </span>
                </div>
                <div class="complaint-desc"><?php echo htmlspecialchars($c['description']); ?></div>
                <?php if (!empty($c['admin_response'])): ?>
                <div class="admin-response">
                    <i class="fas fa-shield-alt"></i> <strong>Admin Response:</strong><br>
                    <?php echo htmlspecialchars($c['admin_response']); ?>
                    <?php if ($c['resolved_at']): ?>
                    <div style="font-size:0.78rem; margin-top:4px; opacity:0.7;">Resolved on <?php echo date('d M Y', strtotime($c['resolved_at'])); ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
@media (max-width:768px) {
    div[style*="grid-template-columns:1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php include 'views/layout/footer.php'; ?>
