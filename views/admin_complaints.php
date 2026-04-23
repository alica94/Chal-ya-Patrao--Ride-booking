<?php include 'views/layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fas fa-flag"></i> Manage Complaints</h1>
    <p>Review and resolve user and driver complaints</p>
</div>

<?php if (!empty($success)): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<?php if (empty($complaints)): ?>
    <div class="alert alert-info"><i class="fas fa-check"></i> No complaints filed yet. Things are running smoothly!</div>
<?php else: ?>
    <?php foreach ($complaints as $c): ?>
    <div class="complaint-card">
        <div class="complaint-header">
            <div>
                <div class="complaint-subject"><?php echo htmlspecialchars($c['subject']); ?></div>
                <div class="complaint-meta">
                    Complaint #<?php echo intval($c['complaint_id']); ?>
                    <?php if ($c['ride_id']): ?> · Ride #<?php echo intval($c['ride_id']); ?><?php endif; ?>
                    · Filed by <?php echo htmlspecialchars($c['filed_by_type']); ?> (ID <?php echo intval($c['filed_by_user_id']); ?>)
                    · <?php echo date('d M Y, g:ia', strtotime($c['created_at'])); ?>
                </div>
            </div>
            <span class="status-badge status-<?php echo $c['status']==='open' ? 'open' : 'resolved'; ?>">
                <?php echo strtoupper($c['status']); ?>
            </span>
        </div>
        <div class="complaint-desc" style="margin-bottom:16px;"><?php echo htmlspecialchars($c['description']); ?></div>

        <?php if (!empty($c['admin_response'])): ?>
        <div class="admin-response" style="margin-bottom:12px;">
            <i class="fas fa-shield-alt"></i> <strong>Your Response:</strong><br>
            <?php echo htmlspecialchars($c['admin_response']); ?>
        </div>
        <?php endif; ?>

        <?php if ($c['status'] === 'open'): ?>
        <form method="POST" action="index.php?page=admin_complaints" style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
            <input type="hidden" name="complaint_id" value="<?php echo intval($c['complaint_id']); ?>">
            <div style="flex:1; min-width:200px;">
                <textarea name="admin_response" rows="2" placeholder="Write your response / resolution..." required
                    style="width:100%; padding:10px; background:var(--bg-input); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); resize:vertical; font-size:0.88rem; outline:none;"></textarea>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <button type="submit" name="resolve_complaint" class="btn btn-primary" style="padding:9px 18px; font-size:0.85rem;">
                    <i class="fas fa-check"></i> Resolve
                </button>
                <button type="submit" name="close_complaint" class="btn btn-danger" style="padding:9px 18px; font-size:0.85rem;">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'views/layout/footer.php'; ?>
