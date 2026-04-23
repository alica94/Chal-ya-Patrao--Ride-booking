<?php
require_once 'models/BookingModel.php';

function requireLogin() {
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }
}

//Verifing student with goa registered emails
function isVerifiedStudent() {
    $user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';
    $email     = isset($_SESSION['email'])     ? strtolower(trim($_SESSION['email'])) : '';
    $domain    = substr(strrchr($email, '@'), 1);
    $allowed_colleges = [
        'chowgules.ac.in', 'dempocollege.ac.in', 'rosarymargao.ac.in',
        'goa.bits-pilani.ac.in', 'unigoa.ac.in', 'gim.ac.in',
        'iitgoa.ac.in', 'nit.goa.ac.in', 'govcollegepanji.ac.in',
        'agnel.ac.in', 'drait.ac.in', 'srosc.ac.in',
    ];
    return ($user_type === 'student' && in_array($domain, $allowed_colleges));
}

// Step 1: Booking form → session → select driver (or auto-assign)
function handleBooking() {
    requireLogin();
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_ride'])) {
        $pickup      = trim($_POST['pickup_location']);
        $dropoff     = trim($_POST['dropoff_location']);
        $ride_type   = trim($_POST['ride_type']);
        $car_type    = isset($_POST['car_type'])           ? trim($_POST['car_type'])           : 'economy_4seater';
        $assign_mode = isset($_POST['driver_assign_mode']) ? trim($_POST['driver_assign_mode']) : 'manual';
        $pet         = isset($_POST['pet_friendly_required']) ? 1 : 0;

        if ($pickup === '' || $dropoff === '') {
            $error = 'Please enter pickup and drop-off locations.';
        } else {
            $fares = array(
                'economy_4seater' => 150, 'economy_7seater' => 200,
                'premium_4seater' => 250, 'premium_7seater' => 350,
                'bike_solo'       => 80,
                'taxi_std'        => 200, 'taxi_ac'         => 240,
                'airport_sedan'   => 350, 'airport_suv'     => 500,
                'rental_half'     => 500, 'rental_full'     => 900,
            );
            $fare = isset($fares[$car_type]) ? $fares[$car_type] : 150;

            // ✅ Apply coupon — verified student with .ac.in email only
            $coupon_code = '';
            $discount    = 0;
            if (!empty($_POST['coupon_code'])) {
                $code = strtoupper(trim($_POST['coupon_code']));
                if ($code === 'STUDENT10') {
                    if (isVerifiedStudent()) {
                        $discount    = round($fare * 0.10);
                        $fare       -= $discount;
                        $coupon_code = $code;
                    } else {
                        $error = 'Coupon STUDENT10 is only valid for students registered with a college email (.ac.in).';
                    }
                }
            }

            if (empty($error)) {
                $_SESSION['pending_pickup']       = $pickup;
                $_SESSION['pending_dropoff']      = $dropoff;
                $_SESSION['pending_ride_type']    = $ride_type;
                $_SESSION['pending_car_type']     = $car_type;
                $_SESSION['pending_fare']         = $fare;
                $_SESSION['pending_driver_id']    = 0;
                $_SESSION['pending_pet_friendly'] = $pet;
                $_SESSION['pending_assign_mode']  = $assign_mode;
                $_SESSION['coupon_code']          = $coupon_code;
                $_SESSION['discount_amount']      = $discount;

                if ($assign_mode === 'auto') {
                    header('Location: index.php?page=payment');
                } else {
                    header('Location: index.php?page=select_driver');
                }
                exit;
            }
        }
    }
    include 'views/booking.php';
}

// Step 3: OTP Verification
function handleOtpVerify() {
    requireLogin();
    $error   = '';
    $ride_id = isset($_GET['ride_id']) ? intval($_GET['ride_id']) : 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
        $otp_input = trim($_POST['otp_code']);
        $ride_id   = intval($_POST['ride_id']);

        if (strlen($otp_input) === 6 && ctype_digit($otp_input)) {
            $_SESSION['otp_verified_ride'] = $ride_id;
            header('Location: index.php?page=thankyou');
            exit;
        } else {
            $error = 'Invalid OTP. Please enter a valid 6-digit code.';
        }
    }
    include 'views/otp_verify.php';
}

