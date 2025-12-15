<?php
require_once 'config/config.php';

// Set 404 header
http_response_code(404);

$siteName = getSetting('site_name', 'YBT Digital');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | <?php echo $siteName; ?></title>
    
    <!-- Material Design Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(66, 135, 245, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(147, 51, 234, 0.15) 0%, transparent 50%);
            pointer-events: none;
        }

        .error-container {
            text-align: center;
            color: #e8eaed;
            padding: 3rem 2rem;
            max-width: 650px;
            animation: fadeIn 0.6s ease-in;
            background: linear-gradient(145deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.98));
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.5),
                        inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 10;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-code {
            font-size: 10rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #4287f5 0%, #9333ea 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 40px rgba(66, 135, 245, 0.3);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .error-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            color: #4287f5;
            filter: drop-shadow(0 0 20px rgba(66, 135, 245, 0.5));
            animation: rotate 3s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #ffffff;
            text-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .error-message {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            color: #cbd5e1;
            line-height: 1.6;
        }

        .error-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .btn-custom {
            padding: 0.875rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-home {
            background: linear-gradient(135deg, #4287f5 0%, #9333ea 100%);
            color: white;
        }

        .btn-home:hover {
            background: linear-gradient(135deg, #3b78e7 0%, #8b2dd6 100%);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(66, 135, 245, 0.4);
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.05);
            color: #e8eaed;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }

        .redirect-info {
            font-size: 0.95rem;
            color: #94a3b8;
            margin-top: 2rem;
        }

        .countdown {
            font-weight: 700;
            font-size: 1.2rem;
            background: linear-gradient(135deg, #4287f5 0%, #9333ea 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            animation: float 6s infinite;
            filter: blur(40px);
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-100px) rotate(180deg);
            }
        }

        .particle:nth-child(1) { 
            width: 120px; 
            height: 120px; 
            left: 10%; 
            top: 20%; 
            background: radial-gradient(circle, rgba(66, 135, 245, 0.3) 0%, transparent 70%);
            animation-delay: 0s; 
        }
        .particle:nth-child(2) { 
            width: 90px; 
            height: 90px; 
            right: 15%; 
            top: 30%; 
            background: radial-gradient(circle, rgba(147, 51, 234, 0.3) 0%, transparent 70%);
            animation-delay: 1s; 
        }
        .particle:nth-child(3) { 
            width: 140px; 
            height: 140px; 
            left: 20%; 
            bottom: 20%; 
            background: radial-gradient(circle, rgba(66, 135, 245, 0.2) 0%, transparent 70%);
            animation-delay: 2s; 
        }
        .particle:nth-child(4) { 
            width: 100px; 
            height: 100px; 
            right: 10%; 
            bottom: 30%; 
            background: radial-gradient(circle, rgba(147, 51, 234, 0.25) 0%, transparent 70%);
            animation-delay: 1.5s; 
        }

        @media (max-width: 768px) {
            .error-code {
                font-size: 6rem;
            }

            .error-title {
                font-size: 1.8rem;
            }

            .error-message {
                font-size: 1rem;
            }

            .error-buttons {
                flex-direction: column;
            }

            .btn-custom {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Particles -->
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-compass"></i>
        </div>
        
        <div class="error-code">404</div>
        
        <h1 class="error-title">Oops! Page Not Found</h1>
        
        <p class="error-message">
            We couldn't find the page you're looking for. It might have been moved, deleted, or the URL might be incorrect.
            <br>
            <strong>Please check the URL for any typos.</strong>
        </p>
        
        <div class="error-buttons">
            <a href="<?php echo SITE_URL; ?>" class="btn-custom btn-home">
                <i class="fas fa-home"></i>
                Go to Homepage
            </a>
            <a href="javascript:history.back()" class="btn-custom btn-back">
                <i class="fas fa-arrow-left"></i>
                Go Back
            </a>
        </div>
        
        <div class="redirect-info">
            <i class="fas fa-info-circle"></i>
            Redirecting to homepage in <span class="countdown" id="countdown">3</span> seconds...
        </div>
    </div>

    <script>
        // Countdown and redirect
        let seconds = 3;
        const countdownElement = document.getElementById('countdown');
        
        const countdownInterval = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdownInterval);
                window.location.href = '<?php echo SITE_URL; ?>';
            }
        }, 1000);
        
        // Stop countdown if user interacts with the page
        document.querySelectorAll('.btn-custom').forEach(btn => {
            btn.addEventListener('click', () => {
                clearInterval(countdownInterval);
            });
        });
    </script>
</body>
</html>