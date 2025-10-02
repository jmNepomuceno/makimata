<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us - Mikamata</title>
    <link rel="stylesheet" href="aboutus.css" />
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
          <a href="aboutus.php" class="active">About Us</a>
          <a href="contactus.php">Contact Us</a>
        </nav>
        <div class="header-actions">
          <input type="search" class="search-input" placeholder="Search..." />

          <button class="icon-btn" id="userBtn" title="User">
              <i class="fa-regular fa-user" id="user-icon"></i>
          </button>


          <i class="fa-solid fa-right-from-bracket" id="logout-btn"></i>
        </div>
      </div>
    </header>

    <!-- Hero -->
    <section class="hero">
      <div class="container hero-content">
        <p class="hero-title"></p>
        <p class="hero-title">Mikamata</p>
        <h2 class="hero-main">
          “<span>MI</span>thiin <span>KA</span>pakanan <span>MA</span>kamtan <span>TA</span>gumpay Livelihood and Handicraft”
        </h2>
        <p class="hero-subtitle">
          Discover our story, our mission, and the values. Local crafts, for the growth of the community.
        </p>
      </div>
    </section>

    <!-- Services -->
    <section class="services container">
      <div class="service-item">
        <div class="service-icon">✓</div>
        <h3>Quality Craftsmanship</h3>
        <p>Expertly crafted bamboo products built to last.</p>
      </div>
      <div class="service-item">
        <div class="service-icon">✓</div>
        <h3>Eco-Friendly</h3>
        <p>Sustainably sourced materials for a greener planet.</p>
      </div>
      <div class="service-item">
        <div class="service-icon">✓</div>
        <h3>Customer Focused</h3>
        <p>Dedicated to providing the best experience for you.</p>
      </div>
      <div class="service-item">
        <div class="service-icon">✓</div>
        <h3>Innovative Design</h3>
        <p>Blending traditional techniques with modern aesthetics.</p>
      </div>
      <div class="service-item">
        <div class="service-icon">✓</div>
        <h3>Community Support</h3>
        <p>Empowering local artisans and communities.</p>
      </div>
    </section>

    <!-- About -->
    <main class="about-main">
      <div class="container about-content">
        <div class="about-text">
          <h2>About Us</h2>
          <p>
            The Mithiin Kapakanan Makamatan Tagumpas Livelihod and Handicrafts MIKAMATA,
             formerly known as AFPAI, is a community-based group established in 2011 with around 
             60 members. The group focuses on making handmade bamboo products such as lampshades, 
             mugs, kitchenware, phone baskets, and furniture. 
          </p>
          <p>
           This website was developed to help organize product listings, 
           provide basic customization options, and offer basic knowledge about bamboo that support
            local producers in their craft and daily operations.
          </p>
        </div>
        <div class="about-gallery">
          <img src="mik/ab3.png" alt="Bamboo products showcase" class="gallery-img-1" />
          <img src="mik/ab2.png" alt="Crafting process" class="gallery-img-2" />
          <img src="mik/ab1.png" alt="Finished bamboo holder" class="gallery-img-3" />
        </div>
      </div>
    </main>

    <!-- Mission & Vision -->
    <section class="mission-vision">
      <h2 class="section-title">MISSION & VISION</h2>
      <div class="container mission-vision-content">
      <div class="mission">
      <h3>Our Mission</h3>
      <p>
      To empower local bamboo producers by providing a user-friendly, innovative, 
      and accessible online platform that enhances their reach, improves their livelihood, 
      and supports sustainable handcrafted product development through customization, 
      cost estimation, and livelihood tutorials.
      </p>
      </div>
      <div class="vision">
      <h3>Our Vision</h3>
      <p>
      To be a dedicated digital platform that supports local bamboo producers 
      by promoting their crafted products, improving their livelihood through technology, 
      and preserving traditional skills for future generations.
      </p>
      </div>
      </div>
    </section>

    <!-- Footer -->
  <footer class="footer">
    <div class="container foot">
      <div class="copy">
        <p>&copy; Copyright 2025 | MIKAMATA</p>
      </div>
    </div>
  </footer>

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

  <script>
    function showToast(message, type = "info") {
        const container = $("#toast-container");
        const toast = $("<div>").addClass(`toast ${type}`).text(message);
        container.append(toast);
        setTimeout(() => toast.remove(), 4000);
    }


    $('#logout-btn').click(() => {
        $.ajax({
            url: "../assets/php/logout.php",
            type: "POST",
            dataType: "json",
            success: function(res) {
            console.log(res)
                if (res.status === "success") {
                    // Optionally clear local storage/session storage
                    sessionStorage.clear();
                    localStorage.clear();

                    // Redirect to landing page
                    window.location.href = "../index.php"; 
                } else {
                    showToast("⚠️ Logout failed: " + res.message);
                }
            },
            error: function() {
                showToast("❌ Something went wrong during logout");
            }
        });
    });

    // Open modal on user button click
    $('#userBtn').click(() => {
        $("#profileModal").show();
        $.ajax({
            url: "../assets/php/get_user_info.php",
            type: "POST",
            dataType: "json",
            success: function(res) {
                console.log(res)
                if (res.status === "success") {
                    $("#firstName").val(res.data.firstname);
                    $("#lastName").val(res.data.lastname);
                    $("#mobile").val(res.data.mobile);
                    $("#email").val(res.data.email);
                    $("#profileModal").show();
                } else {
                    showToast("⚠️ " + res.message);
                }
            },
            error: function() {
                showToast("❌ Something went wrong during logout");
            }
        });
    });

    $('#updatePasswordBtn').click(() => {
        const newPassword = $("#newPassword").val().trim();
        const confirmPassword = $("#confirmPassword").val().trim();

        $.ajax({
            url: "../assets/php/update_password.php",
            type: "POST",
            data: { newPassword, confirmPassword },
            dataType: "json",
            success: function(res) {
                if (res.status === "success") {
                    showToast("✅ " + res.message);
                    $("#newPassword, #confirmPassword").val(""); // clear fields
                    $("#profileModal").hide();
                } else {
                    showToast("⚠️ " + res.message);
                }
            }
        });
    });


    // Close modal
    $(".close").click(() => {
        $("#profileModal").hide();
    });

  </script>
  </body>
</html>
