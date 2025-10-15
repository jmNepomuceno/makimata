// Mikamata Tutorial Module - Public Interface JavaScript

class TutorialPublic {
  constructor() {
    this.tutorials = []
    this.currentFilter = "all"
    this.init()
  }

  init() {
    this.loadTutorials()
    this.setupEventListeners()
  }

  setupEventListeners() {
    // Upload form submission
    const uploadForm = document.getElementById("uploadForm")
    if (uploadForm) {
      uploadForm.addEventListener("submit", (e) => this.handleUploadSubmit(e))
    }

    // Modal close
    const modalClose = document.querySelector(".modal-close")
    if (modalClose) {
      modalClose.addEventListener("click", () => this.closeModal())
    }

    // Close modal on outside click
    const modal = document.getElementById("tutorialModal")
    if (modal) {
      modal.addEventListener("click", (e) => {
        if (e.target === modal) {
          this.closeModal()
        }
      })
    }

    // Tutorial type change
    const typeSelect = document.getElementById("tutorialType")
    if (typeSelect) {
      typeSelect.addEventListener("change", (e) => this.handleTypeChange(e))
    }

    // Scroll to upload form
    const uploadBtn = document.getElementById("scrollToUpload")
    if (uploadBtn) {
      uploadBtn.addEventListener("click", () => {
        document.getElementById("uploadSection").scrollIntoView({
          behavior: "smooth",
        })
      })
    }
  }

  loadTutorials() {
    $.ajax({
      url: "../assets/php_admin/fetch_tutorials.php",
      method: "GET",
      dataType: "json",
      success: (response) => {
        console.log(response)
        if (response.status) {
          this.tutorials = response.data;
          this.renderTutorials();
        } else {
          console.error("Failed to load tutorials:", response.message);
        }
      },
      error: (xhr, status, error) => {
        console.error("Error loading tutorials:", error);
      },
    });
  }


  renderTutorials() {
    const grid = document.getElementById("tutorialsGrid");
    if (!grid) return;

    if (this.tutorials.length === 0) {
      grid.innerHTML = `
        <div class="empty-state">
          <i class="fas fa-video"></i>
          <h3>No Tutorials Yet</h3>
          <p>Be the first to share your knowledge with the community!</p>
        </div>
      `;
      return;
    }

    console.log(this.tutorials);

    grid.innerHTML = this.tutorials
      .map((tutorial) => {
        // Determine thumbnail or icon fallback
        const iconHTML = `<i class="${tutorial.icon || "fas fa-play"} tutorial-icon"></i>`;
        const link =
          tutorial.type === "video"
            ? tutorial.video_url
            : tutorial.article_url;

        return `
          <div class="tutorial-card" onclick="tutorialPublic.openTutorial(${tutorial.id})">
            <div class="tutorial-thumbnail">
              ${iconHTML}
            </div>
            <div class="tutorial-content">
              <span class="tutorial-type">
                <i class="${tutorial.icon || "fas fa-play"}"></i> ${tutorial.type}
              </span>
              <h3 class="tutorial-title">${this.escapeHtml(tutorial.title)}</h3>
              <p class="tutorial-description">${this.escapeHtml(tutorial.description)}</p>
              <div class="tutorial-meta">
                <span><i class="fas fa-eye"></i> ${tutorial.views} views</span>
                <span><i class="fas fa-clock"></i> ${this.formatDate(tutorial.last_updated)}</span>
                <span class="status-badge status-${tutorial.status}">
                  ${tutorial.status}
                </span>
              </div>
            </div>
          </div>
        `;
      })
      .join("");
  }


