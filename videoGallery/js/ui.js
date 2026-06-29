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
            <div class="video-title" style="cursor: pointer;">${video.topic}</div>
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
            <button class="copy-link-btn" data-video-id="${video.id}">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
              <span>Copy Link</span>
            </button>
          </div>
        `;
    galleryContainer.appendChild(videoCard);
  });

  // Add click event to video thumbnails and titles
  document.querySelectorAll(".video-thumbnail, .video-title").forEach((el) => {
    el.addEventListener("click", function () {
      let videoInfo;
      if (this.classList.contains("video-thumbnail")) {
        videoInfo = JSON.parse(this.getAttribute("data-video-info"));
      } else {
        // For title, search info from the sibling thumbnail
        const thumbnailEl = this.closest(".video-card").querySelector(".video-thumbnail");
        videoInfo = JSON.parse(thumbnailEl.getAttribute("data-video-info"));
      }
      showInlinePlayer(videoInfo);
    });
  });

  // Add click event to copy link buttons
  document.querySelectorAll(".copy-link-btn").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      const videoId = this.getAttribute("data-video-id");
      const videoUrl = window.location.origin + window.location.pathname.replace('index.php', '') + 'watch.php?v=' + videoId;
      
      navigator.clipboard.writeText(videoUrl).then(() => {
        const span = this.querySelector("span");
        const originalText = span.textContent;
        this.classList.add("copied");
        span.textContent = "Copied!";
        
        setTimeout(() => {
          this.classList.remove("copied");
          span.textContent = originalText;
        }, 2000);
      }).catch(err => {
        console.error("Failed to copy link:", err);
      });
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
      <div class="category-header${selectedMainCategory === "all" ? " active" : ""}" data-category="all">
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
    
    const isMainActive = selectedMainCategory === categoryId;

    const categoryItem = document.createElement("li");
    categoryItem.className = "category-item";
    categoryItem.innerHTML = `
        <div class="category-header${hasSubcategories ? " has-subcategories" : ""}${isMainActive ? " active" : ""}" data-category="${categoryId}">
          <span>${categoryName}</span>
          ${hasSubcategories ? `<span class="toggle-icon" style="transform: ${isMainActive ? 'rotate(180deg)' : 'rotate(0deg)'}">▼</span>` : ""}
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
      subcategoryList.className = `subcategory-list${isMainActive ? " open" : ""}`;

      // Add each subcategory first
      sortedSubCategories.forEach(([subCategoryId, subCategoryData]) => {
        const isSubActive = selectedMainCategory === categoryId && selectedSubCategory === subCategoryId;
        const subcategoryItem = document.createElement("li");
        subcategoryItem.className = `subcategory-item${isSubActive ? " active" : ""}`;
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
        const isAllActive = selectedMainCategory === categoryId && selectedSubCategory === "all";
        const allOption = document.createElement("li");
        allOption.className = `all-option${isAllActive ? " active" : ""}`;
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

// Inline Video Player functions
function showInlinePlayer(videoInfo, isPopState = false) {
  const inlineContainer = document.getElementById("inline-player-container");
  if (!inlineContainer) return;

  const filterContainer = document.getElementById("filter-container");
  const galleryContainer = document.getElementById("video-gallery");

  if (filterContainer) filterContainer.style.display = "none";
  if (galleryContainer) galleryContainer.style.display = "none";

  const playerWrapper = inlineContainer.querySelector(".watch-video-player-wrapper");
  const infoContainer = document.getElementById("inline-video-info");
  const videoType = getVideoType(videoInfo.video_link);

  let playerHtml = "";
  if (videoType === "youtube") {
    const videoId = getYouTubeId(videoInfo.video_link);
    const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
    playerHtml = `<iframe src="${embedUrl}" frameborder="0" allowfullscreen style="position: absolute; top:0; left:0; width:100%; height:100%; border:none;"></iframe>`;
  } else if (videoType === "vimeo") {
    const videoId = getVimeoId(videoInfo.video_link);
    const embedUrl = `https://player.vimeo.com/video/${videoId}?autoplay=1`;
    playerHtml = `<iframe src="${embedUrl}" frameborder="0" allowfullscreen style="position: absolute; top:0; left:0; width:100%; height:100%; border:none;"></iframe>`;
  } else if (videoType === "direct" || videoType === "other") {
    playerHtml = `
      <video controls autoplay style="position: absolute; top:0; left:0; width:100%; height:100%;">
        <source src="${videoInfo.video_link}" type="${getVideoFileType(videoInfo.video_link)}">
        Your browser does not support the video tag.
      </video>
    `;
  }

  playerWrapper.innerHTML = playerHtml;

  infoContainer.innerHTML = `
    <h2 class="modal-video-title" style="margin-top: 0;">${videoInfo.topic}</h2>
    <div class="modal-meta">
      <div class="modal-meta-item"><span class="meta-icon">⏱️</span> ${formatDuration(videoInfo.topic_duration)} hrs</div>
      <div class="modal-meta-item"><span class="meta-icon">👁️</span> ${videoInfo.watch_count} views</div>
      <div class="modal-meta-item"><span class="meta-icon">📅</span> ${videoInfo.fr_training_date || "—"}</div>
    </div>
    <div class="modal-description" style="margin-top: 1rem; color: var(--text-light); line-height: 1.6; font-size: 0.95rem;">${videoInfo.video_description}</div>
    <div class="modal-tags" style="margin-top: 1.5rem;">
      ${videoInfo.main_category_name ? `<span class="modal-tag">${videoInfo.main_category_name}</span>` : ""}
      ${videoInfo.sub_category_name ? `<span class="modal-tag">${videoInfo.sub_category_name}</span>` : ""}
    </div>
  `;

  inlineContainer.style.display = "block";

  // Set the video ID on the inline copy link button and bind listener
  const inlineCopyBtn = document.getElementById("inline-copy-link-btn");
  if (inlineCopyBtn) {
    inlineCopyBtn.setAttribute("data-video-id", videoInfo.id);
    
    if (!inlineCopyBtn.dataset.listenerAttached) {
      inlineCopyBtn.addEventListener("click", function() {
        const videoId = this.getAttribute("data-video-id");
        if (!videoId) return;
        const videoUrl = window.location.origin + window.location.pathname.replace('index.php', '') + 'watch.php?v=' + videoId;
        
        navigator.clipboard.writeText(videoUrl).then(() => {
          const span = this.querySelector("span");
          const originalText = span.textContent;
          this.classList.add("copied");
          span.textContent = "Copied!";
          
          setTimeout(() => {
            this.classList.remove("copied");
            span.textContent = originalText;
          }, 2000);
        }).catch(err => {
          console.error("Failed to copy link:", err);
        });
      });
      inlineCopyBtn.dataset.listenerAttached = "true";
    }
  }

  // Bind back button listener once
  const backBtn = document.getElementById("back-to-gallery-btn");
  if (backBtn && !backBtn.dataset.listenerAttached) {
    backBtn.addEventListener("click", function() {
      hideInlinePlayer();
    });
    backBtn.dataset.listenerAttached = "true";
  }

  if (!isPopState) {
    const newUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname}?v=${videoInfo.id}`;
    window.history.pushState({ videoId: videoInfo.id }, '', newUrl);
  }

  // Update views counter in database
  if (videoType === "direct" || videoType === "other") {
    const videoElement = playerWrapper.querySelector("video");
    if (videoElement) {
      videoElement.addEventListener("ended", function () {
        sendVideoComplete(videoInfo.id);
      });
    }
  } else {
    sendVideoComplete(videoInfo.id);
  }
}

function hideInlinePlayer(isPopState = false) {
  const inlineContainer = document.getElementById("inline-player-container");
  if (!inlineContainer || inlineContainer.style.display === "none") return;

  const playerWrapper = inlineContainer.querySelector(".watch-video-player-wrapper");
  if (playerWrapper) playerWrapper.innerHTML = "";

  inlineContainer.style.display = "none";
  const filterContainer = document.getElementById("filter-container");
  const galleryContainer = document.getElementById("video-gallery");

  if (filterContainer) filterContainer.style.display = "flex";
  if (galleryContainer) galleryContainer.style.display = "grid";

  if (!isPopState) {
    const cleanUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname}`;
    window.history.pushState({}, '', cleanUrl);
  }
}

function checkAndAutoOpenVideo() {
  const urlParams = new URLSearchParams(window.location.search);
  const videoId = urlParams.get("v");
  if (videoId && window.videosData && window.videosData.length > 0) {
    const video = window.videosData.find(v => String(v.id) === String(videoId));
    if (video) {
      showInlinePlayer(video, true);
    }
  }
}