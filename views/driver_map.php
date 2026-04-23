<?php include 'views/layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fas fa-map-marked-alt"></i> Navigation Map</h1>
    <p>Use the map to navigate to your passenger's location</p>
</div>

<div class="driver-map-page">
    <!-- Sidebar -->
    <div class="driver-map-info">
        <?php if (!empty($current_ride)): ?>
        <div class="ride-info-card">
            <h4><i class="fas fa-user"></i> Current Passenger</h4>
            <div class="info-row"><i class="fas fa-user"></i><span><?php echo htmlspecialchars($current_ride['user_name']); ?></span></div>
            <div class="info-row"><i class="fas fa-phone"></i><span><?php echo htmlspecialchars($current_ride['user_phone']); ?></span></div>
        </div>
        <div class="ride-info-card">
            <h4><i class="fas fa-route"></i> Journey</h4>
            <div class="info-row"><i class="fas fa-circle" style="color:var(--neon-green); font-size:0.7rem;"></i><span><?php echo htmlspecialchars($current_ride['pickup_location']); ?></span></div>
            <div class="info-row"><i class="fas fa-flag-checkered" style="color:var(--neon-pink);"></i><span><?php echo htmlspecialchars($current_ride['dropoff_location']); ?></span></div>
            <div class="info-row"><i class="fas fa-rupee-sign" style="color:var(--neon-yellow);"></i><span>₹<?php echo number_format(floatval($current_ride['fare']),0); ?></span></div>
        </div>
        <?php else: ?>
        <div class="ride-info-card">
            <h4><i class="fas fa-info-circle"></i> No Active Ride</h4>
            <p style="font-size:0.88rem; color:var(--text-secondary);">Accept a ride from your dashboard to see passenger navigation here.</p>
        </div>
        <?php endif; ?>

        <!-- Quick navigation input -->
        <div class="ride-info-card">
            <h4><i class="fas fa-search"></i> Quick Navigate</h4>
            <div class="location-input-wrap">
                <i class="fas fa-map-marker-alt location-input-icon"></i>
                <input type="text" id="navDest" placeholder="Enter destination..." list="goa-places"
                    oninput="updateDriverMap(this.value)"
                    style="padding:11px 14px 11px 40px; width:100%; background:var(--bg-input); border:1px solid var(--border-color); border-radius:10px; color:var(--text-primary); outline:none;">
            </div>
            <datalist id="goa-places">
                <?php
                $places = ['Panaji Bus Stand','Dabolim Airport','Calangute Beach','Baga Beach','Margao Market','Vasco Railway','Colva Beach','Madgaon Station','Anjuna Beach','Old Goa Church','Miramar Beach','Mapusa Market','Candolim','Fort Aguada'];
                foreach($places as $p) echo "<option value='$p, Goa'>";
                ?>
            </datalist>
        </div>

        <a href="index.php?page=driver_dashboard" class="btn btn-secondary btn-full"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <!-- Map -->
    <div>
        <div class="map-box" style="height:520px;">
            <?php
            $default_loc = 'Panaji Goa';
            if (!empty($current_ride)) {
                $default_loc = urlencode($current_ride['pickup_location'].' Goa India');
            } else {
                $default_loc = urlencode('Panaji, Goa');
            }
            ?>
            <iframe id="driverMapFrame"
                src="https://maps.google.com/maps?q=<?php echo $default_loc; ?>&output=embed"
                allowfullscreen loading="lazy"></iframe>
        </div>
        <div style="text-align:center; margin-top:10px;">
            <a href="https://maps.google.com/?q=<?php echo urlencode(!empty($current_ride) ? $current_ride['pickup_location'].' Goa' : 'Panaji Goa'); ?>"
               target="_blank" class="btn btn-primary" style="margin-top:8px;">
                <i class="fas fa-external-link-alt"></i> Open in Google Maps
            </a>
        </div>
    </div>
</div>

<script>
function updateDriverMap(dest) {
    if (dest.length > 3) {
        const q = encodeURIComponent(dest);
        document.getElementById('driverMapFrame').src = `https://maps.google.com/maps?q=${q}&output=embed`;
    }
}
</script>

<?php include 'views/layout/footer.php'; ?>
