<?php include 'views/layout/header.php'; ?>
<div class="page-header"><h1><i class="fas fa-star" style="color:var(--neon-yellow);"></i> My Ratings</h1></div>
<?php if (empty($ratings)): ?>
    <div class="alert alert-info"><i class="fas fa-info-circle"></i> No ratings yet. Complete rides to receive ratings!</div>
<?php else: ?>
<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:16px;">
<?php foreach($ratings as $r): ?>
<div class="complaint-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
        <span style="font-weight:700;"><?php echo htmlspecialchars($r['user_name']); ?></span>
        <span style="color:var(--neon-yellow); font-size:1rem;"><?php for($s=1;$s<=5;$s++) echo $s<=$r['stars']?'★':'☆'; ?></span>
    </div>
    <?php if ($r['feedback_text']): ?><div class="complaint-desc"><?php echo htmlspecialchars($r['feedback_text']); ?></div><?php endif; ?>
    <div style="font-size:0.78rem; color:var(--text-muted); margin-top:8px;"><?php echo date('d M Y', strtotime($r['created_at'])); ?></div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<div style="margin-top:20px;"><a href="index.php?page=driver_dashboard" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a></div>
<?php include 'views/layout/footer.php'; ?>
