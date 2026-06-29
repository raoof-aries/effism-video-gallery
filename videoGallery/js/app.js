// Application initialization and event binding.
// Sets up the application on page load and connects all the components.

// Initialize the page
document.addEventListener("DOMContentLoaded", function () {
  // Populate side navbar
  // populateSideNavbar();

  // Set default view to "recently added"
  currentView = "featured";

  // Add event listeners to view tabs
  document.querySelectorAll(".view-tab").forEach((tab) => {
    tab.addEventListener("click", function () {
      const viewType = this.getAttribute("data-view");
      switchView(viewType);
    });
  });

  // Explicitly sort and render videos with the initial view
  filterVideos();

  // Add event listener to search input
  document
    .getElementById("search-input")
    .addEventListener("input", filterVideos);

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
});