  openTutorial(id) {
    const tutorial = this.tutorials.find((t) => t.id === id)
    if (!tutorial) return

    const modal = document.getElementById("tutorialModal")
    const modalBody = document.querySelector(".modal-body")

    let content = ""
    console.log(tutorial.video_url)

    if (tutorial.type === "video" && tutorial.video_url) {
      // Check if it's a YouTube URL or a direct file path
      if (tutorial.video_url.includes("youtube.com") || tutorial.video_url.includes("youtu.be")) {
        const videoId = this.extractVideoId(tutorial.video_url)
        content = `
          <div class="video-container">
            <iframe 
              src="https://www.youtube.com/embed/${videoId}" 
              frameborder="0" 
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
              allowfullscreen>
            </iframe>
          </div>
        `
      } else {
        // Assume it's a direct file path from the server
        content = `
          <div class="video-container">
            <video controls style="width: 100%; height: auto; border-radius: var(--radius);">
              <source src="/${tutorial.video_url}" type="video/mp4">
              Your browser does not support the video tag.
            </video>
          </div>
        `
      }
      // Add title and description after the video content
      content += `
          <h2>${this.escapeHtml(tutorial.title)}</h2>
          <p><strong>By:</strong> ${this.escapeHtml(tutorial.author || "Anonymous")}</p>
          <p>${this.escapeHtml(tutorial.description)}</p>
        `
    } else {
      content = `
        <h2>${this.escapeHtml(tutorial.title)}</h2>
        <p><strong>By:</strong> ${this.escapeHtml(tutorial.author || "Anonymous")}</p>
        <p>${this.escapeHtml(tutorial.description)}</p>
        ${tutorial.article_url ? `<p><a href="${tutorial.article_url}" target="_blank" class="btn-primary">Read Full Article</a></p>` : ""}
        ${tutorial.content ? `<div class="article-content">${tutorial.content}</div>` : ""}
      `
    }

    modalBody.innerHTML = content
    modal.classList.add("active")
  }

  closeModal() {
    const modal = document.getElementById("tutorialModal")
    modal.classList.remove("active")
  }

  handleUploadSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData();
    formData.append("title", $("#tutorialTitle").val());
    formData.append("description", $("#tutorialDescription").val());
    formData.append("article_url", $("#articleUrl").val());
    formData.append("status", "pending");

    const fileInput = $("#video_file")[0];
    if (fileInput.files.length > 0) {
      formData.append("video_file", fileInput.files[0]);
    } else {
      alert("Please select a video file before submitting.");
      return;
    }

    // ✅ Debug: log all entries
    for (let [key, value] of formData.entries()) {
      console.log(key, value);
    }

    const videoFile = $("#video_file")[0].files[0];
    console.log("Video file selected:", videoFile);

   $.ajax({
      url: "../assets/php_admin/add_tutorial.php",
      type: "POST",
      data: formData,
      dataType: "json", // ✅ tell jQuery to auto-parse JSON
      contentType: false,
      processData: false,
      success: function (data) {
        if (data.success) {
          alert("Tutorial submitted successfully! It will be reviewed by our team.");
          form.reset();

          const successMessage = document.getElementById("successMessage");
          if (successMessage) {
            successMessage.scrollIntoView({ behavior: "smooth" });
          }
        } else {
          alert("Error: " + data.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        console.log("Response text:", xhr.responseText);
        alert("An error occurred while submitting your tutorial. Please try again.");
      },
    });

  }


  showSuccessMessage(message) {
    const successDiv = document.getElementById("successMessage")
    successDiv.querySelector("p").textContent = message
    successDiv.classList.add("show")

    setTimeout(() => {
      successDiv.classList.remove("show")
    }, 5000)
  }

  handleTypeChange(e) {
    const type = e.target.value
    const videoFields = document.getElementById("videoFields")
    const articleFields = document.getElementById("articleFields")

    if (type === "video") {
      videoFields.style.display = "block"
      articleFields.style.display = "none"
    } else {
      videoFields.style.display = "none"
      articleFields.style.display = "block"
    }
  }

  extractVideoId(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
    const match = url.match(regExp)
    return match && match[2].length === 11 ? match[2] : null
  }

  getDefaultThumbnail(type) {
    return type === "video" ? "/video-tutorial-crafts.jpg" : "/article-handicraft-guide.jpg"
  }

  formatDate(dateString) {
    const date = new Date(dateString)
    return date.toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    })
  }

  escapeHtml(text) {
    const div = document.createElement("div")
    div.textContent = text
    return div.innerHTML
  }
}

// Initialize when DOM is ready
let tutorialPublic
document.addEventListener("DOMContentLoaded", () => {
  tutorialPublic = new TutorialPublic()
})
