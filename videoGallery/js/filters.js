// Logic for filtering and sorting videos.
// Handles category selection, search filtering, and view switching logic.

// Global variables to track filter state
let currentView = "featured"; // Default view on page load
let selectedMainCategory = "all";
let selectedSubCategory = "all";
let isInitialLoad = true; // Track if this is the initial page load

// Function to extract unique categories from video data
function extractCategories() {
  const mainCategoriesMap = new Map();
  const subCategoriesMap = new Map();

  videosData.forEach((video) => {
    if (video.main_category_name) {
      if (!mainCategoriesMap.has(video.main_category)) {
        mainCategoriesMap.set(video.main_category, {
          name: video.main_category_name,
          mainCatSortOrder: video.main_cat_sort_order || null
        });
      }

      if (video.sub_category_name) {
        if (!subCategoriesMap.has(video.main_category)) {
          subCategoriesMap.set(video.main_category, new Map());
        }

        const subCategories = subCategoriesMap.get(video.main_category);
        if (!subCategories.has(video.sub_category)) {
          subCategories.set(video.sub_category, {
            name: video.sub_category_name,
            subCatSortOrder: video.sub_cat_sort_order || null
          });
        }
      }
    }
  });

  return {
    mainCategories: Array.from(mainCategoriesMap.entries()),
    subCategories: subCategoriesMap,
  };
}

// Function to select category
function selectCategory(mainCategory, subCategory) {
  selectedMainCategory = mainCategory;
  selectedSubCategory = subCategory;

  // Set the flag to false immediately when any category is selected
  isInitialLoad = false;

  // Always show "all" when selecting any category (except initial page load which is handled separately)
  switchView("all");
}

// Function to filter videos
function filterVideos() {
  const searchTerm = document
    .getElementById("search-input")
    .value.toLowerCase();

  const filteredVideos = videosData.filter((video) => {
    // Filter by search term
    const matchesSearch =
      video.video_name.toLowerCase().includes(searchTerm) ||
      video.video_description.toLowerCase().includes(searchTerm) ||
      (video.topic && video.topic.toLowerCase().includes(searchTerm)) ||
      (video.trainer_name &&
        video.trainer_name.toLowerCase().includes(searchTerm));

    // Filter by main category
    const matchesMainCategory =
      selectedMainCategory === "all" ||
      video.main_category === selectedMainCategory;

    // Filter by sub category
    const matchesSubCategory =
      selectedSubCategory === "all" ||
      video.sub_category === selectedSubCategory;

    return matchesSearch && matchesMainCategory && matchesSubCategory;
  });

  // Sort videos based on current view
  const sortedVideos = sortVideos(filteredVideos);
  renderVideos(sortedVideos);
}

// Function to switch between view tabs
function switchView(viewType) {
  currentView = viewType;

  // Update active tab
  document.querySelectorAll(".view-tab").forEach((tab) => {
    if (tab.getAttribute("data-view") === viewType) {
      tab.classList.add("active");
    } else {
      tab.classList.remove("active");
    }
  });

  // Re-filter and render videos
  filterVideos();
}

// Function to sort videos based on the current view
function sortVideos(videos) {
  const sortedVideos = [...videos];

  if (currentView === "featured") {
    // Filter out videos where featured is not "1"
    const featuredVideos = sortedVideos.filter(
      (video) => video.featured === "1"
    );

    // Sort featured videos by sort_order, lower numbers come first
    featuredVideos.sort((a, b) => {
      // Convert sort_order to a number, handle potential non-numeric values
      const orderA = parseInt(a.sort_order) || Infinity;
      const orderB = parseInt(b.sort_order) || Infinity;
      return orderA - orderB;
    });

    return featuredVideos;
  } else if (currentView === "recent") {
    // Sort by added_date in descending order (most recent first)
    sortedVideos.sort((a, b) => {
      const dateA = new Date(a.added_date.split("-").reverse().join("-"));
      const dateB = new Date(b.added_date.split("-").reverse().join("-"));
      return dateB - dateA;
    });
    return sortedVideos;
  } else if (currentView === "most-viewed") {
    // Sort by watch_count in descending order (most viewed first)
    sortedVideos.sort(
      (a, b) => parseInt(b.watch_count) - parseInt(a.watch_count)
    );
    return sortedVideos;
  }
  else if (currentView === "all") {
    sortedVideos.sort((a, b) => {
      const dateA = new Date(a.added_date.split("-").reverse().join("-"));
      const dateB = new Date(b.added_date.split("-").reverse().join("-"));
      return dateB - dateA;
    });
  }

  return sortedVideos;
}