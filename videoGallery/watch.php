<?php
include("../includes/connect.inc.php");

$video_id = isset($_GET['v']) ? intval($_GET['v']) : 0;
$video = null;

if ($video_id > 0) {
    $SQL = "SELECT v.*, c1.name AS main_category_name, c2.name AS sub_category_name, d2.short_name AS division, u.full_name AS added_by_name, DATE_FORMAT(v.training_date, '%d-%m-%Y') AS fr_training_date,
    DATE_FORMAT(v.added_date, '%d-%m-%Y') AS added_date, IFNULL(vh.watch_count, 0) AS watch_count, c1.sort_order AS main_cat_sort_order, c2.sort_order AS sub_cat_sort_order
    FROM tbl_videos v
    LEFT JOIN tbl_video_categories c1 ON v.main_category=c1.id AND c1.type=1
    LEFT JOIN tbl_video_categories c2 ON v.sub_category=c2.id AND c2.type=2
    LEFT JOIN tbl_users u ON v.added_by=u.user_id
    LEFT JOIN tbl_dimensions d2 ON v.conducted_by=d2.id AND d2.dimension_type=2
    LEFT JOIN (SELECT v_id, COUNT(id) AS watch_count FROM tbl_video_history GROUP BY v_id) vh ON vh.v_id = v.id 
    WHERE v.status=1 AND v.id = ? LIMIT 1";
    
    if ($stmt = $mysqli->prepare($SQL)) {
        $stmt->bind_param("i", $video_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $video = $result->fetch_assoc();
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Watch Video - EFFISM</title>
    <link rel="stylesheet" href="./css/style.css" />
    
    <style>
        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Lock viewport scrolling on desktop */
            background-color: #f9fafb;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: stretch;
        }

        .watch-page-container {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        /* Top Header Branding Row */
        .watch-brand-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background-color: white;
            box-sizing: border-box;
            height: 55px; /* Fixed height for top header */
            flex-shrink: 0;
        }

        .watch-brand-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-color);
            letter-spacing: 0.5px;
            position: relative;
            padding-left: 12px;
        }

        .watch-brand-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 18px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }

        .watch-brand-badge {
            background-color: var(--primary-light);
            color: var(--primary-color);
            border: 1px solid rgba(26, 123, 159, 0.15);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* 100% split screen layout block */
        .watch-container {
            width: 100%;
            height: calc(100vh - 55px); /* Span exactly the remaining window height */
            background-color: white;
            display: flex;
            flex-direction: row;
            align-items: stretch;
            flex-grow: 1;
            border: none;
            border-radius: 0;
            box-shadow: none;
        }

        .watch-video-player {
            flex: 0 0 70%; /* 70% width player */
            max-width: 70%;
            height: 100%; /* Span full height */
            background-color: #000;
            position: relative;
        }

        .watch-video-player iframe,
        .watch-video-player video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            object-fit: fill; /* Ensure video stretches fully to edges */
        }

        .watch-video-info {
            flex: 0 0 30%; /* 30% width details */
            max-width: 30%;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            overflow-y: auto; /* Independent vertical scroll */
            box-sizing: border-box;
            border-left: 1px solid var(--border-color);
            height: 100%;
        }

        .watch-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text-color);
            line-height: 1.35;
        }

        .watch-video-info .modal-meta {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .watch-description {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--text-color);
            margin-top: 0.5rem;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .watch-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: auto;
            padding-top: 1rem;
        }

        .watch-tag {
            font-size: 0.8rem;
            padding: 0.3rem 0.8rem;
            background-color: var(--primary-light);
            border-radius: 0.7rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        /* Status messages centered inside full screen container */
        .status-message {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            box-sizing: border-box;
            color: var(--text-light);
        }

        .status-message h3 {
            font-size: 1.6rem;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .status-message p {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .btn-gallery {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.75rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-gallery:hover {
            background-color: var(--hover-color);
        }

        /* Fallback stacked layout on smaller screens */
        @media (max-width: 900px) {
            html, body {
                overflow: auto; /* Restore scroll */
            }

            .watch-page-container {
                height: auto;
            }

            .watch-brand-header {
                height: auto;
                padding: 1rem;
            }

            .watch-container {
                flex-direction: column;
                height: auto;
            }

            .watch-video-player {
                flex: 0 0 auto;
                max-width: 100%;
                aspect-ratio: 16 / 9;
                height: auto;
            }

            .watch-video-info {
                flex: 0 0 auto;
                max-width: 100%;
                padding: 1.5rem;
                border-left: none;
                border-top: 1px solid var(--border-color);
                height: auto;
                overflow-y: visible;
            }
            
            .watch-video-info .modal-meta {
                flex-direction: row;
                flex-wrap: wrap;
            }
        }
    </style>
    
    <!-- Inject current video details directly into JS context -->
    <script>
        window.currentVideo = <?php echo $video ? json_encode($video) : 'null'; ?>;
    </script>
</head>

<body>
    <div class="watch-page-container">
        <!-- Branding Header -->
        <div class="watch-brand-header">
            <div class="watch-brand-title">Video Gallery</div>
            <div class="watch-brand-badge">Public Video</div>
        </div>

        <div class="watch-container" id="watch-container">
            <div style="padding: 4rem; text-align: center; color: var(--text-light); font-size: 1.1rem; font-weight: 500; width: 100%;">
                Loading video player...
            </div>
        </div>
    </div>

    <!-- Load JavaScript files -->
    <script src="https://www.youtube.com/iframe_api"></script>
    <script src="./js/video.js"></script>
    <script src="./js/watch.js"></script>
</body>

</html>
