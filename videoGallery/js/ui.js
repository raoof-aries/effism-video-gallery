// UI rendering and DOM manipulation.
// Functions that create and update UI elements like video cards, modals, and navigation.

// Function to render videos
let youtubePlayer;
function renderVideos(videos) {
  const galleryContainer = document.getElementById("video-gallery");
  galleryContainer.innerHTML = "";

  if (videos.length === 0) {
    galleryContainer.innerHTML = `
          <div class="no-videos">
            <h3>No videos found</h3>
            <p>Try adjusting your filters or search term</p>
          </div>
        `;
    return;
  }

  videos.forEach((video) => {
    const thumbnail = getThumbnail(video.video_link, video.thumbnail);

    const videoCard = document.createElement("div");
    videoCard.className = "video-card";
    videoCard.innerHTML = `
          <div class="video-thumbnail" data-video-info='${JSON.stringify(
      video
    )}'>
            <img src="${thumbnail}" alt="${video.video_name}">
            <div class="play-icon">▶</div>
          </div>
          <div class="video-info">
            <div class="video-title">${video.topic}</div>
            <div class="video-meta">
              <div class="meta-item">
                <span class="meta-icon">⏱️</span>
                <span>${formatDuration(video.topic_duration)}</span>
              </div>
              <div class="meta-item">
                <span class="meta-icon">👁️</span>
                <span>${video.watch_count} views</span>
              </div>
              <div class="meta-item">
                <span class="meta-icon">📅</span>
                <span>${video.fr_training_date || "—"}</span>
              </div>
            </div>
            <div class="video-description">${video.video_description}</div>
            <div class="video-tags">
             ${video.main_category_name
        ? `<span class="video-tag">${video.main_category_name}</span>`
        : ""
      }
              ${video.sub_category_name
        ? `<span class="video-tag">${video.sub_category_name}</span>`
        : ""
      }
            </div>
          </div>
        `;
    galleryContainer.appendChild(videoCard);
  });

  // Add click event to video thumbnails
  document.querySelectorAll(".video-thumbnail").forEach((thumbnail) => {
    thumbnail.addEventListener("click", function () {
      const videoInfo = JSON.parse(this.getAttribute("data-video-info"));
      openVideoModal(videoInfo);
    });
  });
}

// Function to open video modal

function openVideoModal(videoInfo) {
  const modal = document.getElementById("video-modal");
  const modalInfo = document.getElementById("modal-video-info");
  const videoContainer = document.querySelector(".video-iframe-container");
  const videoType = getVideoType(videoInfo.video_link);

  // Clear previous content
  videoContainer.innerHTML = "";

  modalInfo.innerHTML = `
    <h2 class="modal-video-title">${videoInfo.topic}</h2>
    <div class="modal-meta">
      <div class="modal-meta-item"><span class="meta-icon">⏱️</span> ${formatDuration(
    videoInfo.topic_duration
  )} hrs</div>
      <div class="modal-meta-item"><span class="meta-icon">👁️</span> ${videoInfo.watch_count
    } views</div>
      <div class="modal-meta-item"><span class="meta-icon">📅</span> ${videoInfo.fr_training_date || "—"
    }</div>
    </div>
    <div class="modal-description">${videoInfo.video_description}</div>
    <div class="modal-tags">
      ${videoInfo.main_category_name
      ? `<span class="modal-tag">${videoInfo.main_category_name}</span>`
      : ""
    }
      ${videoInfo.sub_category_name
      ? `<span class="modal-tag">${videoInfo.sub_category_name}</span>`
      : ""
    }
    </div>
  `;

  if (videoType === "youtube") {
    // YouTube video
    const videoId = getYouTubeId(videoInfo.video_link);
    const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
    videoContainer.innerHTML = `<iframe src="${embedUrl}" frameborder="0" allowfullscreen></iframe>`;
  } else if (videoType === "vimeo") {
    // Vimeo video (embed)
    const videoId = getVimeoId(videoInfo.video_link);
    const embedUrl = `https://player.vimeo.com/video/${videoId}?autoplay=1`;
    videoContainer.innerHTML = `<iframe src="${embedUrl}" frameborder="0" allowfullscreen></iframe>`;
  } else if (videoType === "direct" || videoType === "other") {
    // Direct video file (including Vimeo direct files)
    videoContainer.innerHTML = `
      <video controls autoplay style="width: 100%; height: 100%;">
        <source src="${videoInfo.video_link}" type="${getVideoFileType(
      videoInfo.video_link
    )}">
        Your browser does not support the video tag.
      </video>
    `;
  }

  modal.style.display = "flex";
  document.body.style.overflow = "hidden";

  if (videoType === "direct" || videoType === "other") {
    const videoElement = videoContainer.querySelector("video");
    if (videoElement) {
      videoElement.addEventListener("ended", function () {
        console.log("Video has ended.");
        sendVideoComplete(videoInfo.id);
      });
    }
  }

  if (videoType === "youtube") {
    console.log("you tube video has ended.");
    sendVideoComplete(videoInfo.id);
  }
}

// Function to close video modal
function closeVideoModal() {
  const modal = document.getElementById("video-modal");
  const videoContainer = document.querySelector(".video-iframe-container");

  modal.style.display = "none";
  videoContainer.innerHTML = "";
  document.body.style.overflow = "auto";
}

