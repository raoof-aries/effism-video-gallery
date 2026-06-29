// watch.js - Handles watch page initialization and video rendering without session dependency

document.addEventListener("DOMContentLoaded", function () {
  initWatchPage();
});

function initWatchPage() {
  const urlParams = new URLSearchParams(window.location.search);
  const videoId = urlParams.get("v");

  if (!videoId) {
    document.getElementById("watch-container").innerHTML = `
      <div class="status-message">
        <h3>No Video Specified</h3>
        <p>Please select a video from the main gallery to watch.</p>
        <a href="index.php" class="btn-gallery">Return to Gallery</a>
      </div>
    `;
    return;
  }

  // If videosData is not yet loaded, wait and retry
  if (!window.videosData || window.videosData.length === 0) {
    setTimeout(initWatchPage, 100);
    return;
  }

  const video = window.videosData.find((v) => String(v.id) === String(videoId));

  if (!video) {
    document.getElementById("watch-container").innerHTML = `
      <div class="status-message">
        <h3>Video Not Found</h3>
        <p>The requested video does not exist or has been removed.</p>
        <a href="index.php" class="btn-gallery">Return to Gallery</a>
      </div>
    `;
    return;
  }

  renderWatchVideo(video);
}

function renderWatchVideo(videoInfo) {
  const container = document.getElementById("watch-container");
  const videoType = getVideoType(videoInfo.video_link);

  // Set document title to the video topic
  document.title = `${videoInfo.topic} - Video Gallery`;

  let playerHtml = "";
  if (videoType === "youtube") {
    const videoId = getYouTubeId(videoInfo.video_link);
    const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
    playerHtml = `<iframe src="${embedUrl}" frameborder="0" allowfullscreen style="width: 100%; height: 100%; object-fit: fill;"></iframe>`;
  } else if (videoType === "vimeo") {
    const videoId = getVimeoId(videoInfo.video_link);
    const embedUrl = `https://player.vimeo.com/video/${videoId}?autoplay=1`;
    playerHtml = `<iframe src="${embedUrl}" frameborder="0" allowfullscreen style="width: 100%; height: 100%; object-fit: fill;"></iframe>`;
  } else if (videoType === "direct" || videoType === "other") {
    playerHtml = `
      <video controls autoplay style="width: 100%; height: 100%; object-fit: fill;">
        <source src="${videoInfo.video_link}" type="${getVideoFileType(videoInfo.video_link)}">
        Your browser does not support the video tag.
      </video>
    `;
  }

  container.innerHTML = `
    <div class="watch-video-player">
      ${playerHtml}
    </div>
    <div class="watch-video-info">
      <h1 class="watch-title">${videoInfo.topic}</h1>
      <div class="modal-meta">
        <div class="modal-meta-item"><span class="meta-icon">⏱️</span> ${formatDuration(videoInfo.topic_duration)} hrs</div>
        <div class="modal-meta-item"><span class="meta-icon">👁️</span> ${videoInfo.watch_count} views</div>
        <div class="modal-meta-item"><span class="meta-icon">📅</span> ${videoInfo.fr_training_date || "—"}</div>
      </div>
      <div class="watch-description">${videoInfo.video_description}</div>
      <div class="watch-tags">
        ${videoInfo.main_category_name ? `<span class="watch-tag">${videoInfo.main_category_name}</span>` : ""}
        ${videoInfo.sub_category_name ? `<span class="watch-tag">${videoInfo.sub_category_name}</span>` : ""}
      </div>
    </div>
  `;

  // Update views counter in database
  if (videoType === "direct" || videoType === "other") {
    const videoElement = container.querySelector("video");
    if (videoElement) {
      videoElement.addEventListener("ended", function () {
        sendVideoComplete(videoInfo.id);
      });
    }
  } else {
    // For YouTube and Vimeo, update views counter immediately on load
    sendVideoComplete(videoInfo.id);
  }
}

function sendVideoComplete(videoId) {
  console.log("Sending videoId complete event:", videoId);
  fetch("backend.php?action=update-video-count", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      video_id: videoId,
    }),
  })
    .then((res) => res.json())
    .then((data) => console.log("Video complete event recorded:", data))
    .catch((err) => console.error("Failed to send complete event:", err));
}
