<?php include 'views/layout/header.php'; ?>

<?php
$is_student = false;
if (isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['user_type'])) {
        require_once 'models/UserModel.php';
        $uModel = new UserModel();
        $uData = $uModel->getUserById($_SESSION['user_id']);
        if ($uData) $_SESSION['user_type'] = $uData['user_type'];
    }
    $is_student = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student');

    // ✅ Extra check — verify college email domain too
    $userEmail = isset($_SESSION['email']) ? strtolower($_SESSION['email']) : '';
    $emailDomain = substr(strrchr($userEmail, '@'), 1);
    if (!str_ends_with($emailDomain, '.ac.in')) {
        $is_student = false; // block non-college email even if user_type is student
    }
}
?>

<div class="page-header">
    <h1><i class="fas fa-car"></i> Book Your Ride</h1>
    <p>Select ride type, car, locations &amp; driver preference</p>
</div>

<?php if ($is_student): ?>
<div class="coupon-banner">
    <div class="coupon-icon"><i class="fas fa-graduation-cap"></i></div>
    <div class="coupon-info">
        <h3>🎉 Student Discount Unlocked!</h3>
        <p>10% off on every ride — unlimited. Use code:</p>
        <span class="coupon-code-badge" onclick="applyCoupon()" title="Click to auto-apply">STUDENT10</span>
        <small style="color:var(--text-muted); margin-left:8px;">No expiry · No limit</small>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="booking-wrap">
