// Updated video.js functions to handle direct Vimeo video files

/**
 * Determine the type of video URL.
 * Returns 'youtube', 'vimeo', 'direct', or 'other'
 */
function getVideoType(url) {
  // Check if it's a direct video file first (including Vimeo direct files)
  if (isDirectVideoFile(url)) {
    return "direct";
  }

  if (/youtu\.?be/.test(url)) {
    return "youtube";
  } else if (
    /vimeo\.com/.test(url) &&
    !/player\.vimeo\.com\/progressive_redirect/.test(url)
  ) {
    return "vimeo";
  } else {
    return "other";
  }
}

/**
 * Extract YouTube ID from URL.
 */
function getYouTubeId(url) {
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
  const match = url.match(regExp);
  return match && match[2].length === 11 ? match[2] : null;
}

/**
 * Extract Vimeo ID from URL.
 */
function getVimeoId(url) {
  // Handle regular Vimeo URLs (not direct video files)
  const regExp = /vimeo\.com\/(?:.*\/)?(\d+)(?:\?.*)?$/;
  const match = url.match(regExp);
  return match ? match[1] : null;
}

/**
 * Build embed URL for any video type
 */
function getEmbedUrl(url) {
  const type = getVideoType(url);

  if (type === "youtube") {
    const id = getYouTubeId(url);
    return id ? `https://www.youtube.com/embed/${id}?autoplay=1` : url;
  } else if (type === "vimeo") {
    const id = getVimeoId(url);
    return id ? `https://player.vimeo.com/video/${id}?autoplay=1` : url;
  } else {
    // For direct video files, return as-is
    return url;
  }
}

/**
 * Get the thumbnail for a video, using custom thumbnail if provided
 */
function getThumbnail(url, customThumbnail = "") {
  const THUMBNAIL_PREFIX = "../uploads/video-gallery-thumbnails/";

  // If a custom thumbnail is provided, use it
  if (customThumbnail && customThumbnail.trim() !== "") {
    return THUMBNAIL_PREFIX + customThumbnail;
  }

  // Otherwise, use default thumbnail
  return "./images/default-thumbnail.jpg";
}

/**
 * Function to format duration.
 */
function formatDuration(duration) {
  if (!duration) return "—";
  return duration;
}

/**
 * Check if a video URL is a direct video file.
 */
function isDirectVideoFile(url) {
  const videoExtensions = [
    ".mp4",
    ".webm",
    ".ogg",
    ".mov",
    ".avi",
    ".mkv",
    ".flv",
  ];
  const lowerUrl = url.toLowerCase();
  return videoExtensions.some((ext) => lowerUrl.includes(ext));
}

/**
 * Get video file type from URL.
 */
function getVideoFileType(url) {
  const lowerUrl = url.toLowerCase();
  if (lowerUrl.includes(".mp4")) return "video/mp4";
  if (lowerUrl.includes(".webm")) return "video/webm";
  if (lowerUrl.includes(".ogg")) return "video/ogg";
  if (lowerUrl.includes(".mov")) return "video/quicktime";
  return "video/mp4"; // Default fallback
}
