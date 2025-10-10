

class TutorialManager {
  constructor() {
    this.tutorials = []
    this.filteredTutorials = []
    this.currentPage = 1
    this.itemsPerPage = 10
    this.init()
  }

  _createLog(action, description, severity = "info") {
    try {
      const logs = JSON.parse(localStorage.getItem("mikamataActivityLogs") || "[]")
      const newLog = {
        id: `log-${Date.now()}`,
        user: { name: "Admin User", avatar: "../placeholder.svg?height=32&width=32&text=A" },
        action: action,
        description: description,
        severity: severity,
        icon: "fa-book-open",
        timestamp: new Date().toISOString(),
        ip: "127.0.0.1",
        details: {},
      }
      logs.unshift(newLog)
      localStorage.setItem("mikamataActivityLogs", JSON.stringify(logs.slice(0, 100)))
    } catch (error) {
      console.error("Failed to create activity log:", error)
    }
  }

  async init() {
    await this.loadTutorials()
    this.setupEventListeners()
  }

  // async loadTutorials() {
  //   console.log('here')
  //   try {
  //     // --- DATABASE INTEGRATION PLACEHOLDER ---
  //     // const response = await fetch('/api/tutorials');
  //     // this.tutorials = await response.json();

  //     // For demo, use mock data
  //     const savedTutorials = localStorage.getItem("mikamataTutorials");
  //     this.tutorials = savedTutorials ? JSON.parse(savedTutorials) : generateMockTutorials();
  //     this.saveTutorialsToStorage();
  //   } catch (error) {
  //     console.error("Error loading tutorials from localStorage:", error)
  //     this.tutorials = generateMockTutorials()
  //   }
  //   this.filteredTutorials = [...this.tutorials]
  //   this.renderTutorials()
  // }

  // saveTutorialsToStorage() {
  //   // --- DATABASE INTEGRATION PLACEHOLDER ---
  //   // This will be removed. Saving is done via API calls.
  //   localStorage.setItem("mikamataTutorials", JSON.stringify(this.tutorials));
  // }

  getTutorialIcon(type) {
    return type === "video" ? "fas fa-play-circle" : "fas fa-file-alt";
  }

  loadTutorials() {
    console.log('here')
    $.ajax({
      url: "../assets/php_admin/fetch_tutorials.php", // your PHP backend
      type: "GET",
      dataType: "json",
      success: (res) => {
        console.log(res)
        if (res.status === "success") {
          this.tutorials = res.data;           // store raw tutorials
          this.filteredTutorials = this.tutorials; // default filter
          this.renderTutorials();              // render the fetched data
        } else {
          $("#tutorials-view").html(
            `<p style="text-align:center; padding:2rem;">${res.message}</p>`
          );
        }
      },
      error: (xhr, status, err) => {
        console.error("AJAX Error:", err);
        $("#tutorials-view").html(
          `<p style="text-align:center; padding:2rem; color:red;">Failed to load tutorials.</p>`
        );
      }
    });
  }

