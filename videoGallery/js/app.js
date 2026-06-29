// Application initialization and event binding.
// Sets up the application on page load and connects all the components.

// Initialize the page
document.addEventListener("DOMContentLoaded", function () {
  // Set default view to "recently added"
  currentView = "featured";

  // Add event listeners to view tabs
  document.querySelectorAll(".view-tab").forEach((tab) => {
    tab.addEventListener("click", function () {
      if (typeof hideInlinePlayer === "function") {
        hideInlinePlayer();
      }
      const viewType = this.getAttribute("data-view");
      switchView(viewType);
    });
  });

  // Explicitly sort and render videos with the initial view
  filterVideos();

  // Add event listener to search input
  document
    .getElementById("search-input")
    .addEventListener("input", function() {
      if (typeof hideInlinePlayer === "function") {
        hideInlinePlayer();
      }
      filterVideos();
    });

  // Add event listener to close modal
  document
    .getElementById("close-modal")
    .addEventListener("click", closeVideoModal);

  // Close modal when clicking outside the content
  document
    .getElementById("video-modal")
    .addEventListener("click", function (e) {
      if (e.target === this) {
        closeVideoModal();
      }
    });

  // Close modal with escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      closeVideoModal();
    }
  });

  // Handle browser back/forward buttons
  window.addEventListener("popstate", function (e) {
    const urlParams = new URLSearchParams(window.location.search);
    const videoId = urlParams.get("v");
    if (videoId) {
      if (window.videosData) {
        const video = window.videosData.find(v => String(v.id) === String(videoId));
        if (video && typeof showInlinePlayer === "function") {
          showInlinePlayer(video, true);
          return;
        }
      }
    }
    if (typeof hideInlinePlayer === "function") {
      hideInlinePlayer(true);
    }
  });

  // Auto-open video if "v" query param is present on load
  if (typeof checkAndAutoOpenVideo === "function") {
    checkAndAutoOpenVideo();
  }
});
