# चल या!!! PATRAO — Enhanced Ride Booking App
## Goa's #1 Ride Hailing Platform

### ✅ New Features in This Version

| Feature | Details |
|---|---|
| 🎬 Animated Splash Screen | Rotating logo, racing car animation on click, neon stars |
| 🌙 Dark / Light Mode | Full dark mode with neon accents (cyan, yellow, pink, green, purple). Toggle in top-right |
| 🚗 Car Category Filters | Horizontal scroll chips: Economy, Bike, Taxi, Airport, Rental |
| 🛻 Car Variety Grid | 4-seater, 7-seater, AC, Premium, etc. per category |
| 📍 Location Suggestions | 27 predefined Goa locations + free-text input |
| 🗺️ Live Map (User) | Google Maps embed on booking & ride tracking pages |
| 🗺️ Driver Navigation Map | Full map page for driver with active ride passenger location |
| 🎓 Student Coupon | STUDENT10 — 10% off, unlimited, auto-shown for student users |
| 🔐 6-Digit OTP | OTP screen after payment — demo mode accepts any 6-digit code |
| 💪 Password Strength | Weak / Fair / Strong meter on register & driver register |
| 🚨 Complaint System | Users file complaints linked to rides; admin sees & resolves them |
| ⭐ Ride Reviews | Star rating + feedback for completed rides |
| 👨‍✈️ 2 Pre-set Drivers | Rajan Naik (ph: 9876543210) and Priya Salgaonkar (ph: 9876543211) — pass: driver123 |

### 🗂️ MVC Structure
```
chalya_patrao_v2/
├── index.php               ← Front controller / router
├── database.sql            ← Full schema + seed data
├── models/
│   ├── Database.php        ← DB connection wrapper
│   ├── config/config.php   ← DB credentials
│   ├── UserModel.php
│   ├── DriverModel.php
│   ├── BookingModel.php
│   ├── ComplaintModel.php  ← Includes resolveComplaint() with admin response
│   ├── RatingModel.php
│   └── AdminModel.php
├── controllers/
│   ├── UserController.php
│   ├── BookingController.php  ← OTP flow, coupon validation
│   ├── DriverController.php   ← Includes handleDriverMap()
│   ├── FeatureController.php
│   └── AdminController.php    ← Includes handleAdminComplaints()
├── views/
│   ├── layout/
│   │   ├── header.php      ← Dark/Light toggle, animated logo
│   │   └── footer.php      ← Theme JS, hamburger
│   ├── splash.php          ← Animated splash screen
│   ├── home.php
│   ├── booking.php         ← Car filters + map + suggestions + coupon
│   ├── select_driver.php
│   ├── otp_verify.php      ← 6-digit OTP screen
│   ├── payment.php
│   ├── thankyou.php
│   ├── my_rides.php
│   ├── track_ride.php      ← Map embed
│   ├── rate_ride.php       ← Interactive stars
│   ├── complaint.php       ← File + view complaints with admin response
│   ├── driver_map.php      ← Driver navigation map
│   ├── driver_dashboard.php
│   ├── admin_complaints.php ← Resolve complaints
│   └── ... (all other views)
└── public/css/style.css    ← Full dark/light CSS with neon theme
```

### 🚀 Setup
1. Import `database.sql` into your MySQL database
2. Edit `models/config/config.php` with your credentials
3. Point your web server to the project root
4. Visit `index.php` — you'll see the splash screen

### 👤 Default Logins
| Role | Credential |
|---|---|
| Admin | admin@chalya.in / admin123 |
| Driver 1 | Phone: 9876543210 / driver123 |
| Driver 2 | Phone: 9876543211 / driver123 |
| Student | Register with user_type = student → get STUDENT10 coupon |

### 🎨 Theme
- **Dark Mode**: Neon cyan (#00f5ff), neon yellow (#FFD700), neon pink (#ff006e), neon green (#00ff88)
- **Light Mode**: Blue (#1a73e8) based, clean white/grey
- Toggle saved to localStorage — persists across sessions
