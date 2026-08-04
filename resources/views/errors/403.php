<?php
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-100: #f1f5f9;
            --red: #ef4444;
            --red-dark: #dc2626;
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
            background: #fef2f2;
            color: var(--red);
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
            background: linear-gradient(135deg, var(--red-dark), var(--red));
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
            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div class="error-code">403</div>
        <h1 class="error-title">Access Forbidden</h1>
        <p class="error-description">You don't have permission to access this resource. Please make sure you are logged in with the correct role privilege levels.</p>
        <a href="index.php?route=users" class="btn">
            <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Dashboard
        </a>
    </div>
</body>
</html>
