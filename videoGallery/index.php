<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: ../index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Video Gallery - EFFISM</title>
    <link rel="stylesheet" href="./css/style.css" />
</head>

<body>
    <div class="page-container">
        <!-- Side Navbar -->
        <div class="side-navbar">
            <div class="navbar-header">
                <div class="navbar-title">Video Gallery</div>
            </div>
            <ul class="category-list" id="category-list">
                <!-- Categories will be populated here -->
            </ul>
            <div class="back-to-effism-container">
                <a href="https://www.effism.com/jobdiary.php" class="back-to-effism">
                    Back to JobDiary
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="videoGallery-container">
            <div class="content-container">
                <div class="filter-container" id="filter-container">
                    <div class="view-tabs">
                        <div class="view-tab" data-view="all">All</div>
                        <div class="view-tab active" data-view="featured">Featured</div>
                        <div class="view-tab" data-view="recent">Recently Added</div>
                        <div class="view-tab" data-view="most-viewed">Most Viewed</div>
                    </div>
                    <div class="search-filter-bar">
                        <div class="search-input">
                            <input type="text" id="search-input" placeholder="Search..." />
                        </div>
                    </div>
                </div>

                <div class="video-gallery" id="video-gallery">
                    <!-- Videos will be populated here -->
                </div>

                <!-- Inline Video Player Container -->
                <div class="inline-player-container" id="inline-player-container" style="display: none; animation: fadeIn 0.4s ease-out;">
                    <div class="inline-player-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem;">
                        <button id="back-to-gallery-btn" class="back-pill-btn">
                            ← Back to Gallery
                        </button>
                        <button id="inline-copy-link-btn" class="copy-pill-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            <span>Copy Share Link</span>
                        </button>
                    </div>
                    <div class="watch-container-inline" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); display: flex; flex-direction: column; border: 1px solid var(--border-color);">
                        <div class="watch-video-player-wrapper" style="position: relative; padding-bottom: 56.25%; height: 0; background: black; overflow: hidden; width: 100%;">
                            <!-- Player will be dynamically loaded here -->
                        </div>
                        <div class="watch-video-info" id="inline-video-info" style="padding: 2.5rem 2rem 2rem 2rem;">
                            <!-- Video info will go here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="video-modal" id="video-modal">
        <div class="close-modal" id="close-modal">×</div>
        <div class="modal-content">
            <div class="video-iframe-container">
                <iframe id="video-iframe" allowfullscreen></iframe>
            </div>
            <div class="modal-video-info" id="modal-video-info">
                <!-- Video details will be populated here -->
            </div>
        </div>
    </div>

    <!-- Load JavaScript files -->
    <script src="https://www.youtube.com/iframe_api"></script>
    <script src="./js/data.js"></script>
    <script src="./js/video.js"></script>
    <script src="./js/filters.js"></script>
    <script src="./js/ui.js"></script>
    <script src="./js/app.js"></script>
</body>

</html>