<form method="POST" action="index.php?page=booking" id="bookingForm">
<div class="booking-card">

    <!-- ① RIDE TYPE FILTER -->
    <div class="booking-section">
        <h2><i class="fas fa-filter"></i> Choose Ride Category</h2>
        <div class="ride-filter-scroll-wrap">
            <div class="ride-filter-scroll">
                <div class="ride-filter-chip active" data-type="car"     onclick="filterRide('car',this)"><i class="fas fa-car"></i> Economy Cars</div>
                <div class="ride-filter-chip"         data-type="bike"    onclick="filterRide('bike',this)"><i class="fas fa-motorcycle"></i> Bike Rides</div>
                <div class="ride-filter-chip"         data-type="taxi"    onclick="filterRide('taxi',this)"><i class="fas fa-taxi"></i> Taxis</div>
                <div class="ride-filter-chip"         data-type="airport" onclick="filterRide('airport',this)"><i class="fas fa-plane"></i> Airport Transfer</div>
                <div class="ride-filter-chip"         data-type="rental"  onclick="filterRide('rental',this)"><i class="fas fa-key"></i> Rental Package</div>
            </div>
        </div>
        <input type="hidden" name="ride_type" id="rideTypeInput" value="car">

        <p style="font-size:0.82rem; color:var(--text-secondary); margin-bottom:12px;">Select your car type:</p>
        <div class="car-grid" id="carGrid">
            <!-- economy cars -->
            <div class="car-card selected" data-car="economy_4seater" data-category="car" onclick="selectCar(this)">
                <div class="car-card-icon"><i class="fas fa-car"></i></div>
                <h4>Economy Sedan</h4><div class="car-meta">Swift Dzire / Etios</div>
                <div class="seat-badge"><i class="fas fa-users"></i> 4 Seats</div>
                <div class="car-price">₹40 base · ₹12/km</div>
            </div>
            <div class="car-card" data-car="economy_7seater" data-category="car" onclick="selectCar(this)">
                <div class="car-card-icon"><i class="fas fa-shuttle-van"></i></div>
                <h4>Economy MPV</h4><div class="car-meta">Ertiga / Marazzo</div>
                <div class="seat-badge"><i class="fas fa-users"></i> 7 Seats</div>
                <div class="car-price">₹60 base · ₹14/km</div>
            </div>
            <div class="car-card" data-car="premium_4seater" data-category="car" onclick="selectCar(this)">
                <div class="car-card-icon" style="color:var(--neon-yellow);"><i class="fas fa-star"></i></div>
                <h4>Premium Sedan</h4><div class="car-meta">Honda City / Ciaz</div>
                <div class="seat-badge"><i class="fas fa-users"></i> 4 Seats</div>
                <div class="car-price">₹80 base · ₹16/km</div>
            </div>
            <div class="car-card" data-car="premium_7seater" data-category="car" onclick="selectCar(this)">
                <div class="car-card-icon" style="color:var(--neon-purple);"><i class="fas fa-crown"></i></div>
                <h4>Premium SUV</h4><div class="car-meta">Innova / XUV700</div>
                <div class="seat-badge"><i class="fas fa-users"></i> 7 Seats</div>
                <div class="car-price">₹120 base · ₹18/km</div>
            </div>
            <!-- bike -->
            <div class="car-card" data-car="bike_solo" data-category="bike" onclick="selectCar(this)" style="display:none;">
                <div class="car-card-icon"><i class="fas fa-motorcycle"></i></div>
                <h4>Solo Bike</h4><div class="car-meta">Hero / Honda / TVS</div>
                <div class="seat-badge"><i class="fas fa-user"></i> 1 Seat</div>
                <div class="car-price">₹20 base · ₹8/km</div>
            </div>
            <!-- taxi -->
            <div class="car-card" data-car="taxi_std" data-category="taxi" onclick="selectCar(this)" style="display:none;">
                <div class="car-card-icon"><i class="fas fa-taxi"></i></div>
                <h4>Standard Taxi</h4><div class="car-meta">Indica / Ambassador</div>
                <div class="seat-badge"><i class="fas fa-users"></i> 4 Seats</div>
                <div class="car-price">₹50 base · ₹14/km</div>
            </div>
            <div class="car-card" data-car="taxi_ac" data-category="taxi" onclick="selectCar(this)" style="display:none;">
                <div class="car-card-icon" style="color:var(--neon-cyan);"><i class="fas fa-snowflake"></i></div>
                <h4>AC Taxi</h4><div class="car-meta">Dzire / Etios</div>
                <div class="seat-badge"><i class="fas fa-users"></i> 4 Seats</div>
                <div class="car-price">₹60 base · ₹16/km</div>
            </div>
            <!-- airport -->
            <div class="car-card" data-car="airport_sedan" data-category="airport" onclick="selectCar(this)" style="display:none;">
                <div class="car-card-icon" style="color:var(--neon-green);"><i class="fas fa-plane-departure"></i></div>
                <h4>Airport Sedan</h4><div class="car-meta">City / Dzire</div>
                <div class="seat-badge"><i class="fas fa-users"></i> 4 Seats</div>
                <div class="car-price">₹200 base · ₹16/km</div>
            </div>
            <div class="car-card" data-car="airport_suv" data-category="airport" onclick="selectCar(this)" style="display:none;">
                <div class="car-card-icon" style="color:var(--neon-purple);"><i class="fas fa-plane-arrival"></i></div>
                <h4>Airport SUV</h4><div class="car-meta">Innova Crysta</div>
                <div class="seat-badge"><i class="fas fa-users"></i> 7 Seats</div>
                <div class="car-price">₹280 base · ₹20/km</div>
            </div>
            <!-- rental -->
            <div class="car-card" data-car="rental_half" data-category="rental" onclick="selectCar(this)" style="display:none;">
                <div class="car-card-icon" style="color:var(--neon-pink);"><i class="fas fa-clock"></i></div>
                <h4>Half Day</h4><div class="car-meta">4 Hours · Any car</div>
                <div class="seat-badge"><i class="fas fa-users"></i> 4–7 Seats</div>
                <div class="car-price">₹500 flat</div>
            </div>
            <div class="car-card" data-car="rental_full" data-category="rental" onclick="selectCar(this)" style="display:none;">
                <div class="car-card-icon" style="color:var(--neon-yellow);"><i class="fas fa-calendar-day"></i></div>
                <h4>Full Day</h4><div class="car-meta">8 Hours · Any car</div>
                <div class="seat-badge"><i class="fas fa-users"></i> 4–7 Seats</div>
                <div class="car-price">₹900 flat</div>
            </div>
        </div>
        <input type="hidden" name="car_type" id="carTypeInput" value="economy_4seater">
    </div>

    <!-- ② PET FRIENDLY -->
    <div class="booking-section">
        <h2><i class="fas fa-paw"></i> Travelling with a Pet?</h2>
        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
            <label id="petLabel" style="display:flex; align-items:center; gap:12px; padding:14px 20px;
                   background:var(--bg-card); border:2px solid var(--border-color); border-radius:12px;
                   cursor:pointer; transition:all 0.25s; flex:1; min-width:200px;" onclick="togglePet()">
                <input type="checkbox" name="pet_friendly_required" id="petCheck" value="1"
                       style="width:18px; height:18px; accent-color:var(--neon-green);"
                       onchange="togglePet()">
                <span>
                    <strong style="display:block; margin-bottom:2px;"><i class="fas fa-paw" style="color:var(--neon-green);"></i> Yes, I have a pet</strong>
                    <small style="color:var(--text-secondary);">Only pet-friendly drivers will be shown</small>
                </span>
            </label>
            <div id="petWarning" style="display:none; font-size:0.85rem; color:var(--neon-green);">
                <i class="fas fa-check-circle"></i> Great! We'll match you with a <strong>pet-friendly driver</strong>. Additional care charges may apply.
            </div>
        </div>
    </div>

    <!-- ③ LOCATIONS -->
    <div class="booking-section">
        <h2><i class="fas fa-map-marker-alt"></i> Where to?</h2>
        <div class="location-input-wrap">
            <i class="fas fa-circle location-input-icon" style="color:var(--neon-green); font-size:0.65rem;"></i>
            <input type="text" name="pickup_location" id="pickupInput"
                   placeholder="Enter pickup location..." autocomplete="off" required
                   oninput="showSugg('pickupInput','pickupSug')"
                   onfocus="showSugg('pickupInput','pickupSug')"
                   onblur="hideSugg('pickupSug')">
            <div class="suggestions-list" id="pickupSug"></div>
        </div>
        <div class="location-input-wrap">
            <i class="fas fa-flag-checkered location-input-icon" style="color:var(--neon-pink); font-size:0.75rem;"></i>
            <input type="text" name="dropoff_location" id="dropoffInput"
                   placeholder="Where are you going?" autocomplete="off" required
                   oninput="showSugg('dropoffInput','dropoffSug')"
                   onfocus="showSugg('dropoffInput','dropoffSug')"
                   onblur="hideSugg('dropoffSug')">
            <div class="suggestions-list" id="dropoffSug"></div>
        </div>
        <div class="map-box" style="height:240px; margin-top:14px;">
            <div class="map-placeholder" id="mapPlaceholder">
                <i class="fas fa-map-marked-alt"></i>
                <p>Enter pickup &amp; drop to preview route</p>
                <small>Map preview will appear here</small>
            </div>
            <iframe id="routeMapFrame" style="display:none; width:100%; height:100%; border:none;"></iframe>
        </div>
    </div>

    <!-- ④ DRIVER ASSIGNMENT MODE -->
    <div class="booking-section">
        <h2><i class="fas fa-user-check"></i> Driver Assignment</h2>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;" id="assignModeGrid">
            <label class="payment-option selected" id="modeManual" onclick="setMode('manual', this)">
                <input type="radio" name="driver_assign_mode" value="manual" checked style="display:none;">
                <i class="fas fa-hand-pointer" style="font-size:1.4rem;"></i>
                <span><strong>Choose My Driver</strong><br><small style="font-weight:400;">I'll pick from the list</small></span>
            </label>
            <label class="payment-option" id="modeAuto" onclick="setMode('auto', this)">
                <input type="radio" name="driver_assign_mode" value="auto" style="display:none;">
                <i class="fas fa-magic" style="font-size:1.4rem;"></i>
                <span><strong>Auto Assign</strong><br><small style="font-weight:400;">Best available driver</small></span>
            </label>
        </div>
        <div id="autoNote" style="display:none; margin-top:10px;" class="alert alert-info" style="margin:0;">
            <i class="fas fa-bolt"></i> The system will instantly match you with the best available online driver near your pickup area.
        </div>
    </div>

    <!-- ⑤ PAYMENT -->
    <div class="booking-section">
        <h2><i class="fas fa-wallet"></i> Payment Method</h2>
        <div class="payment-options">
            <label class="payment-option selected" onclick="selPay(this)"><input type="radio" name="payment_mode" value="upi" checked><i class="fas fa-mobile-alt"></i><span>UPI / GPay</span></label>
            <label class="payment-option" onclick="selPay(this)"><input type="radio" name="payment_mode" value="card"><i class="fas fa-credit-card"></i><span>Card</span></label>
            <label class="payment-option" onclick="selPay(this)"><input type="radio" name="payment_mode" value="cash"><i class="fas fa-money-bill-wave"></i><span>Cash</span></label>
            <label class="payment-option" onclick="selPay(this)"><input type="radio" name="payment_mode" value="wallet"><i class="fas fa-wallet"></i><span>Wallet</span></label>
        </div>
    </div>

    <!-- ⑥ COUPON -->
    <div class="booking-section">
        <h2><i class="fas fa-tag"></i> Coupon Code</h2>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <input type="text" id="couponInput" name="coupon_code"
                   placeholder="Enter coupon code"
                   style="flex:1; min-width:150px; padding:12px 16px; background:var(--bg-input);
                          border:1px solid var(--border-color); border-radius:10px;
                          color:var(--text-primary); font-size:0.95rem; outline:none; text-transform:uppercase;">
            <button type="button" class="btn btn-secondary" onclick="valCoupon()"><i class="fas fa-check"></i> Apply</button>
        </div>
        <div id="couponMsg" style="margin-top:8px; font-size:0.84rem;"></div>
    </div>

    <input type="hidden" name="fare" value="150">
    <button type="submit" name="book_ride" class="book-now-btn" style="margin-top:6px;">
        <i class="fas fa-arrow-right"></i> Continue to Driver Selection
    </button>
