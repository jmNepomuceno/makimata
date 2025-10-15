<?php
// Mikamata Tutorial Module - Public Interface (Learn With Us)
session_start();
include("../scripts_links/header_links.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn With Us - MIKAMATA</title>
    <link rel="stylesheet" href="contactus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="products.css"> <!-- Re-using some styles -->
</head>
<body>
    <!-- Header -->
    <header class="header">
      <div class="container">
        <div class="logo">
          <img src="mik/logo.png" alt="MIKAMATA Logo" style="width: 130px; height: 40px;">
        </div>
        <nav class="nav">
          <a href="products.php">Products</a>
          <a href="home.php">Overview</a>
          <a href="aboutus.php">About Us</a>
          <a href="contactus.php" class="active">Learn With Us</a>
        </nav>
        <div class="header-actions">
         

          <button class="icon-btn" id="userBtn" title="User">
              <i class="fa-regular fa-user" id="user-icon"></i>
          </button>
          <i class="fa-solid fa-right-from-bracket" id="logout-btn"></i>
        </div>
      </div>
    </header>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Share Your Craft, Inspire Others</h1>
            <p>Join our community of local artisans and producers. Share your skills through video tutorials and help preserve traditional craftsmanship.</p>
            <button id="scrollToUpload" class="btn-primary">
                <i class="fas fa-upload"></i> Submit Your Tutorial
            </button>
        </div>
    </section>

    <!-- Tutorials Display Section -->
    <section class="tutorials-section">
        <div class="container">
            <div class="section-header">
                <h2>Featured Tutorials</h2>
                <p>Learn from our community of skilled artisans and craftspeople</p>
            </div>

            <div id="tutorialsGrid" class="tutorials-grid">
                <!-- Tutorials will be loaded here by JavaScript -->
            </div>
        </div>
    </section>

    <!-- Upload Section -->
    <section id="uploadSection" class="upload-section">
        <div class="container">
            <div class="section-header">
                <h2>Submit Your Tutorial</h2>
                <p>Share your knowledge with the community. All submissions are reviewed to ensure quality content.</p>
            </div>

            <div id="successMessage" class="success-message">
                <p>Tutorial submitted successfully! It will be reviewed by our team.</p>
            </div>

            <form id="uploadForm" class="upload-form" enctype="multipart/form-data">
                <div class="guidelines-box">
                    <h3><i class="fas fa-info-circle"></i> Submission Guidelines</h3>
                    <ul>
                        <li>Tutorials should focus on local crafts, handicrafts, or livelihood skills</li>
                        <li>Videos should be clear, well-lit, and demonstrate the craft step-by-step</li>
                        <li>Content must be original or you must have permission to share it</li>
                        <li>All submissions will be reviewed by our team before publication</li>
                        <li>Please allow 2-3 business days for review</li>
                    </ul>
                </div>

                <div class="form-group">
                    <label for="tutorialTitle">Tutorial Title *</label>
                    <input 
                        type="text" 
                        id="tutorialTitle" 
                        name="title" 
                        required 
                        placeholder="e.g., Traditional Basket Weaving Techniques"
                    >
                </div>

                <div class="form-group">
                    <label for="tutorialDescription">Description *</label>
                    <textarea 
                        id="tutorialDescription" 
                        name="description" 
                        required 
                        placeholder="Describe what viewers will learn from your tutorial..."
                    ></textarea>
                    <span class="form-help">Provide a clear description of what your tutorial covers</span>
                </div>

                <div id="videoFields" style="display: block;">
                    <div class="form-group">
                        <label for="videoFile">Upload Video File *</label>
                        <input 
                            type="file" 
                            id="video_file" 
                            name="video_file" 
                            accept="video/mp4,video/webm,video/ogg"
                        >
                        <span class="form-help">Upload your tutorial video. Max size: 50MB. Supported formats: MP4, WebM, OGG.</span>
                    </div>
                </div>

                <div id="articleFields" style="display: none;">
                    <div class="form-group">
                        <label for="articleUrl">Article URL (Optional)</label>
                        <input 
                            type="url" 
                            id="articleUrl" 
                            name="article_url" 
                            placeholder="https://..."
                        >
                        <span class="form-help">Link to your full article or blog post</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="document.getElementById('uploadForm').reset()">
                        Clear Form
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit for Review
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Tutorial View Modal -->
    <div id="tutorialModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tutorial</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Tutorial content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About Us</h3>
                    <div class="footer-logo">
                        <h2>MIKAMATA</h2>
                    </div>
                    <p>The Mithiin Kapakanan Makamatan Tagumpas Livelihood and Handicrafts MIKAMATA, is a community-based group established in 2011. The group focuses on making handmade bamboo products such as lampshades, mugs, earrings, phone holders, and furniture.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Learn With Us</h3>
                    <div class="contact-info">
                        <p><i class="fas fa-envelope"></i> Mikamatahandicrafts@gmail.com</p>
                        <p><i class="fas fa-phone"></i> +639217329592 / +639622328554</p>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Index</h3>
                    <ul class="footer-links"> 
                        <li><a href="home.php">Home</a></li>
                        <li><a href="aboutus.php">About Us</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="contactus.php">Learn With Us</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; Copyright 2025 | MIKAMATA</p>
        </div>
    </footer>

    <!-- Profile Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
        <span class="close">&times;</span>
        <span id="title-modal">User Information</span>

        <div class="profile-info">
            <label>First Name:</label>
            <input type="text" id="firstName" readonly>

            <label>Last Name:</label>
            <input type="text" id="lastName" readonly>

            <label>Mobile:</label>
            <input type="text" id="mobile" readonly>

            <label>Email:</label>
            <input type="text" id="email" readonly>

            <hr>
            <h3>Change Password</h3>
            <label>New Password:</label>
            <input type="password" id="newPassword">
            
            <label>Confirm Password:</label>
            <input type="password" id="confirmPassword">

            <button id="updatePasswordBtn">Update Password</button>
        </div>
        </div>
    </div>

    <div id="toast-container"></div>

    <script src="contactus.js"></script>
    <script>
        // Shared script for header functionality
        function showToast(message, type = "info") {
            const container = $("#toast-container");
            const toast = $("<div>").addClass(`toast ${type}`).text(message);
            container.append(toast);
            setTimeout(() => toast.remove(), 4000);
        }

        $('#logout-btn').click(() => {
            $.ajax({
                url: "../assets/php/logout.php", type: "POST", dataType: "json",
                success: (res) => {
                    if (res.status === "success") {
                        sessionStorage.clear(); localStorage.clear();
                        window.location.href = "../index.php"; 
                    } else { showToast("⚠️ Logout failed: " + res.message); }
                },
                error: () => showToast("❌ Something went wrong during logout")
            });
        });

        $('#userBtn').click(() => {
            $.ajax({
                url: "../assets/php/get_user_info.php", type: "POST", dataType: "json",
                success: (res) => {
                    if (res.status === "success") {
                        $("#firstName").val(res.data.firstname); $("#lastName").val(res.data.lastname);
                        $("#mobile").val(res.data.mobile); $("#email").val(res.data.email);
                        $("#profileModal").show();
                    } else { showToast("⚠️ " + res.message); }
                },
                error: () => showToast("❌ Error fetching user data.")
            });
        });

        $("#profileModal .close").click(() => $("#profileModal").hide());
    </script>
</body>
</html>
