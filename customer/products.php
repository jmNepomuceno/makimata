<?php 
    session_start();
    // echo "<pre>"; print_r($_SESSION); echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - MIKAMATA</title>
    <link rel="stylesheet" href="products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php include("../scripts_links/header_links.php") ?>

    <style>
        .customization-content {
            display: flex;
            gap: 30px;
        }
        .customization-left {
            flex: 1.5; /* Give more space to images */
            display: flex;
            gap: 15px;
        }
        .thumbnail-gallery {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .thumbnail-gallery img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid #ddd;
            transition: border-color 0.3s, transform 0.3s;
        }
        .thumbnail-gallery img.active,
        .thumbnail-gallery img:hover {
            border-color: #2d5016; /* Theme color for highlight */
            transform: scale(1.05);
        }
        .main-product-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-grow: 1;
        }
        .customization-left .product-details {
            text-align: center;
            padding-top: 15px;
        }
        .customization-left .product-details h3 {
            margin-bottom: 8px;
        }
        .main-image-container {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #customizationImage {
            max-width: 100%; /* Allow image to take full width of its container */
            max-height: 400px; /* Set a maximum height */
            object-fit: contain; /* Ensure the entire image is visible, scaling down if necessary */
        }
        .customization-right {
            flex: 1;
        }
        .finish-options .radio-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            padding: 10px 5px;
            min-height: 60px; /* Ensure consistent height */
        }
        @media (max-width: 992px) {
            .customization-content { flex-direction: column; }
            .customization-left { flex-direction: column-reverse; }
            .thumbnail-gallery { flex-direction: row; justify-content: center; flex-wrap: wrap; }
        }
        /* Styles for Billing Summary */
        #billingSummaryItems {
            max-height: 250px; /* Reduced max height */
            overflow-y: auto;
            padding-right: 10px; /* Space for scrollbar */
            margin: 0 -10px 15px 0; /* Counteract padding and add bottom margin */
        }
        .billing-summary-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .summary-item-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0;
        }
        .summary-item-details {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .summary-item-details p {
            margin: 0;
        }
        .summary-item-details .item-name {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 5px;
        }
        .summary-item-details .item-attributes {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 4px; /* Reduced margin for tighter spacing */
        }
        .summary-item-details .item-qty {
            font-size: 0.9rem;
            color: #495057;
        }
        .summary-item-price {
            font-weight: 600;
            font-size: 1rem;
            color: #2d5016;
            text-align: right;
            white-space: nowrap;
            align-self: center;
        }
        /* Styles for Customization Footer Price Display */
        .customization-footer .price-display {
            display: flex;
            flex-direction: row; /* Align items horizontally */
            align-items: baseline; /* Align text along their baseline */
            gap: 8px; /* Add some space between label and price */
        }
        .customization-footer .price-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        #customizationModal .modal-content {
            display: flex;
            flex-direction: column;
        }
        #customizationModal .modal-body {
            flex-grow: 1; /* Allow body to take up available space */
            overflow-y: auto; /* Make body scrollable if content overflows */
        }
        .customization-footer {
            flex-shrink: 0; /* Prevent footer from shrinking */
            padding: 0.75rem 2rem; /* Reduced padding for a shorter footer */
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .customization-footer #estimatedPrice {
            font-size: 1.4rem; /* Reduced font size */
            font-weight: 700;
            color: #2d5016;
        }
        /* Styles for Quantity and Stock Display */
        .quantity-wrapper {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 15px;
        }
        .stock-display {
            font-size: 0.8rem;
            color: #000000;
            min-height: 1em; /* Reserve space to prevent layout shift */
        }
        .billing-summary-container {
            flex: 1;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #eee;
            height: fit-content;
        }
        .billing-costs {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .cost-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f3f5; /* Light underline */
        }
        .cost-row:last-child {
            border-bottom: none; /* No underline for total */
        }
        .cost-row span:first-child { color: #6c757d; }
        .cost-row span:last-child, .cost-row strong:last-child { font-weight: 600; color: #343a40; }
        .cost-row.total {
            margin-top: 5px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6; /* Stronger top border */
            border-bottom: none;
            font-size: 1.1rem;
        }
        .cost-row.total strong { font-size: 1.2rem; }
        .cost-row.total #billingSummaryTotal { font-size: 1.6rem; color: #2d5016; }

        #billingModal .modal-content.large {
            max-width: 1280px; /* Increased width for billing modal only */
            width: 90%;
        }
        @media (max-width: 1024px) {
            #billingModal .billing-content {
                grid-template-columns: 1fr; /* Stack columns on tablets and mobile */
            }
        }
        #billingModal .modal-content {
            background-color: #fff; /* Clean white background */
            color: #343a40; /* Dark text */
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: none; /* Remove blur */
            display: flex;
            flex-direction: column;
        }
        #billingModal .modal-header {
            border-bottom: 1px solid #dee2e6;
        }
        #billingModal .modal-header h2 { color: #212529; }
        #billingModal .close-btn { color: #6c757d; }
        #billingModal .modal-body {
            overflow-y: auto; /* Make body scrollable if content overflows */
        }

        #billingModal .billing-content {
            display: grid;
            grid-template-columns: 1fr 1.5fr; /* ~40% for summary, ~60% for form */
            gap: 30px; /* Reduced gap */
            align-items: start; /* Prevents columns from stretching to equal height */
        }

        #billingModal .billing-summary-container {
            background-color: #f8f9fa; /* Light gray background */
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            padding: 1.25rem; /* Reduced padding */
        }
        #billingModal .billing-summary-container h3,
        #billingModal .billing-form-container h3 {
            color: #212529;
            font-size: 1.15rem; /* Reduced font size */
            margin-bottom: 1rem; /* Reduced margin */
            padding-bottom: 0.75rem; /* Reduced padding */
            border-bottom: 1px solid #e9ecef;
        }
        #billingModal .billing-summary-item {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            gap: 1rem;
        }
        #billingModal .summary-item-details .item-name { color: #212529; font-weight: 600; }
        #billingModal .summary-item-details .item-attributes,
        #billingModal .summary-item-details .item-qty { color: #6c757d; }
        #billingModal .summary-item-price { color: #212529; font-weight: 600; }
        
        #billingModal .modal-footer .billing-costs {
            flex-grow: 1; /* Allows this container to take up space */
            max-width: 380px; /* Constrains the width for better alignment */
            display: flex;
            flex-direction: column;
            gap: 0.1rem; /* Minimal gap between rows */
           
        }
        #billingModal .modal-footer .cost-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            line-height: 0.5; /* Adjusted line-height for better spacing */
        }
        #billingModal .modal-footer .cost-row span:first-child,
        #billingModal .modal-footer .cost-row strong:first-child {
            font-size: 0.9rem;
            color: #000000;
        }
        #billingModal .modal-footer .cost-row span:last-child,
        #billingModal .modal-footer .cost-row strong:last-child {
            font-size: 0.9rem;
            font-weight: 600;
        }
        #billingModal .modal-footer .cost-row.total {
            margin-top: 0.4rem; /* Reduced margin */
            padding-top: 0.4rem; /* Reduced padding */
            border-top: 1px solid #dee2e6; /* The underline */
        }
        #billingModal .modal-footer .cost-row.total strong:first-child {
            font-weight: 700;
            font-size: 1rem;
        }
        #billingModal .modal-footer .cost-row.total strong:last-child {
            font-size: 1.6rem;
            color: #2d5016;
            font-weight: 700;
        }

        #billingModal .address-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            padding: 1rem; /* Reduced padding */
        }
        #billingModal .address-card.selected {
            border-color: #2d5016;
            background-color: #f8fbf6;
            box-shadow: 0 0 0 2px rgba(45, 80, 22, 0.2);
        }
        #billingModal .address-card-header strong { color: #212529; }
        #billingModal .address-line {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: #495057;
            margin-bottom: 10px;
        }
        #billingModal .address-line i {
            color: #6c757d;
            margin-top: 5px;
        }
        #billingModal .remove-address-btn { color: #dc3545; }
        
        #billingModal .add-address-btn {
            background-color: #fff;
            border: 1px dashed #ced4da;
            color: #495057;
            border-radius: 0.75rem;
        }
        #billingModal .add-address-btn:hover {
            background-color: #f8f9fa;
            border-color: #2d5016;
            color: #2d5016;
        }
        #billingModal .payment-section {
            margin-top: 1.5rem; /* Reduced margin */
            padding-top: 1.5rem; /* Reduced padding */
            border-top: 1px solid #e9ecef;
        }
        #billingModal .payment-options-grid {
            gap: 10px; /* Reduced gap between payment options */
        }
        #billingModal .payment-tile {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
        }
        #billingModal .payment-option input[type="radio"]:checked + .payment-tile {
            border-color: #2d5016;
            background-color: #f8fbf6;
            box-shadow: none;
        }
        #billingModal .payment-tile > span { color: #212529; }
        #billingModal .payment-logos img {
            height: 24px;
            filter: none; /* Remove grayscale */
            opacity: 1;
        }

        #billingModal .modal-footer {
            background-color: #ffffff;
            border-top: 1px solid #dee2e6;
            padding: 1.125rem 2rem; /* Increased vertical padding to add ~20px height */
            margin-top: auto; /* Push to bottom */
            display: flex;
            justify-content: space-between; /* Pushes costs and button to opposite ends */
            align-items: center;
            gap: 2rem;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            
        }
        #billingModal .modal-actions {
            display: flex; /* Arrange buttons in a row */
            gap: 1rem; /* Add space between the buttons */
            width: auto;
        }
        #billingModal #placeOrderBtn {
            width: auto;
            min-width: 200px; /* Set a reasonable minimum width */
            background-image: linear-gradient(to right, #3a6a24, #2d5016);
            color: #fff;
            border: none;
            font-size: 0.95rem; /* Slightly reduced font size */
            font-weight: 600;
            padding: 0.7rem 1.6rem; /* Slightly reduced padding */
            border-radius: 0.5rem;
            box-shadow: 0 4px 15px rgba(45, 80, 22, 0.2);
        }
        #billingModal #placeOrderBtn:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(45, 80, 22, 0.4);
        }
    </style>