// Step 4: Payment → create booking in DB
function handlePayment() {
    requireLogin();
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
        $payment_mode = isset($_POST['payment_mode']) ? trim($_POST['payment_mode']) : '';

        if ($payment_mode === '') {
            $error = 'Please select a payment method.';
            include 'views/payment.php';
            return;
        }

        $assign_mode = isset($_SESSION['pending_assign_mode'])  ? $_SESSION['pending_assign_mode']  : 'manual';
        $pet         = isset($_SESSION['pending_pet_friendly']) ? intval($_SESSION['pending_pet_friendly']) : 0;

        $model = new BookingModel();
        $data  = array(
            'user_id'               => $_SESSION['user_id'],
            'user_email'            => isset($_SESSION['email'])     ? $_SESSION['email']     : '', // ✅ pass email
            'user_type'             => isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '', // ✅ pass user_type
            'pickup_location'       => $_SESSION['pending_pickup'],
            'dropoff_location'      => $_SESSION['pending_dropoff'],
            'ride_type'             => $_SESSION['pending_ride_type'],
            'car_type'              => isset($_SESSION['pending_car_type']) ? $_SESSION['pending_car_type'] : 'economy_4seater',
            'fare'                  => $_SESSION['pending_fare'],
            'payment_mode'          => $payment_mode,
            'requested_driver_id'   => isset($_SESSION['pending_driver_id']) ? intval($_SESSION['pending_driver_id']) : 0,
            'driver_assign_mode'    => $assign_mode,
            'pet_friendly_required' => $pet,
            'coupon_code'           => isset($_SESSION['coupon_code'])     ? $_SESSION['coupon_code']     : '',
            'discount_amount'       => isset($_SESSION['discount_amount']) ? $_SESSION['discount_amount'] : 0,
        );

        $result = $model->createBooking($data);

        unset(
            $_SESSION['pending_pickup'],    $_SESSION['pending_dropoff'],
            $_SESSION['pending_ride_type'], $_SESSION['pending_car_type'],
            $_SESSION['pending_fare'],      $_SESSION['pending_driver_id'],
            $_SESSION['pending_pet_friendly'], $_SESSION['pending_assign_mode'],
            $_SESSION['coupon_code'],       $_SESSION['discount_amount']
        );

        if ($result['success']) {
            $_SESSION['last_ride_id'] = $result['ride_id'];
            header('Location: index.php?page=otp_verify&ride_id=' . $result['ride_id']);
            exit;
        } else {
            $error = $result['message'];
            include 'views/payment.php';
            return;
        }
    }

    if (!isset($_SESSION['pending_pickup'])) {
        header('Location: index.php?page=booking');
        exit;
    }
    include 'views/payment.php';
}

function handleMyRides() {
    requireLogin();
    $model    = new BookingModel();
    $bookings = $model->getUserBookings($_SESSION['user_id']);
    include 'views/my_rides.php';
}

function handleCancel() {
    requireLogin();
    $model   = new BookingModel();
    $error   = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_ride'])) {
        $ride_id = intval($_POST['ride_id']);
        $result  = $model->cancelBooking($ride_id, $_SESSION['user_id']);
        if ($result['success']) { $success = $result['message']; }
        else                    { $error   = $result['message']; }
    }
    include 'views/cancel.php';
}

function handleApplyCoupon() {
    requireLogin();
    header('Content-Type: application/json');
    $code = strtoupper(trim($_POST['code'] ?? ''));

    if ($code === 'STUDENT10') {
        // ✅ Check both user_type AND college email domain
        if (isVerifiedStudent()) {
            echo json_encode(['valid' => true, 'discount' => 10, 'message' => '10% student discount applied!']);
        } else {
            echo json_encode(['valid' => false, 'message' => 'This coupon is only for students registered with a college email (.ac.in).']);
        }
    } else {
        echo json_encode(['valid' => false, 'message' => 'Invalid or ineligible coupon.']);
    }
    exit;
}
function handleReselectDriver() {
    requireLogin();
    $ride_id = isset($_GET['ride_id']) ? intval($_GET['ride_id']) : 0;

    if (!$ride_id) {
        header('Location: index.php?page=my_rides'); exit;
    }

    $model = new BookingModel();
    $ride  = $model->getBookingById($ride_id);

    // ✅ Make sure this ride belongs to the logged-in user
    if (!$ride || intval($ride['user_id']) !== intval($_SESSION['user_id'])) {
        header('Location: index.php?page=my_rides'); exit;
    }

    // ✅ Restore session with existing ride details — user only changes driver
    $_SESSION['pending_pickup']       = $ride['pickup_location'];
    $_SESSION['pending_dropoff']      = $ride['dropoff_location'];
    $_SESSION['pending_ride_type']    = $ride['ride_type'];
    $_SESSION['pending_car_type']     = $ride['car_type'];
    $_SESSION['pending_fare']         = $ride['fare'];
    $_SESSION['pending_pet_friendly'] = $ride['pet_friendly_required'];
    $_SESSION['pending_assign_mode']  = 'manual';
    $_SESSION['coupon_code']          = '';
    $_SESSION['discount_amount']      = 0;
    $_SESSION['reselect_ride_id']     = $ride_id; // ✅ remember original ride

    header('Location: index.php?page=select_driver'); exit;
}
?>