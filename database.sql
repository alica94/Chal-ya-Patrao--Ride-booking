

DROP TABLE IF EXISTS ratings;
DROP TABLE IF EXISTS complaints;
DROP TABLE IF EXISTS otp_verifications;
DROP TABLE IF EXISTS coupons;
DROP TABLE IF EXISTS driver_requests;
DROP TABLE IF EXISTS rides;
DROP TABLE IF EXISTS ride_pricing;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS drivers;
DROP TABLE IF EXISTS users;

-- ─────────────────────────────────────────────
-- USERS (added: date_of_birth)
-- ─────────────────────────────────────────────
CREATE TABLE users (
    user_id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name          VARCHAR(100)  NOT NULL,
    phone_number       VARCHAR(15)   NOT NULL UNIQUE,
    email              VARCHAR(100)  NOT NULL UNIQUE,
    password_hash      VARCHAR(255)  NOT NULL,
    date_of_birth      DATE,
    user_type          VARCHAR(20)   NOT NULL DEFAULT 'resident',
    preferred_language VARCHAR(20)   NOT NULL DEFAULT 'english',
    profile_photo      VARCHAR(255),
    is_active          TINYINT(1)    NOT NULL DEFAULT 1,
    created_at         TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- DRIVERS
-- ─────────────────────────────────────────────
CREATE TABLE drivers (
    driver_id           INT AUTO_INCREMENT PRIMARY KEY,
    full_name           VARCHAR(100)  NOT NULL,
    phone_number        VARCHAR(15)   NOT NULL UNIQUE,
    gender              VARCHAR(10)   NOT NULL,
    license_number      VARCHAR(50)   NOT NULL,
    password_hash       VARCHAR(255)  NOT NULL,
    approval_status     VARCHAR(20)   NOT NULL DEFAULT 'pending',
    avg_rating          DECIMAL(3,2)  NOT NULL DEFAULT 0.00,
    daily_hours_worked  DECIMAL(4,2)  NOT NULL DEFAULT 0.00,
    is_online           TINYINT(1)    NOT NULL DEFAULT 0,
    current_lat         DECIMAL(10,8),
    current_lng         DECIMAL(11,8),
    created_at          TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- VEHICLES (is_pet_friendly already here)
-- ─────────────────────────────────────────────
CREATE TABLE vehicles (
    vehicle_id           INT AUTO_INCREMENT PRIMARY KEY,
    driver_id            INT           NOT NULL,
    vehicle_type         VARCHAR(20)   NOT NULL,
    registration_number  VARCHAR(20)   NOT NULL UNIQUE,
    model                VARCHAR(50)   NOT NULL,
    color                VARCHAR(30)   NOT NULL,
    is_pet_friendly      TINYINT(1)    NOT NULL DEFAULT 0,
    insurance_expiry     DATE          NOT NULL,
    seats                INT           NOT NULL DEFAULT 4,
    car_category         VARCHAR(30)   NOT NULL DEFAULT 'economy',
    is_active            TINYINT(1)    NOT NULL DEFAULT 1
);

-- ─────────────────────────────────────────────
-- ADMIN
-- ─────────────────────────────────────────────
CREATE TABLE admin (
    admin_id    INT AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(100)  NOT NULL,
    email       VARCHAR(100)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role        VARCHAR(20)   NOT NULL DEFAULT 'support',
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- RIDES (added: pet_friendly_required, requested_driver_id, driver_assign_mode)
-- ─────────────────────────────────────────────
CREATE TABLE rides (
    ride_id               INT AUTO_INCREMENT PRIMARY KEY,
    user_id               INT           NOT NULL,
    driver_id             INT,
    requested_driver_id   INT,                          -- whom user selected/requested
    vehicle_id            INT,
    pickup_location       VARCHAR(200)  NOT NULL,
    dropoff_location      VARCHAR(200)  NOT NULL,
    ride_type             VARCHAR(20)   NOT NULL DEFAULT 'car',
    car_type              VARCHAR(30)   NOT NULL DEFAULT 'economy_4seater',
    pet_friendly_required TINYINT(1)    NOT NULL DEFAULT 0,
    driver_assign_mode    VARCHAR(20)   NOT NULL DEFAULT 'manual',  -- 'manual' | 'auto'
    fare                  DECIMAL(8,2)  NOT NULL DEFAULT 0.00,
    payment_mode          VARCHAR(20)   NOT NULL DEFAULT 'cash',
    coupon_code           VARCHAR(30),
    discount_amount       DECIMAL(8,2)  NOT NULL DEFAULT 0.00,
    otp_verified          TINYINT(1)    NOT NULL DEFAULT 0,
    status                VARCHAR(20)   NOT NULL DEFAULT 'pending',  -- pending|accepted|rejected|confirmed|completed|cancelled
    booked_at             TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- DRIVER REQUESTS — notification table
-- When user requests a specific driver, a row goes here.
-- Driver can accept or reject from dashboard.
-- ─────────────────────────────────────────────
CREATE TABLE driver_requests (
    request_id   INT AUTO_INCREMENT PRIMARY KEY,
    ride_id      INT          NOT NULL,
    driver_id    INT          NOT NULL,
    user_id      INT          NOT NULL,
    status       VARCHAR(20)  NOT NULL DEFAULT 'pending',  -- pending|accepted|rejected
    notified_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP    NULL
);

-- ─────────────────────────────────────────────
-- RIDE PRICING
-- ─────────────────────────────────────────────
CREATE TABLE ride_pricing (
    pricing_id            INT AUTO_INCREMENT PRIMARY KEY,
    ride_type             VARCHAR(20)  NOT NULL UNIQUE,
    base_fare             DECIMAL(8,2) NOT NULL,
    per_km_rate           DECIMAL(6,2) NOT NULL,
    cancellation_fee      DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    free_cancel_window_min INT         NOT NULL DEFAULT 5
);

-- ─────────────────────────────────────────────
-- RATINGS
-- ─────────────────────────────────────────────
CREATE TABLE ratings (
    rating_id         INT AUTO_INCREMENT PRIMARY KEY,
    ride_id           INT  NOT NULL,
    rated_by_user_id  INT  NOT NULL,
    rated_driver_id   INT  NOT NULL,
    stars             INT  NOT NULL DEFAULT 5,
    feedback_text     VARCHAR(500),
    created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- COMPLAINTS
-- ─────────────────────────────────────────────
CREATE TABLE complaints (
    complaint_id      INT AUTO_INCREMENT PRIMARY KEY,
    ride_id           INT,
    filed_by_user_id  INT          NOT NULL,
    filed_by_type     VARCHAR(10)  NOT NULL DEFAULT 'user',
    subject           VARCHAR(200) NOT NULL,
    description       TEXT         NOT NULL,
    status            VARCHAR(20)  NOT NULL DEFAULT 'open',
    admin_response    TEXT,
    resolved_by       INT,
    resolved_at       TIMESTAMP    NULL,
    created_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- OTP VERIFICATIONS
-- ─────────────────────────────────────────────
CREATE TABLE otp_verifications (
    otp_id     INT AUTO_INCREMENT PRIMARY KEY,
    ride_id    INT         NOT NULL,
    otp_code   VARCHAR(6)  NOT NULL,
    is_used    TINYINT(1)  NOT NULL DEFAULT 0,
    created_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- COUPONS
-- ─────────────────────────────────────────────
CREATE TABLE coupons (
    coupon_id             INT AUTO_INCREMENT PRIMARY KEY,
    code                  VARCHAR(30)  NOT NULL UNIQUE,
    discount_percent      INT          NOT NULL DEFAULT 10,
    applicable_user_type  VARCHAR(20)  NOT NULL DEFAULT 'student',
    is_unlimited          TINYINT(1)   NOT NULL DEFAULT 1,
    is_active             TINYINT(1)   NOT NULL DEFAULT 1,
    created_at            TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────
-- SEED DATA
-- ─────────────────────────────────────────────
INSERT INTO admin (full_name, email, password_hash, role) VALUES
('Super Admin', 'admin@chalya.in', SHA1(CONCAT('chalya_admin_salt','admin123')), 'super_admin');

INSERT INTO ride_pricing (ride_type, base_fare, per_km_rate, cancellation_fee, free_cancel_window_min) VALUES
('car',     40.00,  12.00, 30.00,  5),
('bike',    20.00,   8.00, 15.00,  5),
('taxi',    50.00,  14.00, 35.00,  5),
('airport', 200.00, 16.00, 50.00, 10),
('rental',  500.00,  0.00, 100.00,15);

INSERT INTO coupons (code, discount_percent, applicable_user_type, is_unlimited) VALUES
('STUDENT10', 10, 'student', 1);

-- Pre-seeded drivers (password: driver123)
INSERT INTO drivers (full_name, phone_number, gender, license_number, password_hash, approval_status, avg_rating, is_online) VALUES
('Rajan Naik',        '9876543210', 'male',   'GA-DL-2023-001', SHA1(CONCAT('chalya_driver_salt','driver123')), 'approved', 4.80, 1),
('Priya Salgaonkar',  '9876543211', 'female', 'GA-DL-2023-002', SHA1(CONCAT('chalya_driver_salt','driver123')), 'approved', 4.90, 1);

-- Rajan: economy 4-seater, not pet friendly
-- Priya: premium 7-seater, pet friendly
INSERT INTO vehicles (driver_id, vehicle_type, registration_number, model, color, is_pet_friendly, insurance_expiry, seats, car_category) VALUES
(1, 'car', 'GA-01-AB-1234', 'Maruti Swift Dzire', 'White',  0, '2027-12-31', 4, 'economy'),
(2, 'car', 'GA-02-CD-5678', 'Toyota Innova Crysta','Silver', 1, '2027-12-31', 7, 'premium');

SHOW TABLES;
SELECT 'DB Ready' AS status;
