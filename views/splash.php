<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>चल या!!! PATRAO - Loading...</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --neon-cyan: #00f5ff;
            --neon-yellow: #FFD700;
            --neon-pink: #ff006e;
            --dark-bg: #0a0a0f;
            --dark-card: #111118;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: var(--dark-bg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
            cursor: pointer;
        }
        /* Stars background */
        .stars {
            position: fixed; inset: 0; pointer-events: none;
        }
        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            animation: twinkle 3s infinite;
        }
        @keyframes twinkle {
            0%,100% { opacity:0.3; transform:scale(1); }
            50% { opacity:1; transform:scale(1.5); }
        }
        /* Road */
        .road {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: 80px;
            background: #1a1a2e;
            border-top: 3px solid #333;
        }
        .road-line {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            height: 4px;
            background: var(--neon-yellow);
            animation: road-move 0.4s linear infinite;
        }
        @keyframes road-move {
            from { transform: translateY(-50%) translateX(0); }
            to { transform: translateY(-50%) translateX(-200px); }
        }
        /* Logo ring */
        .logo-ring {
            width: 220px; height: 220px;
            border-radius: 50%;
            border: 4px solid transparent;
            background: linear-gradient(var(--dark-card), var(--dark-card)) padding-box,
                        linear-gradient(135deg, var(--neon-cyan), var(--neon-yellow), var(--neon-pink)) border-box;
            display: flex; align-items: center; justify-content: center;
            animation: ring-rotate 4s linear infinite;
            position: relative;
            margin-bottom: 30px;
            box-shadow: 0 0 40px rgba(0,245,255,0.3);
        }
        @keyframes ring-rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .logo-inner {
            width: 180px; height: 180px;
            border-radius: 50%;
            background: var(--dark-card);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            animation: ring-rotate 4s linear infinite reverse;
            border: 2px solid rgba(0,245,255,0.2);
        }
        .logo-car-icon {
            font-size: 4rem;
            color: var(--neon-cyan);
            text-shadow: 0 0 20px var(--neon-cyan), 0 0 40px var(--neon-cyan);
            animation: car-bounce 1s ease-in-out infinite;
        }
        @keyframes car-bounce {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .logo-tagline-small {
            color: var(--neon-yellow);
            font-size: 0.6rem;
            letter-spacing: 2px;
            margin-top: 4px;
        }
        /* App name */
        .app-name {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--neon-cyan), var(--neon-yellow));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            line-height: 1.1;
            letter-spacing: 2px;
            text-shadow: none;
            filter: drop-shadow(0 0 20px rgba(0,245,255,0.5));
            animation: name-pulse 2s ease-in-out infinite;
        }
        @keyframes name-pulse {
            0%,100% { filter: drop-shadow(0 0 10px rgba(0,245,255,0.5)); }
            50% { filter: drop-shadow(0 0 30px rgba(0,245,255,0.9)); }
        }
        .tagline-text {
            color: var(--neon-yellow);
            font-size: 1.1rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-top: 10px;
            text-shadow: 0 0 10px var(--neon-yellow);
            animation: tagline-glow 2s ease-in-out infinite;
        }
        @keyframes tagline-glow {
            0%,100% { opacity: 0.7; }
            50% { opacity: 1; }
        }
        .tap-hint {
            color: rgba(255,255,255,0.4);
            font-size: 0.9rem;
            margin-top: 40px;
            letter-spacing: 3px;
            text-transform: uppercase;
            animation: hint-blink 1.5s ease-in-out infinite;
        }
        @keyframes hint-blink {
            0%,100% { opacity:0.3; }
            50% { opacity:0.8; }
        }
        /* Racing car animation on click */
        .racing-car {
            position: fixed;
            bottom: 45px;
            left: -300px;
            font-size: 3rem;
            color: var(--neon-pink);
            text-shadow: 0 0 20px var(--neon-pink), 0 0 40px var(--neon-pink);
            z-index: 999;
            display: none;
            filter: drop-shadow(0 0 15px var(--neon-pink));
        }
        .racing-car.animate {
            display: block;
            animation: race-across 0.8s cubic-bezier(0.1, 0, 0.9, 1) forwards;
        }
        @keyframes race-across {
            0% { left: -300px; opacity:1; }
            70% { left: 60%; opacity:1; }
            85% { left: 75%; opacity:0.7; }
            100% { left: 110%; opacity:0; }
        }
        .speed-lines {
            position: fixed;
            bottom: 60px;
            left: 0;
            width: 100%;
            height: 20px;
            display: none;
            overflow: hidden;
        }
        .speed-lines.animate {
            display: block;
        }
        .speed-line {
            position: absolute;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--neon-pink), transparent);
            animation: speed-line-anim 0.5s ease-out forwards;
        }
        @keyframes speed-line-anim {
            from { transform: scaleX(0); opacity: 1; }
            to { transform: scaleX(1); opacity: 0; }
        }
        /* Flash overlay */
        .flash {
            position: fixed; inset:0;
            background: white;
            opacity: 0;
            pointer-events: none;
            z-index: 9999;
            transition: opacity 0.1s;
        }
        .flash.active {
            opacity: 0.8;
        }
    </style>
</head>
<body id="splashBody">
    <div class="stars" id="stars"></div>
    <div class="road">
        <div class="road-line" style="width:200px; left:0;"></div>
        <div class="road-line" style="width:200px; left:250px;"></div>
        <div class="road-line" style="width:200px; left:500px;"></div>
        <div class="road-line" style="width:200px; left:750px;"></div>
        <div class="road-line" style="width:200px; left:1000px;"></div>
        <div class="road-line" style="width:200px; left:1250px;"></div>
    </div>

    <div class="logo-ring">
        <div class="logo-inner">
            <div class="logo-car-icon"><i class="fas fa-car-side"></i></div>
            <div class="logo-tagline-small">ON TIME · EVERY TIME</div>
        </div>
    </div>

    <div class="app-name">चल या!!! PATRAO</div>
    <div class="tagline-text">⚡ Goa's Fastest Ride ⚡</div>
    <div class="tap-hint">Tap anywhere to enter</div>

    <div class="racing-car" id="racingCar"><i class="fas fa-car-side"></i></div>
    <div class="speed-lines" id="speedLines">
        <div class="speed-line" style="top:0; left:0; width:60%; animation-delay:0s;"></div>
        <div class="speed-line" style="top:8px; left:5%; width:50%; animation-delay:0.05s;"></div>
        <div class="speed-line" style="top:16px; left:10%; width:70%; animation-delay:0.1s;"></div>
    </div>
    <div class="flash" id="flashEl"></div>

    <script>
    // Generate stars
    const starsEl = document.getElementById('stars');
    for (let i = 0; i < 80; i++) {
        const s = document.createElement('div');
        s.className = 'star';
        s.style.cssText = `width:${Math.random()*3+1}px; height:${Math.random()*3+1}px;
            top:${Math.random()*80}%; left:${Math.random()*100}%;
            animation-delay:${Math.random()*3}s; animation-duration:${2+Math.random()*3}s;`;
        starsEl.appendChild(s);
    }

    // Click to animate and redirect
    document.getElementById('splashBody').addEventListener('click', function() {
        const car = document.getElementById('racingCar');
        const lines = document.getElementById('speedLines');
        const flash = document.getElementById('flashEl');

        // Trigger racing car
        car.classList.add('animate');
        lines.classList.add('animate');

        // Flash then redirect
        setTimeout(() => {
            flash.classList.add('active');
            setTimeout(() => {
                window.location.href = 'index.php?page=home';
            }, 150);
        }, 700);
    });
    </script>
</body>
</html>
