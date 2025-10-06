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

      <script src="contactus.js"></script>

</body>
</html>
