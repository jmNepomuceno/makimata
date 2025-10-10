<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - MIKAMATA</title>
    <link rel="stylesheet" href="contactus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <?php include("../scripts_links/header_links.php") ?>

</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <img src="mik/logo.png" alt="MIKAMATA Logo" style="width: 130px; height: 40px;">
            </div>
            <nav class="nav">
                <a href="home.php">Home</a>
                <a href="products.php">Products</a>
                <a href="aboutus.php">About Us</a>
                <a href="contactus.php" class="active">Contact Us</a>
            </nav>
            <div class="header-actions">
                <input type="search" class="search-input" placeholder="Search..." />
                <button class="login-btn">Login</button>
            </div>
        </div>
    </header>

  <main class="admin-main-content">
    <!-- Tutorial Management -->
    <section class="admin-section">
      <!-- Section Header -->
      <div class="section-header">
        <div class="section-controls">
          <h2 class="section-title">Tutorial Management</h2>
          <button class="btn btn-primary" id="add-tutorial-btn">
            <i class="fas fa-plus"></i> Add New Tutorial
          </button>
        </div>
      </div>

      <!-- Grid View -->
      <div class="tutorials-grid" id="tutorials-view"></div>

      <!-- Bulk Actions -->
      <div class="bulk-actions" id="bulk-actions" style="display: none;">
        <span class="selected-count">0 tutorials selected</span>
        <div class="bulk-buttons">
          <button class="btn btn-secondary" id="bulk-publish">
            <i class="fas fa-globe"></i> Publish
          </button>
          <button class="btn btn-secondary" id="bulk-archive">
            <i class="fas fa-archive"></i> Archive
          </button>
          <button class="btn btn-secondary" id="bulk-export">
            <i class="fas fa-download"></i> Export
          </button>
          <button class="btn btn-danger" id="bulk-delete">
            <i class="fas fa-trash"></i> Delete
          </button>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination-wrapper">
        <div class="pagination-info" id="pagination-info"></div>
        <div class="pagination" id="pagination-controls"></div>
      </div>
    </section>
  </main>

  <div id="tutorial-modal" class="modal">
    <div class="modal-content modal-large">
      <div class="modal-header">
        <h3 id="modal-title">Add New Tutorial</h3>
        <button class="modal-close">&times;</button>
      </div>
      <div class="modal-body">
        <form id="tutorial-form" class="tutorial-form">
          <input type="hidden" id="tutorial-id" name="tutorial_id">
          
          <div class="form-section">
            <h4>Basic Information</h4>
            <div class="form-row">
              <div class="form-group">
                <label for="tutorial-title" class="form-label">Title *</label>
                <input type="text" id="tutorial-title" name="title" class="form-input" required>
              </div>
            </div>

            <div class="form-group">
              <label for="tutorial-description" class="form-label">Description</label>
              <textarea id="tutorial-description" name="description" class="form-textarea" rows="3"></textarea>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="tutorial-type" class="form-label">Type *</label>
                <select id="tutorial-type" name="type" class="form-select" required>
                  <option value="">Select Type</option>
                  <option value="video">Video</option>
                  <option value="article">Article</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h4>Content</h4>

            <!-- This will be shown for 'video' type -->
            <div class="form-group" id="video-url-group" style="display: none;">
              <label for="video-url" class="form-label">Video URL (e.g., YouTube, Vimeo)</label>
              <input type="url" id="video-url" name="video_url" class="form-input" placeholder="https://youtube.com/watch?v=...">
            </div>

            <!-- This will be shown for 'article' type -->
            <div class="form-group" id="article-url-group" style="display: none;">
              <label for="article-url" class="form-label">External Article URL</label>
              <input type="url" id="article-url" name="article_url" class="form-input" placeholder="https://example.com/my-article">
            </div>

            <div class="form-group" id="content-editor-group" style="display: none;">
              <label for="tutorial-content" class="form-label">Content (for Articles)</label>
              <div class="content-editor">
                <div class="editor-toolbar">
                  <button type="button" class="editor-btn" data-command="bold"><i class="fas fa-bold"></i></button>
                  <button type="button" class="editor-btn" data-command="italic"><i class="fas fa-italic"></i></button>
                  <button type="button" class="editor-btn" data-command="underline"><i class="fas fa-underline"></i></button>
                  <button type="button" class="editor-btn" data-command="insertUnorderedList"><i class="fas fa-list-ul"></i></button>
                  <button type="button" class="editor-btn" data-command="insertOrderedList"><i class="fas fa-list-ol"></i></button>
                  <button type="button" class="editor-btn" data-command="createLink"><i class="fas fa-link"></i></button>
                </div>
                <div id="tutorial-content" class="content-area" contenteditable="true"></div>
              </div>
            </div>
          </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancel-btn">Cancel</button>
        <button type="button" class="btn btn-danger" id="delete-tutorial-btn" style="display: none;"><i class="fas fa-trash"></i> Delete Tutorial</button>
        <button type="submit" form="tutorial-form" class="btn btn-primary" id="save-tutorial-btn">Save Tutorial</button>
      </div>
    </div>
  </div>

    <div id="toast-container"></div>

      <script src="contactus.js"></script>
</body>
</html>
