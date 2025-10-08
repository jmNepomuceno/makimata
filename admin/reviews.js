class ReviewManager {
  constructor() {
    this.reviews = []
    this.filteredReviews = []
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
        icon: "fa-star",
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

  _createNotification(title, message, type = "review") {
    try {
      const notifications = JSON.parse(localStorage.getItem("mikamataNotifications") || "[]")
      const newNotification = {
        id: `notif-${Date.now()}`,
        type: type,
        icon: "fa-star",
        title: title,
        message: message,
        recipient: "Admin",
        date: new Date().toISOString(),
        status: "sent",
        link: "reviews.html",
        targetId: null,
      }
      notifications.unshift(newNotification)
      localStorage.setItem("mikamataNotifications", JSON.stringify(notifications.slice(0, 50)))
    } catch (error) {
      console.error("Failed to create notification:", error)
    }
  }

  async init() {
    await this.loadReviews()
    this.setupEventListeners()
  }

  loadReviews() {
    $.ajax({
      url: "../assets/php_admin/fetch_reviews.php",
      type: "GET",
      dataType: "json",
      success: (response) => {
        console.log(response);

        if (response.status === "success") {
          this.reviews = response.reviews;
        } else {
          console.error(response.message);
          this.reviews = [];
        }

        this.filteredReviews = [...this.reviews];
        this.renderReviews();
      },
      error: (xhr, status, error) => {
        console.error("Error fetching reviews:", error);
        this.reviews = [];
        this.filteredReviews = [];
        this.renderReviews();
      }
    });
  }




  saveReviewsToStorage() {
    try {
      localStorage.setItem("mikamataReviews", JSON.stringify(this.reviews))
    } catch (error) {
      console.error("Error saving reviews to localStorage:", error)
    }
  }

  computeAverageRatings(reviews) {
  const grouped = {};

  reviews.forEach(r => {
    const product = r.productName || "Unnamed Product"; // match the PHP alias
    if (!grouped[product]) grouped[product] = [];
    grouped[product].push(Number(r.rating));
  });

  const averages = Object.entries(grouped).map(([product, ratings]) => {
    const avg = ratings.reduce((a, b) => a + b, 0) / ratings.length;
    return { product, average: avg.toFixed(1), count: ratings.length };
  });

  return averages;
}

renderAverageRatings() {
  const averages = this.computeAverageRatings(this.reviews);
  const container = document.getElementById("average-ratings");
  if (!container) return;

  container.innerHTML = averages
    .map(avg => `
      <div class="avg-rating-card">
        <span class="product-name">${avg.product}</span>
        <div class="stars">
          ${Array.from({ length: 5 }, (_, i) =>
            `<i class="fas fa-star ${i < Math.round(avg.average) ? 'filled' : ''}"></i>`
          ).join('')}
        </div>
        <span class="avg-score">${avg.average} (${avg.count} reviews)</span>
      </div>
    `)
    .join('');
}

renderReviews() {
  const gridView = document.getElementById("reviews-view");
  if (!gridView) return;

  const startIndex = (this.currentPage - 1) * this.itemsPerPage;
  const endIndex = startIndex + this.itemsPerPage;
  const paginatedReviews = this.filteredReviews.slice(startIndex, endIndex);

  // ✅ Call average rating rendering
  this.renderAverageRatings();

  if (paginatedReviews.length === 0) {
    gridView.innerHTML = `
      <p style="text-align: center; padding: 2rem; grid-column: 1 / -1;">
        No reviews found.
      </p>`;
    this.renderPagination();
    return;
  }

  // ✅ Group reviews by product name
  const grouped = {};
  paginatedReviews.forEach(review => {
    const key = review.productName || "Unknown Product";
    if (!grouped[key]) grouped[key] = [];
    grouped[key].push(review);
  });

  // ✅ Build HTML by groups
  let reviewsHTML = "";
  Object.entries(grouped).forEach(([productName, reviews]) => {
    reviewsHTML += `
      <div class="product-group">
        <h3 class="product-group-title">${productName}</h3>
        <div class="product-group-reviews">
          ${reviews.map(review => {
            const isLongComment = review.comment.length > 150;
            const truncatedComment = isLongComment
              ? review.comment.substring(0, 150) + "..."
              : review.comment;

            return `
              <div class="review-card">
                <div class="review-card-header">
                  <div class="customer-info">
                    <div>
                      <div class="customer-name">${review.customerName}</div>
                      <div class="review-date">
                        ${new Date(review.created_at).toLocaleDateString("en-US", {
                          month: "short",
                          day: "numeric",
                          year: "numeric",
                        })}
                      </div>
                    </div>
                  </div>
                  <div class="review-status ${review.status}">${review.status}</div>
                </div>
                <div class="review-card-body">
                  <div class="review-rating">
                    <div class="stars">
                      ${Array.from({ length: 5 }, (_, i) =>
                        `<i class="fas fa-star ${i < Math.floor(review.rating) ? "filled" : ""}"></i>`
                      ).join("")}
                    </div>
                    <span class="rating-text">${review.rating}</span>
                  </div>
                  <p class="review-comment">"${truncatedComment}"</p>
                  ${isLongComment
                    ? `<button class="read-more-btn" onclick="reviewManager.viewReview(${review.id})">Read More</button>`
                    : ""}
                </div>
                <div class="review-card-footer">
                  <div class="review-actions">
                    <button class="btn-icon approve" title="Approve" onclick="reviewManager.updateStatus(${review.id}, 'approved')"><i class="fas fa-check"></i></button>
                    <button class="btn-icon reject" title="Reject" onclick="reviewManager.updateStatus(${review.id}, 'rejected')"><i class="fas fa-times"></i></button>
                    <button class="btn-icon reply" title="Reply"><i class="fas fa-reply"></i></button>
                    <button class="btn-icon delete" title="Delete" onclick="reviewManager.deleteReview(${review.id})"><i class="fas fa-trash"></i></button>
                  </div>
                </div>
              </div>
            `;
          }).join("")}
        </div>
      </div>
    `;
  });

  gridView.innerHTML = reviewsHTML;
  this.renderPagination();
}


  closeReviewModal() {
    const modal = document.getElementById("review-modal")
    if (modal) {
      modal.style.display = "none"
    }
  }

  renderPagination() {
    const paginationInfo = document.getElementById("pagination-info")
    const paginationControls = document.getElementById("pagination-controls")
    if (!paginationInfo || !paginationControls) return

    const totalItems = this.filteredReviews.length
    const totalPages = Math.ceil(totalItems / this.itemsPerPage)
    const startItem = totalItems > 0 ? (this.currentPage - 1) * this.itemsPerPage + 1 : 0
    const endItem = Math.min(this.currentPage * this.itemsPerPage, totalItems)

    paginationInfo.textContent = `Showing ${startItem}-${endItem} of ${totalItems} reviews`

    if (totalPages <= 1) {
      paginationControls.innerHTML = ""
      return
    }

    let paginationHTML = `<button class="pagination-btn" ${this.currentPage === 1 ? "disabled" : ""} data-page="${this.currentPage - 1}"><i class="fas fa-chevron-left"></i></button>`
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
          this.renderReviews()
        }
      })
    })
  }

  exportToCSV() {
    const csvRows = []
    const headers = Object.keys(this.reviews[0])

    csvRows.push(headers.join(","))

    this.filteredReviews.forEach((review) => {
      const values = headers.map((header) => {
        let value = review[header]
        if (typeof value === "string") {
          value = value.replace(/"/g, '""')
          value = `"${value}"`
        }
        return value
      })
      csvRows.push(values.join(","))
    })

    const csvData = csvRows.join("\n")
    const blob = new Blob([csvData], { type: "text/csv" })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement("a")
    a.setAttribute("href", url)
    a.setAttribute("download", "reviews.csv")
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    window.URL.revokeObjectURL(url)
  }

  openReviewModal(reviewId) {
    const review = this.reviews.find((r) => r.id === reviewId)
    if (!review) return

    // Populate modal content
    document.getElementById("modal-customer-name").textContent = review.customerName
    document.getElementById("modal-customer-avatar").src = review.customerAvatar
    document.getElementById("modal-product-name").textContent = review.productName
    document.getElementById("modal-product-image").src = review.productImage
    document.getElementById("modal-rating").innerHTML = Array.from(
      { length: 5 },
      (_, i) => `<i class="fas fa-star ${i < Math.floor(review.rating) ? "filled" : ""}"></i>`,
    ).join("")
    document.getElementById("modal-comment").textContent = review.comment
    document.getElementById("modal-date").textContent = new Date(review.date).toLocaleDateString("en-US", {
      month: "short",
      day: "numeric",
      year: "numeric",
    })
    document.getElementById("modal-status").textContent = review.status

    const modal = document.getElementById("review-modal")
    if (modal) {
      modal.style.display = "flex"
    }
  }

  setupEventListeners() {
    const ratingFilter = document.getElementById("rating-filter")

    ratingFilter?.addEventListener("change", () => this.filterAndRender())
    document.getElementById("export-reviews")?.addEventListener("click", () => this.exportToCSV())
  }

  filterAndRender() {
    const ratingFilter = document.getElementById("rating-filter")?.value || ""

    this.filteredReviews = this.reviews.filter((review) => {
      const matchesRating =
        ratingFilter === "" || Math.floor(Number.parseFloat(review.rating)) == Number.parseInt(ratingFilter)

      return matchesRating
    })

    this.currentPage = 1
    this.renderReviews()
  }

  viewReview(reviewId) {
    this.openReviewModal(reviewId)
  }

  async deleteReview(reviewId) {
    const confirmed = await showConfirmation("Are you sure you want to delete this review?");
    if (confirmed) {
        try {
            this.reviews = this.reviews.filter((r) => r.id !== reviewId);
            this.saveReviewsToStorage();
            this.filterAndRender();
            this._createLog("delete", `Deleted review ID: ${reviewId}`, "warning");
            showToast('Review deleted.', 'error');
        } catch (error) {
            console.error('Failed to delete review:', error);
            showToast('Could not delete review.', 'error');
        }
    }
  }

  async updateStatus(reviewId, newStatus) {
    try {
      const response = await $.ajax({
        url: "../assets/php_admin/update_review_status.php",
        type: "POST",
        data: {
          reviewId: reviewId,
          status: newStatus
        },
        dataType: "json"
      });

      if (response.status === "success") {
        // Update local data so UI refreshes immediately
        const review = this.reviews.find((r) => r.id === reviewId);
        if (review) review.status = newStatus;

        this.filterAndRender();
        showToast(`Review status updated to ${newStatus}.`, "success");
      } else {
        showToast(response.message, "error");
      }

    } catch (error) {
      console.error("Failed to update review status:", error);
      showToast("Could not update status.", "error");
    }
  }

}

document.addEventListener("DOMContentLoaded", () => {
  window.reviewManager = new ReviewManager()
})
