<?php

session_start();

if(!isset($_SESSION['user_id'])){
    echo "Session expired. Please login again.";
    header("location: ../index.php");
    exit();
}

include("../includes/connect.inc.php");


$action = $_GET['action'] ?? "";

switch ($action) {
    case "fetch-videos":
        $SQL = "SELECT v.*, c1.name AS main_category_name, c2.name AS sub_category_name, d2.short_name AS division, u.full_name AS added_by_name, DATE_FORMAT(v.training_date, '%d-%m-%Y') AS fr_training_date,
        DATE_FORMAT(v.added_date, '%d-%m-%Y') AS added_date, IFNULL(vh.watch_count, 0) AS watch_count, c1.sort_order AS main_cat_sort_order, c2.sort_order AS sub_cat_sort_order
        FROM tbl_videos v
        LEFT JOIN tbl_video_categories c1 ON v.main_category=c1.id AND c1.type=1
        LEFT JOIN tbl_video_categories c2 ON v.sub_category=c2.id AND c2.type=2
        LEFT JOIN tbl_users u ON v.added_by=u.user_id
        LEFT JOIN tbl_dimensions d2 ON v.conducted_by=d2.id AND d2.dimension_type=2
        LEFT JOIN (SELECT v_id, COUNT(id) AS watch_count FROM tbl_video_history GROUP BY v_id) vh ON vh.v_id = v.id 
        WHERE v.status=1 ORDER BY v.sort_order ASC";

        $temp = array();
        $result = $mysqli->query($SQL);
        while ($row = $result->fetch_assoc()) {
            $temp[] = $row;
        }
        echo json_encode($temp);
        break;

    case "update-video-count":
        $input = json_decode(file_get_contents("php://input"), true);
        $video_id = $input["video_id"] ?? 0;
        $user_id = $_SESSION["user_id"] ?? 0;
        $date = date("Y-m-d");

        if ($video_id && $user_id) {
            $stmt = $mysqli->prepare("INSERT INTO tbl_video_history (v_id, user_id, attended_date) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $video_id, $user_id, $date);
            if ($stmt->execute()) {
                echo json_encode(["status" => "success"]);
            } else {
                echo json_encode(["status" => "error", "message" => "DB error"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid data"]);
        }
        exit();
        break;


    default:
        echo "No action found!";
        break;
}


?>