</div><!-- booking-card -->
</form>

    <!-- Sidebar -->
    <div>
        <div class="map-card" style="margin-bottom:18px;">
            <h3 style="font-size:1rem; font-weight:700; margin-bottom:14px; color:var(--accent);">
                <i class="fas fa-info-circle"></i> Ride Summary
            </h3>
            <div style="font-size:0.9rem; color:var(--text-secondary); display:flex; flex-direction:column; gap:8px;">
                <p><i class="fas fa-car" style="color:var(--accent); width:18px;"></i> <span id="sumType">Economy Sedan</span></p>
                <p><i class="fas fa-users" style="color:var(--accent); width:18px;"></i> <span id="sumSeats">4 Seats</span></p>
                <p><i class="fas fa-rupee-sign" style="color:var(--neon-yellow); width:18px;"></i> <span id="sumFare">₹40 base + ₹12/km</span></p>
                <p><i class="fas fa-tag" style="color:var(--neon-pink); width:18px;"></i> <span id="sumDiscount">No discount</span></p>
                <p><i class="fas fa-paw" style="color:var(--neon-green); width:18px;"></i> <span id="sumPet">No pet</span></p>
                <p><i class="fas fa-user-check" style="color:var(--neon-purple); width:18px;"></i> <span id="sumMode">Manual driver selection</span></p>
            </div>
        </div>
        <div class="map-card">
            <h3 style="font-size:1rem; font-weight:700; margin-bottom:12px; color:var(--accent);">
                <i class="fas fa-map-pin"></i> Popular Locations
            </h3>
            <div style="display:flex; flex-wrap:wrap; gap:6px;">
                <?php
                $locs = ['Panaji Bus Stand','Dabolim Airport','Calangute Beach','Baga Beach',
                         'Margao Market','Vasco Railway','Colva Beach','Anjuna Beach',
                         'Old Goa Church','Miramar Beach','Mapusa Market','Fort Aguada'];
                foreach ($locs as $l): ?>
                <button type="button" class="ride-filter-chip"
                        style="font-size:0.73rem; padding:5px 10px;"
                        onclick="insertLoc('<?php echo $l; ?>')">
                    <i class="fas fa-map-pin"></i> <?php echo $l; ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div><!-- booking-wrap -->