  updateTutorialStatus(tutorialId, newStatus) {
    // Example: AJAX call to backend (PHP)
    $.ajax({
      url: "../assets/php_admin/update_tutorial_status.php",
      type: "POST",
      data: { tutorialId: tutorialId, status: newStatus },
      success: (response) => {
        console.log(response)
        // You can adjust this part based on your backend response
        alert(`Tutorial has been ${newStatus}.`);
        
        // Update in the local data
        const tutorial = this.tutorials.find(t => t.id === tutorialId);
        if (tutorial) tutorial.status = newStatus;

        this.renderTutorials();
      },
      error: () => {
        alert("Error updating tutorial status.");
      }
    });
  }


renderTutorials() {
  const gridView = document.getElementById("tutorials-view");
  if (!gridView) return;

  const startIndex = (this.currentPage - 1) * this.itemsPerPage;
  const endIndex = startIndex + this.itemsPerPage;
  const paginatedTutorials = this.filteredTutorials.slice(startIndex, endIndex);
  console.log(paginatedTutorials)

  if (paginatedTutorials.length === 0) {
    gridView.innerHTML = `<p style="text-align: center; padding: 2rem; grid-column: 1 / -1;">No tutorials found.</p>`;
    this.renderPagination();
    return;
  }

  gridView.innerHTML = paginatedTutorials
    .map((tutorial) => {
      const link = tutorial.type === 'video' ? tutorial.video_url : tutorial.article_url;
      const isClickable = !!link;
      const cardTag = isClickable ? 'a' : 'div';
      const cardHref = isClickable ? `href="${link}" target="_blank"` : '';

      // Status badge (Pending, Approved, Rejected)
      const statusClass = tutorial.status === 'approved'
        ? 'badge-success'
        : tutorial.status === 'rejected'
        ? 'badge-danger'
        : 'badge-warning';

      const statusText = tutorial.status
        ? tutorial.status.charAt(0).toUpperCase() + tutorial.status.slice(1)
        : 'Pending';

      return `
        <${cardTag} class="tutorial-card ${!isClickable ? 'not-clickable' : ''}" ${cardHref}>
          <div class="tutorial-card-thumbnail">
            <div class="tutorial-icon">
              <i class="${this.getTutorialIcon(tutorial.type)}"></i>
            </div>
            <span class="type-badge ${tutorial.type}">
              <i class="fas ${tutorial.type === "video" ? "fa-play" : "fa-file-alt"}"></i> ${tutorial.type}
            </span>
          </div>
          <div class="tutorial-card-content">
            <h4 class="tutorial-title"><b>Title</b>: ${tutorial.title}</h4>
            <p class="tutorial-description"><i>Description: </i>${tutorial.description}</p>
            <p class="tutorial-status">
              <span class="badge ${statusClass}">${statusText}</span>
            </p>
          </div>
          <div class="tutorial-card-footer">
            <span class="last-updated">Updated: ${new Date(tutorial.last_updated).toLocaleDateString("en-US", { month: "short", day: "numeric" })}</span>
            <div class="action-buttons">
              ${
                tutorial.status === 'pending'
                  ? `
                    <button class="btn btn-sm btn-success approve-btn" data-id="${tutorial.id}">
                      <i class="fas fa-check"></i> Approve
                    </button>
                    <button class="btn btn-sm btn-danger reject-btn" data-id="${tutorial.id}">
                      <i class="fas fa-times"></i> Reject
                    </button>
                  `
                  : `
                    <button class="btn-icon edit-btn" title="Edit" onclick="event.preventDefault(); event.stopPropagation(); tutorialManager.openTutorialModal(${tutorial.id})">
                      <i class="fas fa-edit"></i>
                    </button>
                  `
              }
            </div>
          </div>
        </${cardTag}>
      `;
    })
    .join("");

  this.renderPagination();

  // Attach approval/rejection handlers using jQuery
  $(".approve-btn").off("click").on("click", (e) => {
    e.preventDefault();
    const tutorialId = $(e.currentTarget).data("id");
    this.updateTutorialStatus(tutorialId, "approved");
  });

  $(".reject-btn").off("click").on("click", (e) => {
    e.preventDefault();
    const tutorialId = $(e.currentTarget).data("id");
    this.updateTutorialStatus(tutorialId, "rejected");
  });
}



