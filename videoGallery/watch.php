<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Watch Video - EFFISM</title>
    <!-- Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-gradient: radial-gradient(circle at top, #1e293b 0%, #0f172a 100%);
            --card-bg: rgba(30, 41, 59, 0.7);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-body: #cbd5e1;
            --primary-color: #1a7b9f;
            --accent-color: #38bdf8;
            --shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: var(--bg-gradient);
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 2rem 1.5rem;
        }

        .watch-page-container {
            width: 100%;
            max-width: 960px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Branding Header */
        .brand-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 0.5rem;
        }

        .brand-logo-text {
            font-family: 'Outfit', sans-serif;
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            background: linear-gradient(135deg, #38bdf8, #1a7b9f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
        }

        .brand-badge {
            background: rgba(56, 189, 248, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.2);
            color: #38bdf8;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .watch-container {
            width: 100%;
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            overflow: hidden;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: var(--shadow), 0 0 40px rgba(26, 123, 159, 0.1);
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }

        .watch-container:hover {
            border-color: rgba(56, 189, 248, 0.2);
            box-shadow: var(--shadow), 0 0 50px rgba(26, 123, 159, 0.15);
        }

        .watch-video-player {
            width: 100%;
            background-color: #020617;
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
        }

        .watch-video-player iframe,
        .watch-video-player video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .watch-video-info {
            padding: 3rem;
        }

        .watch-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            color: var(--text-primary);
            line-height: 1.35;
            letter-spacing: -0.5px;
        }

        .watch-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 500;
            transition: var(--transition);
        }

        .meta-pill:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.12);
            color: var(--text-primary);
        }

        .meta-icon {
            font-size: 1rem;
            color: var(--accent-color);
        }

        .watch-description {
            font-size: 1.05rem;
            line-height: 1.7;
            color: var(--text-body);
            margin-bottom: 2rem;
            white-space: pre-line;
        }

        .watch-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .watch-tag {
            font-size: 0.8rem;
            padding: 0.35rem 1rem;
            background: rgba(26, 123, 159, 0.15);
            border: 1px solid rgba(26, 123, 159, 0.25);
            border-radius: 9999px;
            color: #38bdf8;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: var(--transition);
        }

        .watch-tag:hover {
            background: rgba(26, 123, 159, 0.25);
            border-color: #38bdf8;
            transform: translateY(-1px);
        }

        .status-message {
            padding: 5rem 3rem;
            text-align: center;
            color: var(--text-secondary);
        }

        .status-message h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .status-message p {
            margin-bottom: 2rem;
            font-size: 1.05rem;
        }

        .btn-gallery {
            display: inline-flex;
            align-items: center;
            padding: 0.8rem 1.8rem;
            background: linear-gradient(135deg, #1a7b9f, #135e79);
            color: white;
            text-decoration: none;
            border-radius: 9999px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(26, 123, 159, 0.3);
            transition: var(--transition);
        }

        .btn-gallery:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(26, 123, 159, 0.4);
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            .brand-header {
                padding: 0;
            }
            .watch-video-info {
                padding: 2rem;
            }
            .watch-title {
                font-size: 1.7rem;
            }
        }
    </style>
</head>

<body>
    <div class="watch-page-container">
        <!-- Branding Header -->
        <div class="brand-header">
            <div class="brand-logo-text">EFFISM Video Hub</div>
            <div class="brand-badge">Public Link</div>
        </div>

        <div class="watch-container" id="watch-container">
            <div style="padding: 5rem; text-align: center; color: var(--text-secondary); font-size: 1.1rem; font-weight: 500;">
                Initializing immersive player...
            </div>
        </div>
    </div>

    <!-- Load JavaScript files -->
    <script src="https://www.youtube.com/iframe_api"></script>
    <script src="./js/data.js"></script>
    <script src="./js/video.js"></script>
    <script src="./js/watch.js"></script>
</body>

</html>