<script>
// ✅ Pass $is_student from PHP to JS securely
const IS_STUDENT = <?php echo $is_student ? 'true' : 'false'; ?>;

const GOA_LOCS = [
    'Panaji Bus Stand','Panjim Ferry','Miramar Beach','Campal','Dona Paula',
    'Dabolim Airport','Vasco da Gama','Vasco Railway Station',
    'Calangute Beach','Baga Beach','Anjuna Beach','Arambol Beach','Vagator',
    'Margao Market','Colva Beach','Benaulim','Cavelossim',
    'Mapusa Market','Siolim','Assagao','Old Goa Church','Ponda',
    'Madgaon Station','Candolim','Sinquerim','Fort Aguada','Dona Paula'
];

function showSugg(inId, sugId) {
    const inp = document.getElementById(inId);
    const sug = document.getElementById(sugId);
    const v   = inp.value.toLowerCase();
    const filtered = v
        ? GOA_LOCS.filter(l => l.toLowerCase().includes(v)).slice(0,7)
        : GOA_LOCS.slice(0,6);
    if (filtered.length) {
        sug.innerHTML = filtered.map(l =>
            `<div class="suggestion-item" onmousedown="pickSugg('${inId}','${sugId}','${l}')">
                <i class="fas fa-map-pin"></i>${l}</div>`
        ).join('');
        sug.classList.add('open');
    } else { sug.classList.remove('open'); }
    updateMap();
}
function hideSugg(s) { setTimeout(()=>document.getElementById(s).classList.remove('open'),150); }
function pickSugg(inId, sugId, val) {
    document.getElementById(inId).value = val;
    document.getElementById(sugId).classList.remove('open');
    updateMap();
}
function insertLoc(loc) {
    const pu = document.getElementById('pickupInput');
    const dr = document.getElementById('dropoffInput');
    if (!pu.value) pu.value = loc; else dr.value = loc;
    updateMap();
}
function updateMap() {
    const pu = document.getElementById('pickupInput').value;
    const dr = document.getElementById('dropoffInput').value;
    if (pu && dr) {
        document.getElementById('mapPlaceholder').style.display = 'none';
        const f = document.getElementById('routeMapFrame');
        f.src = `https://maps.google.com/maps?q=${encodeURIComponent(pu+' Goa')}&output=embed`;
        f.style.display = 'block';
    }
}

