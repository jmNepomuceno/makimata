// Mikamata Tutorial Module - Admin Interface JavaScript

class TutorialAdmin {
  constructor() {
    this.tutorials = []
    this.currentFilter = "all"
    this.editingId = null
    this.init()
  }

  init() {
    this.loadTutorials()
    this.setupEventListeners()
    this.updateStats()
  }

  setupEventListeners() {
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

    // Filter buttons
    document.querySelectorAll(".filter-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => this.handleFilterChange(e))
    }
    )
  }

  loadTutorials() {
    $.ajax({
      url: "../assets/php_admin/fetch_tutorials.php",
      method: "GET",
      dataType: "json",
      success: (data) => {
        if (data.status) {
          this.tutorials = data.data;
          this.renderTutorials();
          this.updateStats();
        } else {
          console.error("Failed to load tutorials:", data.message);
        }
      },
      error: (xhr, status, error) => {
        console.error("Error loading tutorials:", error);
      }
    });
  }


  updateStats() {
    const total = this.tutorials.length
    const pending = this.tutorials.filter((t) => t.status === "pending").length
    const approved = this.tutorials.filter((t) => t.status === "approved").length
    const rejected = this.tutorials.filter((t) => t.status === "rejected").length

    document.getElementById("totalCount").textContent = total
    document.getElementById("pendingCount").textContent = pending
    document.getElementById("approvedCount").textContent = approved
    document.getElementById("rejectedCount").textContent = rejected
  }

  handleFilterChange(e) {
    document.querySelectorAll(".filter-btn").forEach((btn) => {
      btn.classList.remove("active")
    })
    e.target.classList.add("active")

    this.currentFilter = e.target.dataset.filter
    this.renderTutorials()
  }

  renderTutorials() {
    const tbody = document.getElementById("tutorialsTableBody")

    if (!tbody) return

    let filteredTutorials = this.tutorials
    if (this.currentFilter !== "all") {
      filteredTutorials = this.tutorials.filter((t) => t.status === this.currentFilter)
    }

    if (filteredTutorials.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="7" style="text-align: center; padding: 40px;">
            <div class="empty-state">
              <i class="fas fa-inbox"></i>
              <h3>No Tutorials Found</h3>
              <p>No tutorials match the current filter.</p>
            </div>
          </td>
        </tr>
      `
      return
    }

    console.log(filteredTutorials)

    tbody.innerHTML = filteredTutorials
      .map(
        (tutorial) => {
          const shortDescription = tutorial.description.length > 50 ? tutorial.description.substring(0, 50) + '...' : tutorial.description;
          return `
      <tr>
        <td>${this.escapeHtml(tutorial.author || "N/A")}</td>
        <td><strong>${this.escapeHtml(tutorial.title)}</strong></td>
        <td title="${this.escapeHtml(tutorial.description)}">${this.escapeHtml(shortDescription)}</td>
        <td><span class="status-badge ${tutorial.status}">${tutorial.status}</span></td>
        <td><a href="/${tutorial.video_url}" target="_blank" class="video-link">View Video</a></td>
        <td>${this.formatDate(tutorial.created_at)}</td>
        <td>
          <div class="action-buttons">
            ${
              tutorial.status === "pending"
                ? `
              <button class="btn-success" onclick="tutorialAdmin.approveReject(${tutorial.id}, 'approved')">
                <i class="fas fa-check"></i> Approve
              </button>
              <button class="btn-danger" onclick="tutorialAdmin.approveReject(${tutorial.id}, 'rejected')">
                <i class="fas fa-times"></i> Reject
              </button>
            `
                : ""
            }
            <button class="btn-secondary" onclick="tutorialAdmin.viewTutorial(${tutorial.id})">
              <i class="fas fa-eye"></i> View
            </button>
          </div>
        </td>
      </tr>
    `
        })
      .join("")
  }

  async approveReject(tutorialId, newStatus) {
    const action = newStatus === "approved" ? "approve" : "reject";

    if (!confirm(`Are you sure you want to ${action} this tutorial?`)) {
      return;
    }

    const formData = new FormData();
    formData.append("tutorialId", tutorialId);
    formData.append("status", newStatus);

    $.ajax({
      url: "../assets/php_admin/update_tutorial_status.php",
      type: "POST",
      data: formData,
      processData: false, // required for FormData
      contentType: false, // required for FormData
      dataType: "json",
      success: (data) => {
        if (data.status) {
          alert(`Tutorial ${action}d successfully!`);
          this.loadTutorials(); // reload tutorials list
        } else {
          alert("Error: " + data.message);
        }
      },
      error: (xhr, status, error) => {
        console.error("Error updating status:", error);
        alert("An error occurred. Please try again.");
      }
    });

  }

  viewTutorial(id) {
    const tutorial = this.tutorials.find((t) => t.id === id)
    if (!tutorial) return

    this.editingId = id
    document.getElementById("modalTitle").textContent = "View Tutorial"

    // Populate and disable form fields
    const titleInput = document.getElementById("tutorialTitle")
    const descriptionInput = document.getElementById("tutorialDescription")
    const statusSelect = document.getElementById("tutorialStatus")
    const videoFileInput = document.getElementById("videoFile")
    const currentVideoDiv = document.getElementById("currentVideo")
    const uploaderInfoDiv = document.querySelector(".user-info-display")
    const uploaderNameSpan = document.getElementById("uploaderName")
    const uploaderEmailSpan = document.getElementById("uploaderEmail")

    titleInput.value = tutorial.title
    titleInput.readOnly = true
    descriptionInput.value = tutorial.description
    descriptionInput.readOnly = true
    statusSelect.value = tutorial.status
    statusSelect.disabled = true

    // Hide file input and show current video if available
    videoFileInput.style.display = "none"
    if (tutorial.video_url) {
      currentVideoDiv.innerHTML = `<a href="${tutorial.video_url}" target="_blank">View Uploaded Video</a>`
      currentVideoDiv.style.display = "block"
    } else {
      currentVideoDiv.innerHTML = "No video uploaded."
      currentVideoDiv.style.display = "block"
    }

    // Show uploader info
    uploaderNameSpan.textContent = tutorial.author || "N/A"
    uploaderEmailSpan.textContent = tutorial.user_email || "N/A"
    uploaderInfoDiv.style.display = "block"

    document.getElementById("tutorialModal").classList.add("active")
  }

  closeModal() {
    document.getElementById("tutorialModal").classList.remove("active")
    this.editingId = null
  }

  getDefaultThumbnail(type) {
    return type === "video" ? "/video-tutorial-concept.png" : "/article-guide.jpg"
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
let tutorialAdmin
document.addEventListener("DOMContentLoaded", () => {
  tutorialAdmin = new TutorialAdmin()
})
