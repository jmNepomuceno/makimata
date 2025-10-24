<?php 
  session_start();
  // echo "<pre>"; print_r($_SESSION); echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MIKAMATA — Home</title>
  <link rel="stylesheet" href="home.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php include("../scripts_links/header_links.php") ?>
</head>
<body>

  <!-- ====== HEADER ====== -->
  <header class="header">
    <div class="container">
      <div class="brand">
        <!-- Updated logo source to match React component -->
        <img src="mik/logo.png" alt="MIKAMATA Logo">
        
      </div>
      <nav class="nav">
          <a href="products.php">Products</a>
          <a href="home.php" class="active">Overview</a>
          <a href="aboutus.php" >About Us</a>
          <a href="contactus.php">Learn With Us</a>
      </nav>
      <div class="header-actions">
        <button class="icon-btn" id="userBtn" title="User">
          <i class="fa-regular fa-user" id="user-icon"></i>
        </button>


        <i class="fa-solid fa-right-from-bracket" id="logout-btn"></i>
      </div>
    </div>
  </header>
  <section class="hero">
    <div class="container hero-grid">
      <div class="hero-copy">
        <p class="eyebrow">"Bamboo Crafts,</p>
        <h1><span>MIKAMATA</span><br>Your Way."</h1>
        <p class="lead">
          Welcome to Mikamata! Here, we celebrate the art of bamboo craftsmanship by offering you a one-stop hub where you can design custom bamboo creations, learn about bamboo, and shop from a vibrant marketplace all while supporting local artisans and discovering how simple it is to bring your bamboo vision to life.
        </p>
         <a href="aboutus.html" class="btn cta">Learn More <i class="fa-solid fa-arrow-right"></i></a>

      </div>
      <div class="hero-visual">
        <img class="bamboo" src="mik/m1.png" alt="Bamboo plant">
        <div class="showcase">
          <img src="mik/b22.png" alt="Bamboo products">
          <img src="mik/p4.png" alt="Bamboo products">
          <img src="mik/c6.png" alt="Bamboo products">        
        </div>
        </div>
      </div>
    </div>
    <div class="hero-highlights">
      <div class="highlight">
        <img src="mik/a1.png" alt="Teacup">
        <p>Bamboo Handcrafts Hand Made Items Directly from Locals</p>
      </div>
      <div class="highlight center">
        <p>You can buy the best bamboo crafts online now!</p>
      </div>
      <div class="highlight">
        <img src="mik/a2.png" alt="Mini rack">
        <p>Crafts that blend tradition with modern design</p>
      </div>
    </div>
    
    <div class="scroll-cue">
      <i class="fa-solid fa-arrow-down"></i>
    </div>
  </section>

  <!-- ====== FEATURED PRODUCTS ====== -->
  <section class="featured">
    <div class="container">
      <h2>Our Products</h2>
      <div class="product-filters">
        <button class="filter-btn active">WHAT'S NEW</button>
        <button class="filter-btn">BEST SELLERS</button>
        <button class="filter-btn">CUSTOMER FAVORITES</button>
      </div>
      
      <div class="grid">
        <!-- Updated featured product to match React component with proper pricing -->
        <article class="card" data-product-id="1">
          <div class="card-media">
            <img src="mik/b6.png" alt="Sulu-Light Lampshade">
          </div>
          <div class="card-meta">
            <p class="title">Sulu-Light Lampshade</p>
            <p class="price">₱150.00</p>
          </div>
        </article>

        <!-- Updated all product cards to match React component exactly -->
        <article class="card" data-product-id="2">
          <div class="card-media">
            <img src="mik/p2.png" alt="Ilaw - Kawayan">
          </div>
          <div class="card-meta">
            <p class="title">Ilaw - Kawayan</p>
            <p class="price">₱100.00</p>
          </div>
        </article>

        <article class="card" data-product-id="3">
          <div class="card-media">
            <img src="mik/p1.png" alt="HabitHook Rack">
          </div>
          <div class="card-meta">
            <p class="title">HabitHook Rack</p>
            <p class="price">₱100.00</p>
          </div>
        </article>

        <article class="card" data-product-id="4">
          <div class="card-media">
            <img src="mik/p3.png" alt="Tadyaw">
          </div>
          <div class="card-meta">
            <p class="title">Tadyaw</p>
            <p class="price">₱100.00</p>
          </div>
        </article>

        <article class="card" data-product-id="5">
          <div class="card-media">
            <img src="mik/p4.png" alt="Pugad Holder">
          </div>
          <div class="card-meta">
            <p class="title">Pugad Holder</p>
            <p class="price">₱100.00</p>
          </div>
        </article>

        <article class="card" data-product-id="6">
          <div class="card-media">
            <img src="mik/p5.png" alt="Kape - Kawayan">
          </div>
          <div class="card-meta">
            <p class="title">Kape - Kawayan</p>
            <p class="price">₱100.00</p>
          </div>
        </article>

        <article class="card" data-product-id="7">
          <div class="card-media">
            <img src="mik/p6.png" alt="Dayang - Dulo">
          </div>
          <div class="card-meta">
            <p class="title">Dayang - Dulo</p>
            <p class="price">₱100.00</p>
          </div>
        </article>

        <article class="card" data-product-id="8">
          <div class="card-media">
            <img src="mik/p9.png" alt="Dayang - Dulo">
          </div>
          <div class="card-meta">
            <p class="title">Dayang - Dulo</p>
            <p class="price">₱100.00</p>
          </div>
        </article>
      </div>
      <div style="text-align: center; margin-top: 2rem;">
        <a href="products.php" class="btn cta">View All Products <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </section>

  <!-- ====== PROCESS ====== -->
  <section class="process">
    <div class="container steps">
      <!-- Updated process steps descriptions to match React component exactly -->
      <div class="step">
        <i class="fa-solid fa-eye"></i>
        <h4>Browse</h4>
        <p>Explore available bamboo products.</p>
      </div>
      <div class="step">
        <i class="fa-solid fa-pen-ruler"></i>
        <h4>Customize</h4>
        <p>Choose design, color, or size.</p>
      </div>
      <div class="step">
        <i class="fa-solid fa-cart-shopping"></i>
        <h4>Order</h4>
        <p>Place your chosen item.</p>
      </div>
      <div class="step">
        <i class="fa-solid fa-truck-fast"></i>
        <h4>Track</h4>
        <p>Get updates on your order.</p>
      </div>
      <div class="step">
        <i class="fa-solid fa-star"></i>
        <h4>Review</h4>
        <p>Leave feedback after delivery.</p>
      </div>
    </div>
  </section>

  <!-- ====== PRODUCT CATEGORIES ====== -->
  <section class="categories">
    <div class="container">
      <h2>Product Categories</h2>
      <div class="category-row">
        <div class="category" onclick="redirectToProducts('lampshades')">
          <div class="circle">
            <img src="mik/c1.png" alt="Lampshades">
          </div>
          <p>Lampshades</p>
        </div>
        <div class="category" onclick="redirectToProducts('mugs')">
          <div class="circle">
            <img src="mik/c2.png" alt="Mugs">
          </div>
          <p>Mugs</p>
        </div>
        <div class="category" onclick="redirectToProducts('basket')">
          <div class="circle">
            <img src="mik/b1.png" alt="Baskets">
          </div>
          <p>Baskets</p>
        </div>
        <div class="category" onclick="redirectToProducts('kitchenware')">
          <div class="circle">
            <img src="mik/kw1.png" alt="Kitchenware">
          </div>
          <p>Kitchenware</p>
        </div>
        <div class="category" onclick="redirectToProducts('furnitures')">
          <div class="circle">
            <img src="mik/c6.png" alt="Furniture">
          </div>
          <p>Furniture</p>
        </div>
      </div>
    </div>
  </section>  <script>
    function redirectToProducts(category) {
      window.location.href = `products.html?category=${category}`;
    }
  </script>


  <!-- ====== ABOUT / CONTACT / INDEX ====== -->
  <section class="info">
    <div class="container info-grid">
      <div class="about">
        <h3>About Us</h3>
        <div class="about-brand">

          <span>MIKAMATA</span>
        </div>
        <p>
          The Mithiin Kapakanan Makamatan Tagumpas Livelihood and Handicrafts MIKAMATA is a community-based group established in 2011. The group focuses on making handmade bamboo products such as lampshades, mugs, earrings, phone holders, and furniture.
        </p>
      </div>
      <div class="contacts">
        <!-- Updated section title to match React component -->
        <h3>Learn With Us</h3>
        <p><i class="fa-solid fa-envelope"></i> mikamatahandicrafts@gmail.com</p>
        <p><i class="fa-solid fa-phone"></i> +639217239592 / +639622328554</p>
      </div>
      <div class="index">
        <h3>Index</h3>
        <ul>
          <li><a href="home.php">Home</a></li>
          <li><a href="aboutus.php">About Us</a></li>
          <li><a href="products.php">Products</a></li>
          <li><a href="contactus.php">Learn With Us</a></li>
        </ul>
      </div>
    </div>
  </section>

  <!-- ====== FOOTER ====== -->
  <footer class="footer">
    <div class="container foot">
      <div class="copy">© Copyright 2025 | MIKAMATA</div>
      <!-- Updated footer logo source to match React component -->

    </div>
  </footer>

  <!-- Cart Modal -->
  <div class="modal" id="cartModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Shopping Cart</h2>
        <button class="close-btn" id="closeCart">&times;</button>
      </div>
      <div class="modal-body" id="cartItems">
        <!-- Cart items will be loaded here -->
        <div class="empty-state">
          <i class="fa fa-shopping-cart"></i>
          <p>Your cart is empty</p>
          <button class="primary-btn">Continue Shopping</button>
        </div>
      </div>
      <div class="modal-footer" id="cartFooter"></div>
    </div>
  </div>

  <!-- Wishlist Modal -->
  <div class="modal" id="wishlistModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Wishlist</h2>
        <button class="close-btn" id="closeWishlist">&times;</button>
      </div>
      <div class="modal-body" id="wishlistItems">
        <div class="empty-state">
          <i class="fa fa-heart"></i>
          <p>Your wishlist is empty</p>
          <button class="primary-btn">Browse Products</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Order Status Modal -->
  <div class="modal" id="orderStatusModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>My Orders</h2>
        <button class="close-btn" id="closeOrderStatus">&times;</button>
      </div>
      <div class="modal-body">
        <div class="order-timeline">
          <div class="step completed"><div class="icon"><i class="fa fa-clock"></i></div><p>Pending</p></div>
          <div class="step active"><div class="icon"><i class="fa fa-cogs"></i></div><p>Processing</p></div>
          <div class="step"><div class="icon"><i class="fa fa-truck"></i></div><p>Shipped</p></div>
          <div class="step"><div class="icon"><i class="fa fa-box"></i></div><p>Completed</p></div>
        </div>
        <div class="orders-list">
          <h4>Order #12345</h4>
          <p><strong>Item:</strong> Wireless Mouse</p>
          <p><strong>Status:</strong> Processing</p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal" id="submodulesModal">
    <div class="modal-content submodules-content">
      <div class="modal-header">
        <h2>Modules</h2>
        <button class="close-btn" id="closeSubmodules">&times;</button>
      </div>
      <div class="modal-body submodules-body">
        <button class="module-btn" id="viewOrderStatus">
          <i class="fa-solid fa-truck-fast"></i>
          <span>View Order Status</span>
        </button>
        <button class="module-btn" id="viewCompleteOrder">
          <i class="fa-solid fa-box-open"></i>
          <span>View Complete Order</span>
        </button>
      </div>
    </div>
  </div>


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


  <style>
    .notification {
      position: fixed;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #4CAF50;
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      z-index: 1001;
    }
  </style>

</body>
<script src="home.js"></script>

</html>