function filterRide(type, chip) {
    document.querySelectorAll('.ride-filter-chip').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');
    document.getElementById('rideTypeInput').value = type;
    document.querySelectorAll('.car-card').forEach(c => {
        c.style.display = c.dataset.category === type ? '' : 'none';
        c.classList.remove('selected');
    });
    const first = document.querySelector(`.car-card[data-category="${type}"]`);
    if (first) { first.classList.add('selected'); document.getElementById('carTypeInput').value = first.dataset.car; }
    updateSummary();
}

function selectCar(card) {
    document.querySelectorAll('.car-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('carTypeInput').value = card.dataset.car;
    updateSummary();
}

function selPay(lbl) {
    document.querySelectorAll('.payment-option').forEach(p => p.classList.remove('selected'));
    lbl.classList.add('selected');
    lbl.querySelector('input').checked = true;
}

function setMode(mode, lbl) {
    document.querySelectorAll('#assignModeGrid .payment-option').forEach(p => p.classList.remove('selected'));
    lbl.classList.add('selected');
    lbl.querySelector('input').checked = true;
    document.getElementById('autoNote').style.display = mode === 'auto' ? 'flex' : 'none';
    document.getElementById('sumMode').textContent = mode === 'auto' ? 'Auto-assign best driver' : 'Manual driver selection';
}

function togglePet() {
    const checked = document.getElementById('petCheck').checked;
    const lbl     = document.getElementById('petLabel');
    const warn    = document.getElementById('petWarning');
    lbl.style.borderColor  = checked ? 'var(--neon-green)' : 'var(--border-color)';
    lbl.style.background   = checked ? 'rgba(0,255,136,0.06)' : 'var(--bg-card)';
    warn.style.display     = checked ? 'block' : 'none';
    document.getElementById('sumPet').textContent = checked ? 'Pet-friendly driver required' : 'No pet';
    document.getElementById('sumPet').style.color = checked ? 'var(--neon-green)' : 'var(--text-secondary)';
}

const carInfo = {
    economy_4seater: {name:'Economy Sedan', seats:'4 Seats', fare:'₹40 base · ₹12/km'},
    economy_7seater: {name:'Economy MPV',   seats:'7 Seats', fare:'₹60 base · ₹14/km'},
    premium_4seater: {name:'Premium Sedan', seats:'4 Seats', fare:'₹80 base · ₹16/km'},
    premium_7seater: {name:'Premium SUV',   seats:'7 Seats', fare:'₹120 base · ₹18/km'},
    bike_solo:       {name:'Bike Ride',     seats:'1 Seat',  fare:'₹20 base · ₹8/km'},
    taxi_std:        {name:'Standard Taxi', seats:'4 Seats', fare:'₹50 base · ₹14/km'},
    taxi_ac:         {name:'AC Taxi',       seats:'4 Seats', fare:'₹60 base · ₹16/km'},
    airport_sedan:   {name:'Airport Sedan', seats:'4 Seats', fare:'₹200 base · ₹16/km'},
    airport_suv:     {name:'Airport SUV',   seats:'7 Seats', fare:'₹280 base · ₹20/km'},
    rental_half:     {name:'Half Day',      seats:'4–7',     fare:'₹500 flat'},
    rental_full:     {name:'Full Day',      seats:'4–7',     fare:'₹900 flat'},
};
function updateSummary() {
    const ct   = document.getElementById('carTypeInput').value;
    const info = carInfo[ct] || carInfo['economy_4seater'];
    document.getElementById('sumType').textContent  = info.name;
    document.getElementById('sumSeats').textContent = info.seats;
    document.getElementById('sumFare').textContent  = info.fare;
}

// ✅ valCoupon now checks IS_STUDENT before allowing STUDENT10
function valCoupon() {
    const code = document.getElementById('couponInput').value.trim().toUpperCase();
    const msg  = document.getElementById('couponMsg');

    if (code === 'STUDENT10') {
        if (!IS_STUDENT) {
            // ✅ Block non-students from using student coupon
            msg.innerHTML = '<span style="color:var(--neon-pink);">✗ This coupon is exclusively for students with a college email (.ac.in). Register as a student with your college email to unlock this offer.</span>';
            document.getElementById('couponInput').value = '';
            return;
        }
        msg.innerHTML = '<span style="color:var(--neon-green);">✓ 10% student discount applied!</span>';
        document.getElementById('sumDiscount').textContent = '10% off (STUDENT10)';
        document.getElementById('sumDiscount').style.color = 'var(--neon-green)';
    } else if (code) {
        msg.innerHTML = '<span style="color:var(--neon-pink);">✗ Invalid coupon code.</span>';
    } else {
        msg.innerHTML = '<span style="color:var(--text-muted);">Enter a coupon code first.</span>';
    }
}

function applyCoupon() { document.getElementById('couponInput').value='STUDENT10'; valCoupon(); }
</script>

<?php include 'views/layout/footer.php'; ?>