  renderPagination() {
    const paginationInfo = document.getElementById("pagination-info")
    const paginationControls = document.getElementById("pagination-controls")
    if (!paginationInfo || !paginationControls) return

    const totalItems = this.filteredTutorials.length
    const totalPages = Math.ceil(totalItems / this.itemsPerPage)
    const startItem = totalItems > 0 ? (this.currentPage - 1) * this.itemsPerPage + 1 : 0
    const endItem = Math.min(this.currentPage * this.itemsPerPage, totalItems)

    paginationInfo.textContent = `Showing ${startItem}-${endItem} of ${totalItems} tutorials`

    if (totalPages <= 1) {
      paginationControls.innerHTML = ""
      return
    }

    let paginationHTML = ""
    paginationHTML += `<button class="pagination-btn" ${this.currentPage === 1 ? "disabled" : ""} data-page="${this.currentPage - 1}"><i class="fas fa-chevron-left"></i></button>`

    // Simplified pagination for now
    for (let i = 1; i <= totalPages; i++) {
      paginationHTML += `<button class="pagination-btn ${i === this.currentPage ? "active" : ""}" data-page="${i}">${i}</button>`
    }

    paginationHTML += `<button class="pagination-btn" ${this.currentPage === totalPages ? "disabled" : ""} data-page="${this.currentPage + 1}"><i class="fas fa-chevron-right"></i></button>`
    paginationControls.innerHTML = paginationHTML

    paginationControls.querySelectorAll(".pagination-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const page = e.currentTarget.dataset.page
        if (page) {
          this.currentPage = Number.parseInt(page)
          this.renderTutorials()
        }
      })
    })
  }

  filterAndRender() {
    this.filteredTutorials = this.tutorials.filter((tutorial) => {
      // Filtering logic can be added back here if a search bar is re-introduced.
      // For now, it just shows all tutorials.
      return true
    })

    this.currentPage = 1
    this.renderTutorials()
  }

  setupEventListeners() {
    // Modal
    const addTutorialBtn = document.getElementById("add-tutorial-btn")
    const tutorialModal = document.getElementById("tutorial-modal")
    const modalClose = document.querySelector("#tutorial-modal .modal-close")
    const tutorialForm = document.getElementById("tutorial-form")
    const cancelBtn = document.getElementById("cancel-btn")
    const deleteBtn = document.getElementById("delete-tutorial-btn");

    addTutorialBtn?.addEventListener("click", () => {
      this.openTutorialModal() // Open with no ID for adding
    })

    modalClose?.addEventListener("click", () => {
      this.closeTutorialModal()
    })

    cancelBtn?.addEventListener("click", () => {
      this.closeTutorialModal()
    })

    tutorialForm?.addEventListener("submit", (e) => {
      e.preventDefault()
      this.saveTutorial()
    })

    deleteBtn?.addEventListener("click", () => {
        const tutorialId = Number.parseInt(document.getElementById("tutorial-id").value);
        if (tutorialId) {
            this.deleteTutorial(tutorialId);
        }
    })

    // Show/hide fields based on tutorial type
    document.getElementById("tutorial-type")?.addEventListener("change", (e) => {
      this.toggleContentTypeFields(e.target.value)
    })

    // Add more event listeners for other form buttons if needed

    // Bulk Actions
    const selectAllCheckbox = document.getElementById("select-all")
    const tableBody = document.getElementById("tutorials-table-body")

    selectAllCheckbox?.addEventListener("change", (e) => {
      tableBody.querySelectorAll(".tutorial-checkbox").forEach((checkbox) => {
        checkbox.checked = e.target.checked
      })
      this.updateBulkActionsVisibility()
    })

    tableBody?.addEventListener("change", (e) => {
      if (e.target.classList.contains("tutorial-checkbox")) {
        this.updateBulkActionsVisibility()
      }
    })
  }

  openTutorialModal(tutorialId = null) {
    console.log('here')
    const modal = document.getElementById("tutorial-modal")
    const modalTitle = document.getElementById("modal-title")
    const form = document.getElementById("tutorial-form")
    const deleteBtn = document.getElementById("delete-tutorial-btn");
    const saveBtn = document.getElementById("save-tutorial-btn");

    form.reset() // Clear previous data
    document.getElementById("tutorial-id").value = "" // Clear hidden ID
    // Reset any other specific fields like contenteditable div
    document.getElementById("tutorial-content").innerHTML = ""
    this.toggleContentTypeFields(form.querySelector("#tutorial-type").value) // Set initial state based on (empty) dropdown

    if (tutorialId) {
      // This is an edit operation
      const tutorial = this.tutorials.find((t) => t.id === tutorialId)
      if (tutorial) {
        deleteBtn.style.display = "inline-block"; // Show delete button
        saveBtn.textContent = "Update Tutorial";
        modalTitle.textContent = "Edit Tutorial"
        // Populate the form
        document.getElementById("tutorial-id").value = tutorial.id
        document.getElementById("tutorial-title").value = tutorial.title
        document.getElementById("tutorial-description").value = tutorial.description
        document.getElementById("tutorial-type").value = tutorial.type
        document.getElementById("video-url").value = tutorial.videoUrl || ""
        document.getElementById("article-url").value = tutorial.articleUrl || ""
        // Populate other fields as they are added to the mock data
      }
    } else {
      // This is an add operation
      deleteBtn.style.display = "none"; // Hide delete button
      saveBtn.textContent = "Save Tutorial";
      modalTitle.textContent = "Add New Tutorial"
    }

    // This call ensures that if we are editing, the correct fields are shown.
    this.toggleContentTypeFields(form.querySelector("#tutorial-type").value)
    modal.style.display = "flex"
  }

  closeTutorialModal() {
    const modal = document.getElementById("tutorial-modal")
    modal.style.display = "none"
  }

  async deleteTutorial(tutorialId) {
    const confirmed = await showConfirmation("Are you sure you want to delete this tutorial?", "Delete");
    if (confirmed) {
      $.ajax({
        url: "../assets/php_admin/delete_tutorial.php",
        type: "POST",
        data: { id: tutorialId },
        dataType: "json",
        success: (res) => {
          if (res.status === "success") {
            showToast('Tutorial deleted successfully.', 'error');
            this.closeTutorialModal();
            this.loadTutorials(); // refresh DB data
          } else {
            showToast(res.message || 'Could not delete tutorial.', 'error');
          }
        },
        error: (xhr, status, err) => {
          console.error("AJAX error:", err);
          showToast('Could not delete tutorial.', 'error');
        }
      });
    }
  }


  async saveTutorial() {
    console.log('save')
    const form = document.getElementById("tutorial-form")
    const tutorialId = Number.parseInt(form.querySelector("#tutorial-id").value)
    const isNew = !tutorialId;

    const tutorialData = {
      id: tutorialId,
      title: form.querySelector("#tutorial-title").value,
      description: form.querySelector("#tutorial-description").value,
      type: form.querySelector("#tutorial-type").value,
      videoUrl: form.querySelector("#video-url").value,
      articleUrl: form.querySelector("#article-url").value,
      lastUpdated: new Date().toISOString().split("T")[0], // today
    }

    try {
      // --- thumbnail generation (same logic you had) ---
      if (tutorialData.type === 'video' && tutorialData.videoUrl) {
        const youtubeId = this.getYouTubeVideoId(tutorialData.videoUrl);
        tutorialData.image = youtubeId
          ? `https://img.youtube.com/vi/${youtubeId}/mqdefault.jpg`
          : this.getPlaceholderImage(tutorialData.videoUrl);
      } else if (tutorialData.type === 'article' && tutorialData.articleUrl) {
        try {
          const response = await fetch(`https://api.microlink.io?url=${encodeURIComponent(tutorialData.articleUrl)}`);
          const linkData = await response.json();
          tutorialData.image = linkData.data?.image?.url || this.getPlaceholderImage(tutorialData.articleUrl);
        } catch {
          tutorialData.image = this.getPlaceholderImage(tutorialData.articleUrl);
        }
      } else {
        tutorialData.image = this.getPlaceholderImage();
      }
    } catch (e) {
      console.error("Error fetching thumbnail:", e);
      tutorialData.image = this.getPlaceholderImage();
    }

    // --- AJAX call to PHP ---
    $.ajax({
      url: isNew ? "../assets/php_admin/add_tutorial.php" : "../assets/php_admin/update_tutorial.php",
      type: "POST",
      data: tutorialData,
      dataType: "json",
      success: (res) => {
        console.log(res)
        if (res.status === "success") {
          showToast(`Tutorial ${isNew ? 'added' : 'updated'} successfully!`, 'success');
          this.closeTutorialModal();
          this.loadTutorials(); // <-- refresh from DB
        } else {
          showToast(res.message || 'Failed to save tutorial.', 'error');
        }
      },
      error: (xhr, status, err) => {
        console.error("AJAX error:", err);
        showToast('Could not save tutorial.', 'error');
      }
    });
  }

  
  /**
   * Generates a consistent or random placeholder image.
   * @param {string|null} url - An optional URL to generate a consistent placeholder.
   * @returns {string} The URL of the placeholder image.
   */
  getPlaceholderImage(url = null) {
    // Simple hash function to get a consistent random number from a string
    const seed = url ? url.split('').reduce((acc, char) => char.charCodeAt(0) + ((acc << 5) - acc), 0) : Date.now();
    const randomIndex = Math.abs(seed) % 5; // We have 5 placeholder images
    return `../mik/tutorials/placeholder-${randomIndex}.jpg`;
  }
  /**
   * <NEW>
   * Parses a YouTube URL to extract the video ID.
   * @param {string} url The YouTube URL.
   * @returns {string|null} The video ID or null if not found.
   */
  getYouTubeVideoId(url) {
    let videoId = null;
    const youtubeRegex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
    const match = url.match(youtubeRegex);

    if (match && match[1]) {
        videoId = match[1];
    }

    return videoId;
  }

  toggleContentTypeFields(type) {
    const videoGroup = document.getElementById("video-url-group")
    const articleGroup = document.getElementById("article-url-group")
    const editorGroup = document.getElementById("content-editor-group")

    // Hide all content-specific groups first
    videoGroup.style.display = "none"
    articleGroup.style.display = "none"
    editorGroup.style.display = "none"

    // Show the relevant group(s) based on the selected type
    if (type === "video") {
      videoGroup.style.display = "block"
    } else if (type === "article") {
      articleGroup.style.display = "block"
      // Always show the manual editor for articles, can be used with or without an external URL
      editorGroup.style.display = "block"
    }
  }

  updateBulkActionsVisibility() {
    const checkedBoxes = document.querySelectorAll(".tutorial-checkbox:checked")
    const bulkActions = document.getElementById("bulk-actions")
    const selectedCount = document.querySelector(".selected-count")

    if (bulkActions && selectedCount) {
      if (checkedBoxes.length > 0) {
        bulkActions.style.display = "flex"
        selectedCount.textContent = `${checkedBoxes.length} tutorials selected`
      } else {
        bulkActions.style.display = "none"
      }
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  // Initialize current date
  const currentDateEl = document.getElementById("current-date")
  if (currentDateEl) {
    currentDateEl.textContent = new Date().toLocaleDateString("en-US", {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    })
  }

  window.tutorialManager = new TutorialManager()
})