</head>
<body>
    
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <header class="header">
            <div class="container">
                <div style="display: flex; align-items: center; gap: 1rem;">
                
                    <div class="logo" style="width: 100px; height: auto;">
                        <img src="mik/logo.png" alt="MIKAMATA Logo" style="width: 120%; height: auto;">
                    </div>
                </div>
                <nav class="nav">
                    <a href="products.php" class="active">Products</a>
                    <a href="home.php">Overview</a>
                    <a href="aboutus.php">About Us</a>
                    <a href="contactus.php">Contact Us</a>
                </nav>
                <div class="header-actions">
                    <div class="search-box">
                        <input type="text" placeholder="Search" id="searchInput">
                        <i class="fas fa-search"></i>
                    </div>
                    <button class="icon-btn" id="cartBtn" title="Shopping Cart">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count" id="cartCount">0</span>
                    </button>
                    <button class="icon-btn" id="wishlistBtn" title="Wishlist">
                        <i class="fas fa-heart"></i>
                        <span class="wishlist-count" id="wishlistCount">0</span>
                    </button>
                    <button class="icon-btn" id="notificationBtn" title="Notifications">
                        <i class="fa-solid fa-bell" id="notification-icon"></i>
                    </button>
                    <!-- New Submodules Button -->
                    <button class="icon-btn" id="submodulesBtn" title="Modules">
                        <i class="fa-solid fa-th-large"></i>
                    </button>

                    <button class="icon-btn" id="userBtn" title="User">
                        <i class="fa-regular fa-user" id="user-icon"></i>
                    </button>


                    <i class="fa-solid fa-right-from-bracket" id="logout-btn"></i>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1 class="hero-title">PRODUCTS</h1>
                
            </div>
        </section>

        <!-- Product Categories -->
        <section class="product-categories">
            <div class="container">
                <div class="category-filters">
                    <button class="category-btn" data-category="all">ALL</button>
                    <button class="category-btn" data-category="lampshades">LAMPSHADES</button>
                    <button class="category-btn" data-category="mugs">MUGS</button>
                    <button class="category-btn" data-category="basket">BASKET</button>
                    <button class="category-btn" data-category="kitchenware">KITCHENWARE</button>
                    <button class="category-btn" data-category="furnitures">FURNITURES</button>
                </div>
            </div>
        </section>

        <!-- Products Grid -->
        <section class="products-section">
            <div class="container">
                <div class="products-grid" id="productsGrid">
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <button class="page-btn prev">&lt;</button>
                    <button class="page-btn active" data-page="1">1</button>
                    <button class="page-btn" data-page="2">2</button>
                    <button class="page-btn next">&gt;</button>
                </div>
            </div>
        </section>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="modal" id="cartModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Shopping Cart</h2>
                <button class="close-btn" id="closeCart">&times;</button>
            </div>
            <div class="modal-body" id="cartItems">
            </div>
            <div class="modal-footer" id="cartFooter">
            </div>
        </div>
    </div>

    <!-- Wishlist Modal -->
    <div class="modal" id="wishlistModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Wishlist</h2>
                <button class="close-btn" id="closeWishlist">&times;</button>
            </div>
            <div class="modal-body" id="wishlistItems"></div>
        </div>
    </div>

    <div class="modal" id="orderStatusModal">
        <div class="modal-content orderStatusModal-content">
            <div class="modal-header">
            <h2>My Orders</h2>
            <button class="close-btn" id="closeOrderStatus">&times;</button>
            </div>

            <!-- FILTERS -->
            <div class="modal-filters">
            <label>
                From: <input type="date" id="filterStartDate">
            </label>
            <label>
                To: <input type="date" id="filterEndDate">
            </label>
            <label>
                Status:
                <select id="filterStatus">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                </select>
            </label>
            <button id="applyFilters">Apply</button>
            </div>

            <div class="modal-body"></div>
        </div>
    </div>

    <div class="modal" id="completeOrdersModal">
        <div class="modal-content complete-orders-content">
            <div class="modal-header">
                <h2>Completed Orders</h2>
                <button class="close-btn" id="closeCompleteOrders">&times;</button>
            </div>

            <!-- Filter Section -->
            <div class="modal-filters">
                <label>Start Date: <input type="date" id="completedStartDate"></label>
                <label>End Date: <input type="date" id="completedEndDate"></label>
                <button id="applyCompletedFilters" class="filter-btn">Apply Filters</button>
            </div>

            <!-- Orders List -->
            <div class="modal-body complete-orders-body" id="completedOrdersList">
            <!-- Orders will be injected here -->
            </div>
        </div>
    </div>

    <div id="order-detail-modal" class="modal">
        <div class="modal-content large">
        <div class="modal-header">
            <h3>Order Details #<span id="modal-order-id"></span></h3>
            <!-- <span class="close" onclick="closeOrderDetailModal()">&times;</span> -->
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div class="order-detail-layout">
            <div class="order-detail-main">
                <!-- Order Items -->
                <div class="details-section">
                <h4><i class="fas fa-list"></i> Order Items (<span id="modal-item-count">0</span>)</h4>
                <div id="modal-order-items-list" class="order-items-list"> 
                </div>
                </div>
            </div>

            <div class="order-detail-sidebar">
                <!-- Customer Info Card -->
                <div class="details-section">
                <h4><i class="fas fa-user"></i> Customer</h4>
                <div class="detail-row"><strong>Name:</strong> <span id="modal-customer-name"></span></div>
                <div class="detail-row"><strong>Email:</strong> <span id="modal-customer-email"></span></div>
                <div class="detail-row"><strong>Contact:</strong> <span id="modal-customer-phone"></span></div>
                <div class="detail-row"><strong>Shipping:</strong> <span id="modal-shipping-address"></span></div>
                </div>

                <!-- Totals Card -->
                <div class="details-section">
                <h4><i class="fas fa-receipt"></i> Totals</h4>
                <div class="totals-summary" id="modal-totals-summary"> 
                </div>
                </div>
            </div>
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

    <!-- Customization Modal -->
    <div class="modal" id="customizationModal">
        <div class="modal-content large">
            <div class="modal-header">
                <h2 id="customizationTitle">Customize Product</h2>
                <button class="close-btn" id="closeCustomization">&times;</button>
            </div>
            <div class="modal-body">
                <div class="customization-content">
                    <div class="customization-left">
                        <div class="thumbnail-gallery" id="thumbnailGallery">
                        </div>
                        <div class="main-product-display">
                            <div class="main-image-container">
                                <!-- <img id="customizationImage" src="/placeholder.svg" alt="Main product view"> -->
                                <img id="customizationImage" src="../customer/mik/logo.png" alt="Main product view">
                            </div>
                            <div class="product-details">
                                <h3 id="customizationName"></h3>
                                <p id="customizationDescription"></p>
                            </div>
                        </div>
                    </div>
                    <div class="customization-right">
                        <div class="customization-options">
                            <div class="option-group">
                                <label class="option-label">Size</label>
                                <div class="radio-group size-options">
                                
                                    <label>
                                        <input type="radio" name="size" value="medium" checked>
                                        <div class="radio-tile">
                                            <span class="radio-label">Standard</span>
                                    <span class="radio-sublabel">(Default)</span>
                                        </div>
                                    </label>
                                    <label>
                                        <input type="radio" name="size" value="large">
                                        <div class="radio-tile">
                                            <span class="radio-label">Large +10%</span>
                                    <span class="radio-sublabel">+10% Price</span> 
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="option-group">
                                <label class="option-label">Finish</label>
                                <div class="radio-group finish-options">
                                    <label title="Natural Finish (Standard)">
                                        <input type="radio" name="finish" value="natural" checked>
                                        <div class="radio-tile" data-color="natural">
                                            <span class="color-swatch natural"></span>
                                            <span class="radio-sublabel">Burly</span>
                                        </div>
                                    </label>
                                    <label title="Dark Stain Finish (Standard)">
                                        <input type="radio" name="finish" value="dark">
                                        <div class="radio-tile" data-color="dark">
                                            <span class="color-swatch dark"></span>
                                            <span class="radio-sublabel">Coffee</span>
                                        </div>
                                    </label>
                                    <label title="Premium Lacquer Finish (+20%)">
                                        <input type="radio" name="finish" value="premium">
                                        <div class="radio-tile" data-color="premium">
                                            <span class="color-swatch premium"></span>
                                            <span class="radio-sublabel">Rust Brown +₱40</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="option-group">
                                <label class="option-label">Quantity</label>
                                <div class="quantity-wrapper">
                                    <div class="quantity-controls">
                                        <button type="button" id="decreaseQty" class="qty-btn">-</button>
                                        <input 
                                        type="number" 
                                        id="quantity" 
                                        class="qty-display" 
                                        value="1" 
                                        min="1" 
                                        step="1" 
                                        />
                                        <button type="button" id="increaseQty" class="qty-btn">+</button>
                                    </div>
                                    <small id="productStock" class="stock-display"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="customization-footer">
                <div class="price-display">
                    <span class="price-label">Estimated Price:</span>
                    <span id="estimatedPrice">₱470.00</span>
                </div>
                <div class="modal-actions">
                   
                    <button class="btn-secondary" id="addCustomizedToCart">Add to Cart</button> 
                    <button class="btn-primary" id="buyNowBtn">Buy Now</button>
                </div>
            </div>
        </div>
    </div>
 
    <!-- Billing Modal -->
    <div class="modal" id="billingModal">
        <div class="modal-content large">
            <div class="modal-header">
                <h2>Checkout</h2>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <div class="billing-content">
                    <!-- Left Column: Order Summary -->
                    <div class="billing-summary-container">
                        <h3 id="billingSummaryHeader">Order Summary</h3>
                        <div id="billingSummaryItems">
                            <div class="billing-summary-item">
                                <!-- <img src="https://via.placeholder.com/70/252525/FFFFFF" alt="Product" class="summary-item-img"> -->
                                <img src="../customer/mik/logo.png" alt="Product" class="summary-item-img">
                                <div class="summary-item-details">
                                    <p class="item-name">Spyder Reboot2 Half Face Helmet</p>
                                    <p class="item-attributes">Matte Silver</p>
                                    <p class="item-qty">₱1,250.00 x 1</p>
                                </div>
                                <span class="summary-item-price">₱1,250.00</span>
                            </div>
                        </div>
                    </div>
                    <!-- Right Column: Delivery & Payment -->
                    <div class="billing-form-container">
                        <h3>Delivery</h3>

                        <button class="add-address-btn">
                            <i class="fas fa-plus"></i> Add New Address
                        </button>

                        <div id="newAddressFormContainer" style="display:none; margin-top:1rem;">
                        </div>

                        <div class="payment-section">
                            <h3>Payment Method</h3>
                            <div class="payment-options-grid">
                                <label class="payment-option">
                                    <input type="radio" name="payment" value="cod" checked>
                                    <div class="payment-tile">
                                        <span>Cash on Delivery</span>
                                    </div>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment" value="ewallet">
                                    <div class="payment-tile">
                                        <span>E-Wallets / Card</span>
                                        <div class="payment-logos">
                                            <img src="./mik/gcash.png" alt="GCash">
                                            <img src="./mik/maya.png" alt="Maya">
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="billing-costs">
                    <div class="cost-row">
                        <span>Subtotal:</span>
                        <span id="billingSubtotal">₱1,250.00</span>
                    </div>
                    <div class="cost-row">
                        <span>Shipping Fee:</span>
                        <span id="billingShipping">₱50.00</span>
                    </div>
                    <div class="cost-row total">
                        <strong>Total</strong>
                        <strong id="billingSummaryTotal">₱1,300.00</strong>
                    </div>
                </div>
                <div class="modal-actions">
                    <button class="btn-secondary" id="cancelBilling">Back</button>
                    <button class="btn-primary" id="placeOrderBtn">Place Order</button>
                </div>
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
                    <h3>Contacts Us</h3>
                    <div class="contact-info">
                        <p><i class="fas fa-envelope"></i> Mikamatahandicrafts@gmail.com</p>
                        <p><i class="fas fa-phone"></i> +639217329592 / +639622328554</p>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Index</h3>
                    <ul class="footer-links"> 
                        <li><a href="home.html">Home</a></li>
                        <li><a href="aboutus.html">About Us</a></li>
                        <li><a href="products.html">Products</a></li>
                        <li><a href="contactus.html">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; Copyright 2025 | MIKAMATA</p>
        </div>
    </footer>
        
    <!-- Success Notification -->
    <div id="successNotification" class="success-notification">
        <div class="notification-header">
            <i class="fas fa-check-circle"></i>
            <span>Added to Cart</span>
        </div>
        <div class="notification-body">
            <img id="notificationImage" src="" alt="Product Image">
            <div class="notification-details">
                <strong id="notificationName">Product Name</strong>
                <small id="notificationSize">Size: Medium</small>
                <small id="notificationFinish">Finish: Natural</small>
                <small id="notificationQuantity">Quantity: 1</small>
            </div>
        </div>
        <div class="notification-progress"></div>
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

    <div class="modal" id="notificationModal">
        <div class="modal-content">
            <span class="close" id="closeNotificationModal">&times;</span>
            <h2>Notifications</h2>
            <div id="notificationList" class="notification-list">
            <p class="loading-text">Loading...</p>
            </div>
        </div>
    </div>

    <div id="toast-container"></div>

    <script src="products.js"></script>
</body>
</html>
