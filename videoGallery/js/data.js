// data.js

// Initialize videosData with empty array
var videosData = [];

// Immediately execute this function to load the data before DOM is ready
(function loadData() {
  // Fetch from the local backend.php
  fetch("backend.php?action=fetch-videos", {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  })
    .then((response) => {
      // Check if response is OK before trying to parse JSON
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.text(); // Get response as text first
    })
    .then((text) => {
      // Check if there's actual content before parsing
      if (!text || text.trim() === "") {
        console.warn("Empty response received");
        return [];
      }

      try {
        // Try to parse the text as JSON
        return JSON.parse(text);
      } catch (e) {
        console.error("JSON parsing error:", e, "Response was:", text);
        return []; // Return empty array on parse error
      }
    })
    .then((data) => {
      // Update the global videosData variable if we have valid fetched data
      if (data && data.length > 0) {
        videosData = data;
      } else {
        videosData = [];
      }

      // Populate sidebar AFTER data is loaded
      if (
        document.readyState === "complete" ||
        document.readyState === "interactive"
      ) {
        populateSideNavbar();
        if (typeof filterVideos === "function") {
          filterVideos();
        }
        if (typeof checkAndAutoOpenVideo === "function") {
          checkAndAutoOpenVideo();
        }
      } else {
        document.addEventListener("DOMContentLoaded", function () {
          populateSideNavbar();
          if (typeof filterVideos === "function") {
            filterVideos();
          }
          if (typeof checkAndAutoOpenVideo === "function") {
            checkAndAutoOpenVideo();
          }
        });
      }

      console.log("Data loaded:", videosData);
    })
    .catch((error) => {
      console.error("Error loading data:", error);
      videosData = [];

      // Still try to initialize the UI even if data loading failed
      if (
        document.readyState === "complete" ||
        document.readyState === "interactive"
      ) {
        populateSideNavbar(); // Try to populate sidebar even with empty data
        if (typeof filterVideos === "function") {
          filterVideos();
        }
        if (typeof checkAndAutoOpenVideo === "function") {
          checkAndAutoOpenVideo();
        }
      } else {
        document.addEventListener("DOMContentLoaded", function () {
          populateSideNavbar(); // Try to populate sidebar even with empty data
          if (typeof filterVideos === "function") {
            filterVideos();
          }
          if (typeof checkAndAutoOpenVideo === "function") {
            checkAndAutoOpenVideo();
          }
        });
      }
    });
})(); // Self-executing function
