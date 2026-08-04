<?php
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Internal Error — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-100: #f1f5f9;
            --orange: #f97316;
            --orange-dark: #ea580c;
            --radius-md: 12px;
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .error-card {
            background: #ffffff;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            max-width: 480px;
            width: 100%;
            padding: 48px 36px;
            text-align: center;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .error-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: #fff7ed;
            color: var(--orange);
            border-radius: 50%;
            margin-bottom: 24px;
        }
        .error-icon svg {
            width: 40px;
            height: 40px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }
        .error-code {
            font-size: 80px;
            font-weight: 800;
            line-height: 1;
            color: var(--slate-900);
            margin-bottom: 16px;
            background: linear-gradient(135deg, var(--orange-dark), var(--orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .error-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--slate-800);
            margin-bottom: 12px;
        }
        .error-description {
            font-size: 15px;
            color: var(--slate-600);
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--slate-900);
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn:hover {
            background: var(--slate-800);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div class="error-code">500</div>
        <h1 class="error-title">Internal Server Error</h1>
        <p class="error-description">Something went wrong on our servers. We are currently performing maintenance, please check back in a few moments.</p>
        <a href="index.php?route=users" class="btn">
            <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Dashboard
        </a>
    </div>
</body>
</html>
