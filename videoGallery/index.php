<?php

// session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("location: ../index.php");
//     exit();
// }

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
                <div class="filter-container">
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