// Function to populate side navbar with categories
function populateSideNavbar() {
  const { mainCategories, subCategories } = extractCategories();
  const categoryList = document.getElementById("category-list");
  categoryList.innerHTML = "";

  // Create "All" category item
  const allCategoryItem = document.createElement("li");
  allCategoryItem.className = "category-item";
  allCategoryItem.innerHTML = `
      <div class="category-header active" data-category="all">
        <span>All Categories</span>
      </div>
    `;
  categoryList.appendChild(allCategoryItem);

  // Add click event to "All" category
  allCategoryItem
    .querySelector(".category-header")
    .addEventListener("click", function () {
      selectedMainCategory = "all";
      selectedSubCategory = "all";
      
      // Set flag to false and always go to "all" view when clicking All Categories
      isInitialLoad = false;
      switchView("all");
      
      document.querySelectorAll(".category-header").forEach((header) => {
        header.classList.remove("active");
      });
      document
        .querySelectorAll(".subcategory-item, .all-option")
        .forEach((item) => {
          item.classList.remove("active");
        });
      this.classList.add("active");
    });

  // Sort main categories by main_cat_sort_order
  const sortedMainCategories = mainCategories.sort((a, b) => {
    const sortOrderA = a[1].mainCatSortOrder || Infinity;
    const sortOrderB = b[1].mainCatSortOrder || Infinity;
    return sortOrderA - sortOrderB;
  });

  // Add each main category
  sortedMainCategories.forEach(([categoryId, categoryData]) => {
    const categoryName = categoryData.name;
    const hasSubcategories = subCategories.has(categoryId);
    const subcategoryCount = hasSubcategories ? subCategories.get(categoryId).size : 0;

    const categoryItem = document.createElement("li");
    categoryItem.className = "category-item";
    categoryItem.innerHTML = `
        <div class="category-header${hasSubcategories ? " has-subcategories" : ""
      }" data-category="${categoryId}">
          <span>${categoryName}</span>
          ${hasSubcategories ? '<span class="toggle-icon">▼</span>' : ""}
        </div>
      `;

    // Add subcategories if available
    if (hasSubcategories) {
      const subCategoryEntries = Array.from(
        subCategories.get(categoryId).entries()
      );
      
      // Sort subcategories by sub_cat_sort_order
      const sortedSubCategories = subCategoryEntries.sort((a, b) => {
        const sortOrderA = a[1].subCatSortOrder || Infinity;
        const sortOrderB = b[1].subCatSortOrder || Infinity;
        return sortOrderA - sortOrderB;
      });
      
      const subcategoryList = document.createElement("ul");
      subcategoryList.className = "subcategory-list";

      // Add each subcategory first
      sortedSubCategories.forEach(([subCategoryId, subCategoryData]) => {
        const subcategoryItem = document.createElement("li");
        subcategoryItem.className = "subcategory-item";
        subcategoryItem.textContent = subCategoryData.name;
        subcategoryItem.setAttribute("data-main-category", categoryId);
        subcategoryItem.setAttribute("data-sub-category", subCategoryId);

        subcategoryItem.addEventListener("click", function () {
          selectCategory(categoryId, subCategoryId);

          // Remove active class from all category headers and subcategory items
          document.querySelectorAll(".category-header").forEach((header) => {
            header.classList.remove("active");
          });
          document
            .querySelectorAll(".subcategory-item, .all-option")
            .forEach((item) => {
              item.classList.remove("active");
            });

          // Add active class to parent category header and this subcategory
          this.closest(".category-item")
            .querySelector(".category-header")
            .classList.add("active");
          this.classList.add("active");
        });

        subcategoryList.appendChild(subcategoryItem);
      });

      // Add "All" option only if there's more than one subcategory
      if (subcategoryCount > 1) {
        const allOption = document.createElement("li");
        allOption.className = "all-option";
        allOption.textContent = "All " + categoryName;
        allOption.setAttribute("data-main-category", categoryId);
        allOption.setAttribute("data-sub-category", "all");

        allOption.addEventListener("click", function () {
          selectCategory(categoryId, "all");

          // Remove active class from all category headers and subcategory items
          document.querySelectorAll(".category-header").forEach((header) => {
            header.classList.remove("active");
          });
          document
            .querySelectorAll(".subcategory-item, .all-option")
            .forEach((item) => {
              item.classList.remove("active");
            });

          // Add active class to parent category header and this option
          this.closest(".category-item")
            .querySelector(".category-header")
            .classList.add("active");
          this.classList.add("active");
        });

        // Add "All" option at the end
        subcategoryList.appendChild(allOption);
      }

      categoryItem.appendChild(subcategoryList);
    }

    categoryList.appendChild(categoryItem);

    // Add click event to category header
    const categoryHeader = categoryItem.querySelector(".category-header");
    categoryHeader.addEventListener("click", function () {
      if (hasSubcategories) {
        // Toggle subcategory list
        const subcategoryList = this.nextElementSibling;
        const toggleIcon = this.querySelector(".toggle-icon");

        // Toggle the open state of the list
        subcategoryList.classList.toggle("open");

        // Toggle active state on the header
        this.classList.toggle("active");

        // Explicitly rotate the icon based on open state
        if (subcategoryList.classList.contains("open")) {
          toggleIcon.style.transform = "rotate(180deg)";
        } else {
          toggleIcon.style.transform = "rotate(0deg)";
        }
      } else {
        // Select this category (no subcategories)
        selectCategory(categoryId, "all");
        document.querySelectorAll(".category-header").forEach((header) => {
          header.classList.remove("active");
        });
        document
          .querySelectorAll(".subcategory-item, .all-option")
          .forEach((item) => {
            item.classList.remove("active");
          });
        this.classList.add("active");
      }
    });
  });
}

function sendVideoComplete(videoId) {
  console.log("Sending videoId:", videoId);
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
    .then((data) => console.log("Video complete sent:", data))
    .catch((err) => console.error("Failed to send complete event:", err));
}