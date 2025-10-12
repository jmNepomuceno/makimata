
let PRODUCTS = []

const FINISH_DISPLAY_NAMES = {
    natural: 'Burly',
    dark: 'Coffee',
    premium: 'Rust Brown'
};

// State management
let selectedCategory = 'all';
let cartItems = [];
let wishlistItems = [];
let searchQuery = '';
let currentCustomizingProduct = null;

// DOM elements
const productsGrid = document.getElementById('productsGrid');
const searchInput = document.getElementById('searchInput');
const cartBtn = document.getElementById('cartBtn');
const wishlistBtn = document.getElementById('wishlistBtn');
const cartModal = document.getElementById('cartModal');
const wishlistModal = document.getElementById('wishlistModal');
const customizationModal = document.getElementById('customizationModal');
const billingModal = document.getElementById('billingModal');

const submodulesBtn = document.getElementById('submodulesBtn');
const submodulesModal = document.getElementById('submodulesModal'); // create this modal in HTML
const closeSubmodules = document.getElementById('closeSubmodules');

const viewOrderStatusBtn = document.getElementById('viewOrderStatus');

/**
 * Injects custom CSS for animations into the document head.
 * This makes the "Add to Cart" notification more dynamic.
 */

function showToast(message, type = "info") {
    const container = $("#toast-container");
    const toast = $("<div>").addClass(`toast ${type}`).text(message);
    container.append(toast);
    setTimeout(() => toast.remove(), 4000);
}

function injectCustomStyles() {
    const style = document.createElement('style');
    style.textContent = `
        /* Shake animation for warnings */
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        .is-shaking {
            animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both;
        }

        /* Cart shake animation on item arrival */
        @keyframes cart-shake-effect {
            10%, 90% { transform: scale(1.1) rotate(-2deg); }
            20%, 80% { transform: scale(1.1) rotate(2deg); }
            30%, 50%, 70% { transform: scale(1.1) rotate(-2deg); }
            40%, 60% { transform: scale(1.1) rotate(2deg); }
            100% { transform: scale(1) rotate(0); }
        }

        .cart-shake {
            animation: cart-shake-effect 0.4s ease-in-out;
        }

        /* Make cart and wishlist modals wider */
        #cartModal .modal-content,
        #wishlistModal .modal-content {
            max-width: 850px;
            width: 90vw;
        }

        /* --- New styles for better warning placement --- */

        /* This forces the footer to stack and center its children (warning, buttons) */
        #customizationModal .modal-footer {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* This is the container for the buttons */
        .customization-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            width: 100%;
        }

        /* Customization Modal Warning Enhancement */
        .customization-warning {
            display: none; /* Initially hidden, controlled by JS */
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #856404; /* A more standard 'warning' yellow text color */
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            font-weight: 600;
            font-size: 0.9em;
            padding: 10px 15px; /* Add more horizontal padding */
            border-radius: 4px; 
            margin-bottom: 15px; /* Adds space between the warning and the buttons below it */
        }

        /* Stock Display */
        .stock-display {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
            width: 100%;
            text-align: center;
        }
        /* --- Polished "3D" Notification Styles --- */
        .success-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 360px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.2), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 2000;
            overflow: hidden;
            opacity: 0;
            transform: translateX(120%);
            visibility: hidden;
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .success-notification.show {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        .notification-header {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #f0fdf4; /* Lighter green */
            color: #2e7d32;
            padding: 10px 15px;
            font-weight: 600;
            font-size: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .notification-header .fa-check-circle {
            font-size: 1.2rem;
        }

        .notification-body {
            display: flex;
            padding: 15px;
            gap: 15px;
            align-items: flex-start;
        }

        #notificationImage {
            width: 65px;
            height: 65px;
            object-fit: cover;
            border-radius: 6px;
        }

        .notification-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 0.85rem;
            color: #555;
        }
        .notification-details strong { font-size: 0.95rem; font-weight: 600; color: #222; }
        .notification-details small { line-height: 1.4; }

        .notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            width: 100%;
            background-color: #4caf50;
            transform-origin: left;
        }

        .success-notification.show .notification-progress {
            animation: progress-timer 3.5s linear forwards;
        }

        @keyframes progress-timer {
            from { transform: scaleX(1); }
            to { transform: scaleX(0); }
        }

        /* --- Billing Module Order Summary Enhancement --- */
        .order-summary-box {
            border: 2px solid #e9ecef; /* Cleaner, modern border */
            border-radius: 8px;
            padding: 25px;
            background-color: #fff;
        }

        .order-summary-box .summary-item {
            /* Styles for individual items are now handled in the JS rendering for better alignment */
        }

        /* --- Billing Summary Total Section --- */
        .order-summary-box #billingSummaryTotal {
            display: flex;
            flex-direction: column; /* Stack lines vertically */
            gap: 8px; /* Space between subtotal, shipping, and total */
            padding-top: 1rem;
            margin-top: 1rem;
            border-top: 2px solid #e9ecef;
            font-size: 1rem; /* Base font-size for this section */
            font-weight: 500;
        }

        .summary-line {
            display: grid;
            grid-template-columns: 1fr auto; /* Label takes space, Amount aligns right */
            align-items: center;
            color: #495057;
        }

        .summary-line.total-line {
            font-size: 1.5rem;
            font-weight: 700; /* Bolder for final total */
            color: #000;
            margin-top: 8px;
        }
        
        .summary-customization-details {
            font-size: 0.95rem; /* Increased size for better readability */
            color: #555;       /* Darker text for better contrast */
            margin-top: 5px; /* Corrected spacing */
            font-weight: 500;
        }
    `;
    // Add new styles for the billing form to match the theme
    style.textContent += `
        #billingModal .modal-content {
            max-width: 1100px; /* Increased width for better spacing */
            width: 90vw;
        }

        /* --- Billing Modal Footer Layout --- */
        #billingModal .modal-footer {
            display: flex;
            justify-content: flex-end; /* Pushes all content to the right */
            align-items: center;
            padding-top: 20px; /* Add space above the footer content */
            margin-top: 20px; /* Add space between form and footer */
            border-top: 1px solid #dee2e6; /* A clear separator line */
        }

        #billingModal .modal-footer .modal-actions {
            display: flex;
            align-items: center;
            gap: 15px; /* Adds space between the 'Back' and 'Place Order' buttons */
        }


        #billingForm {
            padding: 20px;
            background-color: #f9f9f9; /* Light background for the form */
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        #billingForm .form-group {
            margin-bottom: 15px;
        }

        #billingForm label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        #billingForm input[type="text"],
        #billingForm input[type="email"],
        #billingForm input[type="tel"],
        #billingForm textarea {
            width: calc(100% - 20px); /* Account for padding */
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box; /* Include padding in width */
        }

        #billingForm textarea {
            resize: vertical; /* Allow vertical resizing */
        }

        /* --- Billing Module Sticky Summary Layout --- */
        #billingModal .modal-body {
            /* This ensures the two columns (summary and form) are aligned at the top */
            align-items: flex-start;
        }

        .billing-summary-sticky-container {
            position: -webkit-sticky; /* For Safari */
            position: sticky;
            top: 20px; /* Adjust distance from top of modal's scrollable area */
        }

        /* --- Billing Form Modernization --- */
        .billing-form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee; /* Light separator */
        }

        .billing-form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .billing-form-section h5 {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff; /* Highlight section title */
            padding-bottom: 10px;
            display: inline-block; /* Shrink border to content width */
        }

        .billing-form-section .form-group {
            margin-bottom: 20px;
        }

        .billing-form-section input[type="text"],
        .billing-form-section input[type="email"],
        .billing-form-section input[type="tel"],
        .billing-form-section textarea {
            border: 1px solid #ccc;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.08); /* Subtle inner shadow */
        }

        /* --- New Address & Payment Styles --- */
        .address-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px 40px 15px 15px; /* Add more padding on the right for the remove button */
            margin-bottom: 10px;
            position: relative;
            background-color: #f9f9f9; /* Lighter background for non-selected cards */
            transition: all 0.2s ease-in-out;
        }
        .address-card.selected {
            border-color: #2d5016; /* Theme color for selected border */
            border-width: 2px;
            padding: 14px 39px 14px 14px; /* Adjust padding to account for border */
            background-color: #f0f8f0; /* Greenish tint for selected */
        }
        .address-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start; /* Align items to the top */
            margin-bottom: 5px;
        }
        .address-card strong {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-right: 10px; /* Space between name and remove button */
        }
        .address-card p {
            margin: 0 0 5px;
            color: #666;
            font-size: 0.95rem;
        }
        .remove-address {
            position: absolute;
            top: 8px;
            right: 15px;
            background: none;
            border: none;
            color: #999; /* Softer color for the icon */
            cursor: pointer;
            font-size: 1.6rem; /* Make the 'x' icon larger */
            font-weight: bold;
            line-height: 0.8; /* Adjust line height for better vertical centering */
            transition: color 0.2s ease;
        }
        .remove-address:hover {
            color: #dc3545; /* Red on hover for delete action */
        }
        .add-address-btn {
            background-color: #e9ecef; /* Softer background for less emphasis */
            color: #333;
            border-color: #ddd;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }

        .payment-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .payment-option {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            cursor: pointer;
            background-color: #f9f9f9;
            transition: all 0.2s ease-in-out;
        }
        .payment-option:hover {
            border-color: #aaa; /* Indicate interactivity on hover */
        }
        .payment-option.selected {
            border-color: #2d5016; /* Theme color for selected border */
            background-color: #f0f8f0; /* Greenish tint for selected */
        }
        .payment-option label {
            cursor: pointer; /* Ensure label is also a pointer */
        }
        .payment-option input[type="radio"] {
            margin-right: 15px;
        }
        .payment-icons {
            margin-left: auto;
            display: flex;
            gap: 10px;
        }
        .payment-icons img { height: 24px; }
    `;
    document.head.appendChild(style);
}

// Initialize app
function init() {
    // Make loading from localStorage robust to prevent parsing errors from stopping script execution.
    try {
        const storedCart = localStorage.getItem('cartItems');
        if (storedCart) {
            // Ensure cartItems is always an array.
            const parsedCart = JSON.parse(storedCart);
            cartItems = Array.isArray(parsedCart) ? parsedCart : [];
        }
        const storedWishlist = localStorage.getItem('wishlistItems');
        if (storedWishlist) {
            const parsedWishlist = JSON.parse(storedWishlist);
            wishlistItems = Array.isArray(parsedWishlist) ? parsedWishlist : [];
        }
    } catch (error) {
        console.error("Error parsing data from localStorage. Resetting state.", error);
        cartItems = [];
        wishlistItems = [];
        localStorage.removeItem('cartItems');
        localStorage.removeItem('wishlistItems');
    }

    // Check for category from URL and set it
    const urlParams = new URLSearchParams(window.location.search);
    const categoryFromUrl = urlParams.get('category');
    const validCategories = ['lampshades', 'mugs', 'basket', 'kitchenware', 'furnitures', 'all'];
    if (categoryFromUrl && validCategories.includes(categoryFromUrl)) {
        selectedCategory = categoryFromUrl;
    }

    injectCustomStyles();
    // renderProducts();
    fetchProducts()
    setupEventListeners();
    updateCounts();
    updateActiveCategoryUI(); // Update UI on initial load
}

$(document).on("click", ".cancel-order-btn", function() {
    const orderId = $(this).data("order");
    if (!confirm("Are you sure you want to cancel this order?")) return;

    $.ajax({
        url: "../assets/php/cancel_order.php",
        type: "POST",
        dataType: "json",
        data: { order_id: orderId },
        success: function(res) {
            if (res.status === "success") {
                alert("Order cancelled successfully.");
                fetchOrders(); // refresh list
            } else {
                alert("Error: " + res.message);
            }
        },
        error: function() {
            alert("Something went wrong while cancelling order.");
        }
    });
});


// Open modal
$("#viewCompleteOrder").on("click", function () {
    $("#submodulesModal").hide();
    $("#completeOrdersModal").show();
    fetchCompletedOrders();
});

// Close modal
$("#closeCompleteOrders").on("click", function () {
    $("#completeOrdersModal").hide();
});

// Apply filters
$("#applyCompletedFilters").on("click", function () {
    const filters = {
        startDate: $("#completedStartDate").val(),
        endDate: $("#completedEndDate").val()
    };
    fetchCompletedOrders(filters);
});

// simple helper to escape HTML (optional but recommended)\

function escapeHtml(str) {
  if (!str && str !== 0) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function fetchAndRenderAddresses() {
  $.ajax({
    url: '../assets/php/fetch_user_addresses.php',
    type: 'GET',
    dataType: 'json',
    success: function(response) {
      // debug
      console.log('fetch_user_addresses response:', response);

      if (response.status === 'success') {
        const addresses = response.data || [];
        const container = $('.billing-form-container');

        // remove previously appended dynamic cards only (keeps any hardcoded/default card)
        container.find('.address-card.dynamic-address').remove();

        // build html for new addresses (class dynamic-address to mark them)
        let html = '';
        addresses.forEach(addr => {
          html += `
            <div class="address-card dynamic-address ${addr.is_default ? 'selected' : ''}" data-id="${escapeHtml(addr.id)}">
              <div class="address-card-header">
                <strong>${escapeHtml(addr.full_name)}</strong>
                <button class="remove-address-btn" data-id="${escapeHtml(addr.id)}" title="Remove Address">&times;</button>
              </div>
              <div class="address-line">
                <i class="fas fa-map-marker-alt"></i>
                <span>${escapeHtml(addr.barangay)}, ${escapeHtml(addr.city)}, ${escapeHtml(addr.province)}<br>${escapeHtml(addr.house_no)}</span>
              </div>
              <div class="address-line">
                <i class="fas fa-phone-alt"></i>
                <span>${escapeHtml(addr.mobile_number)}</span>
              </div>
            </div>
          `;
        });

        // insert new cards before the "Add New Address" button so Add button remains last
        container.find('.add-address-btn').before(html);

        // (Optional) If you want the first dynamic address to also populate the user-name/address fields:
        if (addresses.length && !container.find('.address-card.selected').length) {
          // if none selected, pick first and mark selected
          container.find('.address-card.dynamic-address').first().addClass('selected');
        }
      } else {
        console.warn('fetch_user_addresses warning:', response.message);
      }
    },
    error: function(xhr, status, error) {
      console.error("Error fetching addresses:", error);
    }
  });
}




// Fetch completed orders
function fetchCompletedOrders(filters = {}) {
    $.ajax({
        url: "../assets/php/fetch_completed_orders.php",
        type: "POST",
        data: filters,
        dataType: "json",
        success: function (response) {
            console.log(response)
            if (response.status === "success") {
                renderCompletedOrders(response.orders);
            } else {
                $("#completedOrdersList").html(
                    `<p class="error">${response.message}</p>`
                );
            }
        },
        error: function () {
            $("#completedOrdersList").html(
                `<p class="error">Failed to load completed orders.</p>`
            );
        }
    });
}

// Render orders
function renderCompletedOrders(orders) {
    if (!orders.length) {
        $("#completedOrdersList").html(`<p>No completed orders found.</p>`);
        return;
    }

    console.log(orders)

    let html = "";
    orders.forEach(order => {
        const itemsHtml = order.items.map(item => {
            const attrList = item.attributes
                ? item.attributes.split("|").map(a => `<div class="attr-item">${a.trim()}</div>`).join("")
                : "<div class='attr-item text-muted'>No customization</div>";

            return `
                <div class="order-item">
                    <div>
                        <div class="item-name fw-bold">${item.name} (${item.qty}×)</div>
                        <div class="item-attrs">${attrList}</div>
                    </div>
                    <div class="item-price">₱${item.price}</div>
                </div>
            `;
        }).join("");

        // Review UI
        let reviewHtml = "";
        if (order.review) {
            const starsHtml = [...Array(5)].map((_, i) =>
                `<i class="fa-star ${i < order.review.rating ? "fas" : "far"}"></i>`
            ).join("");

            reviewHtml = `
                <div class="review-display">
                    <div class="stars">${starsHtml}</div>
                    <p class="review-comment-text">${order.review.comment || "(No comment provided)"}</p>
                    <small class="review-date">Reviewed on ${order.review.created_at}</small>
                </div>
            `;
        } else {
            reviewHtml = `
                <div class="review-section" data-order="${order.id}">
                    <div class="stars">
                        ${[1,2,3,4,5].map(star =>
                            `<i class="fa-star far" data-star="${star}"></i>`
                        ).join("")}
                    </div>
                    <textarea class="review-comment" placeholder="Write your comment (optional)..."></textarea>
                    <button class="submit-review">Submit Review</button>
                </div>
            `;
        }

        html += `
            <div class="completed-order-card">
                <div class="order-header">
                    <h3>Order #${order.id}</h3>
                    <span>${order.date}</span>
                </div>
                <div class="order-items">${itemsHtml}</div>
                <div class="order-total">Total: ₱${order.total}</div>
                <div class="order-actions">
                    <button class="view-receipt-btn" data-id="${order.id}">
                        <i class="fas fa-receipt"></i> View Receipt
                    </button>
                </div>
                ${reviewHtml}
            </div>
        `;
    });

    $("#completedOrdersList").html(html);

    // ⭐ Rating logic
    $(".stars i").on("click", function () {
        const starValue = $(this).data("star");
        const parent = $(this).parent();
        parent.find("i").removeClass("fas").addClass("far");
        parent.find("i").each(function () {
            if ($(this).data("star") <= starValue) $(this).removeClass("far").addClass("fas");
        });
        parent.attr("data-selected", starValue);
    });

    // 💬 Submit review
    $(".submit-review").on("click", function () {
        const parent = $(this).closest(".review-section");
        const orderId = parent.data("order");
        const stars = parent.find(".stars").attr("data-selected") || 0;
        const comment = parent.find(".review-comment").val();

        $.ajax({
            url: "../assets/php/submit_review.php",
            type: "POST",
            data: { orderId, stars, comment },
            dataType: "json",
            success: res => {
                alert(res.status === "success" ? "Review submitted!" : res.message);
                if (res.status === "success") fetchCompletedOrders();
            },
            error: () => alert("Failed to submit review.")
        });
    });
}



function fetchOrders(filters = {}) {
    $.ajax({
        url: "../assets/php/fetch_user_orders.php",
        type: "POST",
        dataType: "json",
        data: filters, // pass filters
        success: function(response) {
            const modalBody = $("#orderStatusModal .modal-body");
            modalBody.empty();

            if (response.status === "success") {
                const orders = response.orders;

                if (orders.length === 0) {
                    modalBody.html('<div class="no-orders">No orders found.</div>');
                    return;
                }

                orders.forEach((order, index) => {
                    const orderNumber = index + 1;

                    // Timeline (same as before) ...
                    const timelineSteps = ['pending','processing','shipped','completed'];
                    let timelineHtml = '<div class="order-timeline">';
                    timelineSteps.forEach(step => {
                        let stepClass = '';
                        if (step === order.status) stepClass = 'active';
                        else if (timelineSteps.indexOf(step) < timelineSteps.indexOf(order.status)) stepClass = 'completed';

                        let icon = '';
                        switch(step) {
                            case 'pending': icon = '<i class="fa fa-clock"></i>'; break;
                            case 'processing': icon = '<i class="fa fa-cogs"></i>'; break;
                            case 'shipped': icon = '<i class="fa fa-truck"></i>'; break;
                            case 'completed': icon = '<i class="fa fa-box"></i>'; break;
                        }

                        timelineHtml += `
                            <div class="step ${stepClass}">
                                <div class="icon">${icon}</div>
                                <p>${step.charAt(0).toUpperCase() + step.slice(1)}</p>
                            </div>
                        `;
                    });
                    timelineHtml += '</div>';

                    // Items
                    let itemsHtml = `
                        <table class="order-items-table">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Total Price</th>
                                    <th>Attributes</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    order.items.forEach(item => {
                        let attributesHtml = '';

                        if (item.attributes && item.attributes.trim() !== '') {
                            // Try to parse JSON
                            try {
                                const attr = JSON.parse(item.attributes);
                                const attrPairs = [];

                                if (attr.sizeLabel) attrPairs.push(`<div><strong>Size:</strong> ${attr.sizeLabel}</div>`);
                                if (attr.finish) attrPairs.push(`<div><strong>Finish:</strong> ${attr.finish}</div>`);
                                if (attr.engravingText) attrPairs.push(`<div><strong>Engraving:</strong> "${attr.engravingText}"</div>`);
                                if (attr.messageCard) attrPairs.push(`<div><strong>Message Card:</strong> "${attr.messageCard}"</div>`);
                                if (attr.giftPackaging && attr.giftPackaging !== 'none') attrPairs.push(`<div><strong>Gift Packaging:</strong> ${attr.giftPackaging}</div>`);
                                if (attr.specialHandling && attr.specialHandling !== 'none') attrPairs.push(`<div><strong>Handling:</strong> ${attr.specialHandling}</div>`);
                                if (attr.deliveryPreference && attr.deliveryPreference !== 'standard') attrPairs.push(`<div><strong>Delivery:</strong> ${attr.deliveryPreference}</div>`);

                                attributesHtml = `<div class="attributes-block">${attrPairs.join('')}</div>`;
                            } catch {
                                // Fallback for plain text
                                const parts = item.attributes.split('|').map(p => `<div>${p.trim()}</div>`).join('');
                                attributesHtml = `<div class="attributes-block">${parts}</div>`;
                            }
                        } else {
                            attributesHtml = `<span class="no-attributes">No customizations</span>`;
                        }

                        itemsHtml += `
                            <tr>
                                <td>${item.name}</td>
                                <td>₱${parseFloat(item.price).toFixed(2)} × ${item.qty}</td>
                                <td>${attributesHtml}</td>
                            </tr>
                        `;
                    });

                    itemsHtml += `
                            </tbody>
                        </table>
                    `;


                    // Cancel button if pending
                    let cancelBtn = '';
                    if (order.status === "pending") {
                        cancelBtn = `<button class="cancel-order-btn" data-order="${order.id}">Cancel Order</button>`;
                    }

                    modalBody.append(`
                        <div class="orders-list">
                            <h4>Order #${orderNumber}</h4>
                            <p><strong>Customer:</strong> ${order.customer.name}</p>
                            <p><strong>Address:</strong> ${order.shippingAddress}</p>
                            <p><strong>Phone:</strong> ${order.customer.phone}</p>
                            <p><strong>Payment:</strong> ${order.paymentMethod.toUpperCase()}</p>
                            <p><strong>Total:</strong> ₱${parseFloat(order.total).toFixed(2)}</p>
                            <p><strong>Status:</strong> ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</p>
                            ${itemsHtml}
                            ${timelineHtml}
                            ${cancelBtn}
                            <hr>
                        </div>
                    `);
                });
            } else {
                modalBody.html(`<div class="error-message">Error: ${response.message}</div>`);
            }
        },
        error: function() {
            $("#orderStatusModal .modal-body").html('<div class="error-message">Something went wrong. Please try again.</div>');
        }
    });
}

// Event listeners
function setupEventListeners() {
    // Category filtering
    document.querySelectorAll('.category-btn').forEach(btn => btn.addEventListener('click', handleCategoryChange));

    // Search functionality
    if (searchInput) searchInput.addEventListener('input', handleSearch);

    // Modal functionality
    if (cartBtn) cartBtn.addEventListener('click', () => openModal('cart'));
    if (wishlistBtn) wishlistBtn.addEventListener('click', () => openModal('wishlist'));
    
    document.querySelectorAll('.close-btn').forEach(btn => btn.addEventListener('click', closeModals));

    // Customization modal listeners
    const decreaseQtyBtn = document.getElementById('decreaseQty');
    const increaseQtyBtn = document.getElementById('increaseQty');
    const quantityInput = document.getElementById('quantity');

    if (decreaseQtyBtn) decreaseQtyBtn.addEventListener('click', () => updateQuantity(-1));
    if (increaseQtyBtn) increaseQtyBtn.addEventListener('click', () => updateQuantity(1));

    // 🔹 Listen for manual input changes
    if (quantityInput) {
        quantityInput.addEventListener('input', () => {
            let value = parseInt(quantityInput.value) || 1;
            const stock = currentCustomizingProduct.stock || 999;

            if (value < 1) value = 1;
            if (value > stock) value = stock;

            quantityInput.value = value;
            updateCustomizationView(false); // Refresh price dynamically
        });
    }
    
    const addCustomizedToCartBtn = document.getElementById('addCustomizedToCart');
    if (addCustomizedToCartBtn) addCustomizedToCartBtn.addEventListener('click', addCustomizedToCart);
    const buyNowBtn = document.getElementById('buyNowBtn');
    if (buyNowBtn) buyNowBtn.addEventListener('click', buyNow);

    // Update price when customization changes
    document.querySelectorAll('input[name="finish"]').forEach(input => input.addEventListener('change', () => updateCustomizationView(true)));
    
    // When a size is selected, just update the view. The image will be handled by the finish.
    document.querySelectorAll('input[name="size"]').forEach(input => input.addEventListener('change', () => {
        // We pass `true` to force an image update if a finish is already selected,
        // otherwise it will just update the price.
        updateCustomizationView(true);
    }));

    // ---- Engraving (text input) ----
    const engravingInput = document.querySelector('input[name="engravingText"]');
    if (engravingInput) {
        engravingInput.addEventListener('input', () => updateCustomizationView(true));
    }

    // ---- Gift Packaging (dropdown) ----
    const giftSelect = document.querySelector('select[name="giftPackaging"]');
    if (giftSelect) {
        giftSelect.addEventListener('change', () => updateCustomizationView(true));
    }

    // ---- Message Card (textarea) ----
    const messageCard = document.querySelector('textarea[name="messageCard"]');
    if (messageCard) {
        messageCard.addEventListener('input', () => updateCustomizationView(true));
    }

    // ---- Special Handling (dropdown) ----
    const handlingSelect = document.querySelector('select[name="specialHandling"]');
    if (handlingSelect) {
        handlingSelect.addEventListener('change', () => updateCustomizationView(true));
    }

    // ---- Delivery Preference (dropdown) ----
    const deliverySelect = document.querySelector('select[name="deliveryPreference"]');
    if (deliverySelect) {
        deliverySelect.addEventListener('change', () => updateCustomizationView(true));
    }

    document.getElementById('engravingText').addEventListener('input', function() {
        const engravingPreview = document.getElementById('engravePreview');
        engravingPreview.textContent = this.value.trim();
    });

    // Add hover listeners for finish options
    document.querySelectorAll('.finish-options label').forEach(label => {
        const radio = label.querySelector('input[type="radio"]');
        if (radio) {
            label.addEventListener('mouseover', () => previewFinish(radio.value)); // Preview specific finish
            label.addEventListener('mouseout', endOptionPreview); // Restore to selected state
        }
    });

    // Add hover listeners for size options
    document.querySelectorAll('.size-options label').forEach(label => {
        const radio = label.querySelector('input[type="radio"]');
        if (radio) {
            label.addEventListener('mouseover', previewSize); // Preview default image
            label.addEventListener('mouseout', endOptionPreview); // Restore to selected state
        }
    });

    // Close modals when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            closeModals();
        }
    });

    // Billing modal event listeners
    const cancelBillingBtn = document.getElementById('cancelBilling');
    // The listener for this button is now set dynamically in openBillingModal
    // to handle different "back" actions (e.g., back to cart vs. back to customization).
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    if (placeOrderBtn) placeOrderBtn.addEventListener('click', handlePlaceOrder);


    submodulesBtn.addEventListener('click', () => {
        submodulesModal.style.display = 'block';
    });

    closeSubmodules.addEventListener('click', () => {
        submodulesModal.style.display = 'none';
    });

    closeOrderStatus.addEventListener('click', () => {
        orderStatusModal.style.display = 'none';
    });

    viewOrderStatusBtn.addEventListener('click', () => {
        submodulesModal.style.display = 'none'; // close modules modal
        fetchOrders();
        orderStatusModal.style.display = 'block';
    });

    // Optional: click outside modal to close
    window.addEventListener('click', (e) => {
        if(e.target == submodulesModal){
            submodulesModal.style.display = 'none';
        }
    });

    
    // Filter button click
    $("#applyFilters").on("click", function() {
        console.log('here')
        const filters = {
            startDate: $("#filterStartDate").val(),
            endDate: $("#filterEndDate").val(),
            status: $("#filterStatus").val()
        };
        fetchOrders(filters);
    });


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


    // Open receipt in new tab
    $(document).on("click", ".view-receipt-btn", function() {
        const orderId = $(this).data("id");
        window.open(`../../customer/receipt.php?orderId=${orderId}`, "_blank");
    });


    $("#notificationBtn").on("click", function() {
        $("#notificationModal").fadeIn(200);
        $("#notificationList").html('<p class="loading-text">Loading notifications...</p>');

        $.ajax({
        url: "../assets/php/fetch_notification.php",
        type: "POST",
        dataType: "json",
        success: function(response) {
            console.log(response)
            if (response.status === "success" && response.notifications.length > 0) {
            const notifHTML = response.notifications.map(notif => `
                <div class="notification-item">
                <i class="${notif.icon}"></i>
                <div class="notification-text">
                    <div class="notification-title">${notif.title}</div>
                    <div class="notification-message">${notif.message}</div>
                    <div class="notification-date">${notif.created_at}</div>
                </div>
                </div>
            `).join('');
            $("#notificationList").html(notifHTML);
            } else {
            $("#notificationList").html('<p>No notifications found.</p>');
            }
        },
        error: function(err) {
            console.error("Error fetching notifications:", err);
            $("#notificationList").html('<p class="text-danger">Failed to load notifications.</p>');
        }
        });
    });

    // Close modal
    $("#closeNotificationModal").on("click", function() {
        $("#notificationModal").fadeOut(200);
    });

    $(window).on("click", function(e) {
        if (e.target.id === "notificationModal") {
        $("#notificationModal").fadeOut(200);
        }
    });


    // Handle Add New Address
    $(".add-address-btn").off("click").on("click", function () {
        const formContainer = $("#newAddressFormContainer");
        
        // If already open, toggle visibility
        if (formContainer.is(":visible")) {
            formContainer.hide();
            return;
        }

        formContainer.html(`
            <div class="address-form" style="border:1px solid #ddd; padding:1rem; border-radius:8px;">
                <h4 style="margin-bottom:0.5rem;">New Address</h4>
                <div class="form-group">
                    <input type="text" id="house_no" placeholder="House No. / Street" required>
                </div>
                <div class="form-group">
                    <input type="text" id="barangay" placeholder="Barangay" required>
                </div>
                <div class="form-group">
                    <input type="text" id="city" placeholder="City" required>
                </div>
                <div class="form-group">
                    <input type="text" id="province" placeholder="Province" required>
                </div>
                <div class="form-group">
                    <input type="text" id="mobile_number" placeholder="Mobile Number" required maxlength="11">
                </div>
                <div style="margin-top:1rem; display:flex; gap:0.5rem;">
                    <button class="btn-secondary" id="cancelAddAddress">Cancel</button>
                    <button class="btn-primary" id="saveNewAddress">Save Address</button>
                </div>
            </div>
        `);

        formContainer.slideDown(200);

        // Cancel button
        $("#cancelAddAddress").on("click", function () {
            formContainer.slideUp(200);
        });

        // Save button
        $("#saveNewAddress").on("click", function () {
            const house_no = $("#house_no").val().trim();
            const barangay = $("#barangay").val().trim();
            const city = $("#city").val().trim();
            const province = $("#province").val().trim();
            const mobile_number = $("#mobile_number").val().trim();

            if (!house_no || !barangay || !city || !province || !mobile_number) {
                alert("Please fill out all fields.");
                return;
            }

            const data = { house_no, barangay, city, province, mobile_number };
            console.log(data);

            $.ajax({
                url: "../assets/php/add_user_address.php",
                type: "POST",
                data: data,
                dataType: "json",
                success: function (res) {
                    console.log(res);

                    if (res.status === "success") {
                        alert("Address added successfully!");
                        formContainer.slideUp(200);

                        // ✅ Option 2: Just append the newest address
                        const newAddr = res.data; // assuming PHP returns { status: 'success', data: { id, full_name, house_no, barangay, city, province, mobile_number } }

                        const newAddressHtml = `
                            <div class="address-card ${newAddr.is_default ? 'selected' : ''}">
                                <div class="address-card-header">
                                    <strong>${newAddr.full_name}</strong>
                                    <button class="remove-address-btn" data-id="${newAddr.id}" title="Remove Address">Remove</button>
                                </div>
                                <div class="address-line">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>${newAddr.barangay}, ${newAddr.city}, ${newAddr.province}<br>${newAddr.house_no}</span>
                                </div>
                                <div class="address-line">
                                    <i class="fas fa-phone-alt"></i>
                                    <span>${newAddr.mobile_number}</span>
                                </div>
                            </div>
                        `;

                        // ✅ Append before "Add New Address" button
                        $(".billing-form-container .add-address-btn").before(newAddressHtml);

                        // ✅ Optionally clear the input fields
                        $("#house_no, #barangay, #city, #province, #mobile_number").val("");
                    } else {
                        alert(res.message || "Failed to add address.");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    alert("Error saving address. Please try again.");
                }
            });
        });

    });

    const addrContainer = $('.billing-form-container');

    // 1) click on address-card -> select it
    addrContainer.off('click', '.address-card').on('click', '.address-card', function (e) {
        // If the click originated from the remove button, ignore selection here
        if ($(e.target).closest('.remove-address-btn').length) return;

        // remove selected class from siblings and add to clicked
        addrContainer.find('.address-card').removeClass('selected');
        $(this).addClass('selected');

        // store selected id
        const selectedId = $(this).data('id');
        sessionStorage.setItem('selectedAddressID', selectedId);
        // console.log('Selected Address ID:', selectedId);

        // OPTIONAL: set as default in DB (uncomment if you want server-side default change)
        /*
        $.post('../assets/php/set_default_address.php', { id: selectedId }, function(res) {
        if (res.status === 'success') {
            console.log('Default address updated on server.');
            // refresh list to reflect is_default flags if needed:
            // fetchAndRenderAddresses();
        } else {
            console.warn('Failed to set default:', res.message);
        }
        }, 'json');
        */
    });

    addrContainer.off('click', '.remove-address-btn').on('click', '.remove-address-btn', function (e) {
        e.stopPropagation(); // prevent bubbling to the .address-card click handler

        const id = $(this).data('id');
        if (!id) return;

        if (!confirm('Remove this address?')) return;

        $.ajax({
        url: '../assets/php/delete_user_address.php',
        type: 'POST',
        data: { id },
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
            // remove the card from DOM
            $(`.address-card[data-id="${id}"]`).remove();

            // If the removed address was selected, clear selection or select first available
            const selectedId = sessionStorage.getItem('selectedAddressID');
            if (String(selectedId) === String(id)) {
                sessionStorage.removeItem('selectedAddressID');
                // try to select first available dynamic or default card
                const next = $('.billing-form-container').find('.address-card').first();
                if (next.length) {
                next.addClass('selected');
                sessionStorage.setItem('selectedAddressID', next.data('id'));
                }
            }
            } else {
            alert(res.message || 'Failed to remove address.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error deleting address:', error);
            alert('Error removing address. Try again.');
        }
        });
    });
}

function updateActiveCategoryUI() {
    // Remove active from all buttons first
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Add active to the correct buttons that match the selectedCategory
    document.querySelectorAll(`.category-btn[data-category="${selectedCategory}"]`).forEach(btn => {
        btn.classList.add('active');
    });
}

// Category filtering
function handleCategoryChange(e) {
    e.preventDefault();
    const category = e.target.dataset.category;
    if (!category) return;

    selectedCategory = category;
    updateActiveCategoryUI();

    // renderProducts();
}

// Search functionality
function handleSearch(e) {
    searchQuery = e.target.value.toLowerCase();
    renderProducts();
}

// Filter products
function getFilteredProducts() {
    return PRODUCTS.filter(product => {
        const matchesCategory = selectedCategory === 'all' || product.category === selectedCategory;
        const matchesSearch = product.name.toLowerCase().includes(searchQuery) || 
                            product.description.toLowerCase().includes(searchQuery);
        return matchesCategory && matchesSearch;
    });
}

// function renderProducts() {
//     $.ajax({
//         url: "../assets/php/fetch_products.php",
//         type: "POST", // or "GET" if you prefer
//         dataType: "json",
//         success: function (response) {
//             console.log(response)
//             PRODUCTS = response.data
//             if (response.status === "success") {
//                 const products = response.data;

//                 if (!products || products.length === 0) {
//                     productsGrid.innerHTML = '<div class="no-products">No products found matching your criteria.</div>';
//                     return;
//                 }

//                 productsGrid.innerHTML = products.map(product => `
//                     <div class="product-card" data-id="${product.id}" data-productCode="${product.product_code}">
//                         <div class="product-image">
//                             <img src="${product.image}" alt="${product.name}" onclick="openCustomization(${product.id})">
//                             <div class="product-actions">
//                                 <button class="action-btn cart-btn" onclick="openCustomization(${product.id})" title="Customize">
//                                     <i class="fas fa-shopping-cart"></i>
//                                 </button>
//                                 <button class="action-btn review-btn ${wishlistItems.includes(product.id) ? 'active' : ''}" 
//                                         onclick="toggleWishlist(event, ${product.id})" title="Add to Wishlist">
//                                     <i class="fas fa-heart"></i>
//                                 </button>
//                             </div>
//                         </div>
//                         <div class="product-info">
//                             <h3>${product.name}</h3>
//                             <p class="price">₱${parseFloat(product.price).toFixed(2)}</p>
//                             <div class="product-buttons">
//                                 <button class="btn-add-cart" onclick="openCustomization(${product.id})">Add to Cart</button>
//                             </div>
//                         </div>
//                     </div>
//                 `).join('');
//             } else {
//                 productsGrid.innerHTML = `<div class="no-products">Error: ${response.message}</div>`;
//             }
//         },
//         error: function () {
//             productsGrid.innerHTML = '<div class="no-products">Something went wrong. Please try again.</div>';
//         }
//     });
// }

let currentPage = 1;
const itemsPerPage = 12; // adjust as needed

function renderPagination(products) {
  const totalPages = Math.ceil(products.length / itemsPerPage);
  const paginationContainer = document.querySelector('.pagination');
  paginationContainer.innerHTML = '';

  // Previous button
  const prevBtn = document.createElement('button');
  prevBtn.className = 'page-btn prev';
  prevBtn.textContent = '<';
  prevBtn.disabled = currentPage === 1;
  prevBtn.addEventListener('click', () => changePage(currentPage - 1));
  paginationContainer.appendChild(prevBtn);

  // Page numbers
  for (let i = 1; i <= totalPages; i++) {
    const pageBtn = document.createElement('button');
    pageBtn.className = 'page-btn';
    if (i === currentPage) pageBtn.classList.add('active');
    pageBtn.textContent = i;
    pageBtn.dataset.page = i;
    pageBtn.addEventListener('click', () => changePage(i));
    paginationContainer.appendChild(pageBtn);
  }

  // Next button
  const nextBtn = document.createElement('button');
  nextBtn.className = 'page-btn next';
  nextBtn.textContent = '>';
  nextBtn.disabled = currentPage === totalPages;
  nextBtn.addEventListener('click', () => changePage(currentPage + 1));
  paginationContainer.appendChild(nextBtn);
}

function changePage(page) {
  currentPage = page;
  paginateAndRender(PRODUCTS);
}

function paginateAndRender(products) {
  const start = (currentPage - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  const paginatedProducts = products.slice(start, end);

  renderProducts(paginatedProducts);
  renderPagination(products);
}

function renderProducts(products = PRODUCTS) {
  const productsGrid = document.querySelector("#productsGrid");
  if (!products || products.length === 0) {
    productsGrid.innerHTML = '<div class="no-products">No products found matching your criteria.</div>';
    return;
  }

  productsGrid.innerHTML = products.map(product => `
    <div class="product-card" data-id="${product.id}" data-productCode="${product.product_code}">
        <div class="product-image">
            <img src="${product.image}" alt="${product.name}" onclick="openCustomization(${product.id})">
            <div class="product-actions">
                <button class="action-btn cart-btn" onclick="openCustomization(${product.id})" title="Customize">
                    <i class="fas fa-shopping-cart"></i>
                </button>
                <button class="action-btn review-btn ${wishlistItems.includes(product.id) ? 'active' : ''}" 
                        onclick="toggleWishlist(event, ${product.id})" title="Add to Wishlist">
                    <i class="fas fa-heart"></i>
                </button>
            </div>
        </div>
        <div class="product-info">
            <h3>${product.name}</h3>
            <p class="price">₱${parseFloat(product.price).toFixed(2)}</p>
            <div class="product-buttons">
                <button class="btn-add-cart" onclick="openCustomization(${product.id})">Add to Cart</button>
            </div>
        </div>
    </div>
  `).join('');
}



function fetchProducts() {
    $.ajax({
        url: "../assets/php/fetch_products.php",
        type: "POST",
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                PRODUCTS = response.data;
                console.log(PRODUCTS)
                renderProducts(PRODUCTS);
                paginateAndRender(PRODUCTS);

            } else {
                productsGrid.innerHTML = `<div class="no-products">Error: ${response.message}</div>`;
            }
        },
        error: function () {
            productsGrid.innerHTML = '<div class="no-products">Something went wrong. Please try again.</div>';
        }
    });
}

// --- Filter buttons logic ---
document.querySelectorAll(".category-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        const category = btn.getAttribute("data-category");

        if (category === "all") {
            renderProducts(PRODUCTS);
        } else {
            const filtered = PRODUCTS.filter(p => p.category.toLowerCase() === category.toLowerCase());
            console.log(filtered)
            renderProducts(filtered);
        }

        // optional: highlight active button
        document.querySelectorAll(".category-btn").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
    });
});



// Cart functionality
function addToCart(productId, customization = null) {
    const product = PRODUCTS.find(p => p.id === productId);
    if (!product) return;

    const existingItem = cartItems.find(item => 
        item.id === productId && 
        JSON.stringify(item.customization) === JSON.stringify(customization)
    );

    if (existingItem) {
        existingItem.quantity += customization?.quantity || 1;
    } else {
        cartItems.unshift({ // Add to the beginning of the array
            id: productId,
            quantity: customization?.quantity || 1,
            customization: customization,
            selected: true // Default to selected when added
        });
    }

    updateCounts();
    saveState();
    showSuccessNotification({
        product: product,
        customization: customization,
        quantity: customization?.quantity || 1
    });
}

function removeFromCart(productId, customizationIndex = 0) {
    cartItems = cartItems.filter((item, index) => 
        !(item.id === productId && index === customizationIndex)
    );
    updateCounts();
    saveState();
    renderCartItems();
}

function updateCartQuantity(productId, quantity, customizationIndex = 0) {
    // Prevent quantity from going below 1.
    if (quantity < 1) {
        // If the new quantity would be less than 1, do nothing.
        // The button should be disabled in the UI, but this is a safeguard.
        return;
    }
    
    const itemIndex = cartItems.findIndex((item, index) => 
        item.id === productId && index === customizationIndex
    );
    
    if (itemIndex !== -1) {
        cartItems[itemIndex].quantity = quantity;
        updateCounts();
        saveState();
        renderCartItems();
    }
}

function toggleCartItemSelection(index) {
    if (cartItems[index]) {
        cartItems[index].selected = !cartItems[index].selected;
        saveState();
        renderCartItems();
    }
}

function toggleSelectAllCartItems() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const isChecked = selectAllCheckbox.checked;
    cartItems.forEach(item => item.selected = isChecked);
    saveState();
    renderCartItems();
}

// Wishlist functionality
function toggleWishlist(event, productId) {
    const product = PRODUCTS.find(p => p.id === productId);
    if (!product) return;

    const index = wishlistItems.indexOf(productId);
    if (index > -1) {
        wishlistItems.splice(index, 1);
        // No animation on removal for simplicity, the UI will just update.
    } else {
        wishlistItems.push(productId);

        // Animate when adding. This only happens from the product grid.
        const card = event.target.closest('.product-card');
        if (card) {
            const img = card.querySelector('.product-image img');
            if (img) {
                animateToWishlist(img);
            }
        }
    }
    updateCounts();
    saveState();
    renderProducts(); // This updates the heart icons on the main page
    
    // If the wishlist modal is open, re-render it too
    if (document.getElementById('wishlistModal').style.display === 'block') {
        renderWishlistItems();
    }
}

// Customization functionality
function openCustomization(productId) {
    console.log(productId)
    const product = PRODUCTS.find(p => p.id === productId);
    if (!product) return;
    
    currentCustomizingProduct = product;
    document.getElementById('customizationTitle').textContent = `Customize ${product.name}`;
    document.getElementById('customizationName').textContent = product.name;
    document.getElementById('customizationDescription').textContent = product.description;

    // --- Apply dynamic engraving position based on database styling ---
    const previewEl = document.getElementById('engravePreview');

    // Safety: reset first
    previewEl.style.top = '50%';
    previewEl.style.left = '50%';
    previewEl.style.transform = 'translate(-50%, -50%)';

    // Apply product styling if available
    if (product.styling) {
        const style = product.styling;
        if (style.top) previewEl.style.top = style.top;
        if (style.left) previewEl.style.left = style.left;
        if (style.transform) previewEl.style.transform = style.transform;
    }
    
    // Populate thumbnails
    const thumbnailGallery = document.getElementById('thumbnailGallery');
    thumbnailGallery.innerHTML = ''; // Clear previous thumbnails

    if (product.images && Object.keys(product.images).length > 0) {
        Object.entries(product.images).forEach(([key, src]) => {
            const thumb = document.createElement('img');
            thumb.src = src;
            thumb.alt = `${product.name} - ${key}`;
            thumb.dataset.key = key;
            if (key === 'default') {
                thumb.classList.add('active');
            }
            thumb.onclick = () => setActiveThumbnail(thumb);
            // Add hover events for a preview effect
            thumb.onmouseover = () => previewThumbnail(thumb);
            thumb.onmouseout = () => endPreviewThumbnail();
            thumbnailGallery.appendChild(thumb);
        });
    }

    // Reset form
    document.querySelectorAll('input[name="size"], input[name="finish"]').forEach(radio => radio.checked = false);
    document.getElementById('quantity').textContent = '1';

    // Display stock and handle button state
    const stockEl = document.getElementById('productStock');
    const increaseQtyBtn = document.getElementById('increaseQty');
    const decreaseQtyBtn = document.getElementById('decreaseQty');
    if (stockEl) {
        if (product.stock > 0) {
            stockEl.textContent = `Stock: ${product.stock}`;
            stockEl.style.color = '#6c757d';
            if (increaseQtyBtn) increaseQtyBtn.disabled = (product.stock <= 1);
            if (decreaseQtyBtn) decreaseQtyBtn.disabled = true; // Starts at 1
        } else {
            stockEl.textContent = 'Out of Stock';
            stockEl.style.color = '#dc3545'; // Red color for out of stock
            if (increaseQtyBtn) increaseQtyBtn.disabled = true;
            if (decreaseQtyBtn) decreaseQtyBtn.disabled = true;
        }
    }

    // Set the initial image to the main product image (not a variant)
    console.log(product.image)
    document.getElementById('customizationImage').src = `/customer/${product.image}`;
    document.getElementById('customizationImageEngrave').src = `/customer/${product.image}`;


    // Call updateCustomizationView to set initial price and button states.
    updateCustomizationView(false); // Pass false so it doesn't try to change the image yet

    customizationModal.style.display = 'block';

    // Shake the warning if it's visible on open to grab user's attention
    const warningEl = document.getElementById('customizationWarning');
    if (warningEl && warningEl.style.display !== 'none') {
        // Add the class to trigger the animation
        warningEl.classList.add('is-shaking');
        // Remove the class after the animation completes
        setTimeout(() => {
            warningEl.classList.remove('is-shaking');
        }, 820); // Match animation duration in CSS
    }
}

function updateQuantity(change) {
    const quantityEl = document.getElementById('quantity');
    let quantity = parseInt(quantityEl.value) + change;

    // ✅ Check against stock
    const stock = currentCustomizingProduct.stock || 999;
    quantity = Math.min(quantity, stock);
    quantity = Math.max(1, quantity);

    quantityEl.value = quantity;

    // ✅ Update button states based on new quantity and stock
    const increaseQtyBtn = document.getElementById('increaseQty');
    const decreaseQtyBtn = document.getElementById('decreaseQty');
    if (increaseQtyBtn) increaseQtyBtn.disabled = (quantity >= stock);
    if (decreaseQtyBtn) decreaseQtyBtn.disabled = (quantity <= 1);

    updateCustomizationView(false); // Only update price, not image
}

function getCustomizationExtras() {
  const engravingText = document.querySelector('input[name="engravingText"]')?.value.trim() || '';
  const giftPackaging = document.querySelector('select[name="giftPackaging"]')?.value || 'none';
  const messageCard = document.querySelector('textarea[name="messageCard"]')?.value.trim() || '';
  const specialHandling = document.querySelector('select[name="specialHandling"]')?.value || 'none';
  const deliveryPreference = document.querySelector('select[name="deliveryPreference"]')?.value || 'standard';

  // --- Compute extra cost ---
  let extraCost = 0;

  // Engraving
  if (engravingText) extraCost += 50;

  // Gift Packaging
  if (giftPackaging === 'basic') extraCost += 50;
  else if (giftPackaging === 'premium') extraCost += 100;

  // Message Card
  if (messageCard) extraCost += 20;

  // Special Handling
  if (specialHandling === 'fragile') extraCost += 10;
  else if (specialHandling === 'eco') extraCost += 15;

  // Delivery Preference
  if (deliveryPreference === 'express') extraCost += 100;
  else if (deliveryPreference === 'scheduled') extraCost += 50;

  // --- Build human-readable attributes string ---
  const attributes = [
    engravingText && `Engraving: "${engravingText}"`,
    giftPackaging !== 'none' && `Packaging: ${giftPackaging}`,
    messageCard && `Card: "${messageCard}"`,
    specialHandling !== 'none' && `Handling: ${specialHandling}`,
    deliveryPreference && `Delivery: ${deliveryPreference}`,
  ]
    .filter(Boolean)
    .join(' | ');

  return {
    extraCost,
    engravingText,
    giftPackaging,
    messageCard,
    specialHandling,
    deliveryPreference,
    attributes,
  };
}

function updateCustomizationView(updateImage = true) {
    if (!currentCustomizingProduct) return;

    // --- Base price setup ---
    let price = currentCustomizingProduct.price;
    const sizeInput = document.querySelector('input[name="size"]:checked');
    const finishInput = document.querySelector('input[name="finish"]:checked');
    
    const size = sizeInput ? sizeInput.value : null;
    const finish = finishInput ? finishInput.value : null;

    const quantity = parseInt(document.getElementById('quantity').value);

    // --- Price adjustment for size ---
    if (size === 'large') price *= 1.1; // +10%
    if (size === 'small') price *= 0.8; // -20%

    // --- Price adjustment for finish ---
    if (finish === 'dark' || finish === 'premium') price += 40;
    

    let finalPrice = price * quantity;

    // --- NEW: get all customization extras ---
    const {
        extraCost,
        engravingText,
        giftPackaging,
        messageCard,
        specialHandling,
        deliveryPreference,
        attributes
    } = getCustomizationExtras();

    
    // --- 🆕 Update engraving text preview ---
    const engravingInput = document.getElementById('engravingText');
    if (engravingInput) {
        const engravingPreview = document.getElementById('engravePreview');
        engravingPreview.textContent = engravingInput.value.trim() || '';
    }

    finalPrice += extraCost;

    // --- Update price display ---
    document.getElementById('estimatedPrice').textContent = `₱${finalPrice.toFixed(2)}`;

    // --- Update attributes summary (if exists) ---
    const attrEl = document.getElementById('attributes-summary');
    if (attrEl) {
        attrEl.textContent = attributes || 'No customizations';
    }

    // --- Optional image update ---
    if (updateImage && finish) {
        const imageKey = finish;
        if (currentCustomizingProduct.images && currentCustomizingProduct.images[imageKey]) {
            const newImageSrc = currentCustomizingProduct.images[imageKey];
            console.log('here')
            document.getElementById('customizationImage').src = newImageSrc;

            const thumbnailToSelect = document.querySelector(`#thumbnailGallery img[src="${newImageSrc}"]`);
            if (thumbnailToSelect) {
                document.querySelectorAll('#thumbnailGallery img').forEach(img => img.classList.remove('active'));
                thumbnailToSelect.classList.add('active');
            }
        }
    }

    // --- Enable/disable buttons based on selection ---
    const addToCartBtn = document.getElementById('addCustomizedToCart');
    const buyNowBtn = document.getElementById('buyNowBtn');
    const warningEl = document.getElementById('customizationWarning');
    const areOptionsSelected = size && finish;
    const isOutOfStock = currentCustomizingProduct.stock <= 0;

    addToCartBtn.disabled = !areOptionsSelected || isOutOfStock;
    if (buyNowBtn) buyNowBtn.disabled = !areOptionsSelected || isOutOfStock;

    // --- Show warning message if needed ---
    if (warningEl) {
        if (isOutOfStock) {
            warningEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> This item is currently out of stock.';
            warningEl.style.display = 'flex';
        } else if (areOptionsSelected) {
            warningEl.style.display = 'none';
        } else {
            warningEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Please select a size and finish to proceed.';
            warningEl.style.display = 'flex';
        }
    }

    // --- Update tooltip text for disabled buttons ---
    let tooltip = "Please select a size and finish to proceed.";
    if (isOutOfStock) tooltip = "This item is out of stock.";

    addToCartBtn.title = (!areOptionsSelected || isOutOfStock) ? tooltip : '';
    if (buyNowBtn) buyNowBtn.title = (!areOptionsSelected || isOutOfStock) ? tooltip : '';
}


function setActiveThumbnail(thumbnailElement) {
    // --- Highlight the selected thumbnail ---
    document.querySelectorAll('#thumbnailGallery img').forEach(img => img.classList.remove('active'));
    thumbnailElement.classList.add('active');

    // --- Sync the finish radio button with the selected thumbnail ---
    const finishKey = thumbnailElement.dataset.key; // 'default', 'natural', 'dark', 'premium'
    const finishValueToSelect = (finishKey === 'default') ? 'natural' : finishKey;

    const finishRadio = document.querySelector(`input[name="finish"][value="${finishValueToSelect}"]`);
    if (finishRadio) finishRadio.checked = true;

    // --- Update main preview image & product state ---
    updateCustomizationView(true);

    // --- 🪄 Apply dynamic engraving text positioning ---
    const product = currentCustomizingProduct;
    const previewEl = document.getElementById('engravePreview');

    if (!previewEl) return; // guard clause

    // Reset to defaults first
    previewEl.style.top = '50%';
    previewEl.style.left = '50%';
    previewEl.style.transform = 'translate(-50%, -50%)';

    // Check if styling exists
    if (product.styling) {
        let styleData = product.styling;

        // If styling is per finish type (object of objects)
        if (styleData[finishKey]) {
            styleData = styleData[finishKey];
        }

        // Apply the styles safely
        if (styleData.top) previewEl.style.top = styleData.top;
        if (styleData.left) previewEl.style.left = styleData.left;
        if (styleData.transform) previewEl.style.transform = styleData.transform;
    }
}


function addCustomizedToCart() {
    if (!currentCustomizingProduct) return;

    const product = currentCustomizingProduct;

    const selectedSizeInput = document.querySelector('input[name="size"]:checked');
    const selectedFinishInput = document.querySelector('input[name="finish"]:checked');

    // Guard clause to prevent missing selections
    if (!selectedSizeInput || !selectedFinishInput) {
        console.error("Add to cart button was clicked without selections.");
        return;
    }

    // --- Get readable size label ---
    let sizeLabel = '';
    const sizeLabelElement = selectedSizeInput.closest('label');
    if (sizeLabelElement) {
        const radioLabelSpan = sizeLabelElement.querySelector('.radio-label');
        if (radioLabelSpan) sizeLabel = radioLabelSpan.textContent.trim();
    }
    if (!sizeLabel) sizeLabel = selectedSizeInput.value;

    // --- Quantity ---
    const quantityToAdd = parseInt(document.getElementById('quantity').value || 1);

    // --- Fetch universal customizations ---
    const { engravingText, giftPackaging, messageCard, specialHandling, deliveryPreference, extraCost, attributes: extraAttributes } = getCustomizationExtras();

    // --- Base price computation ---
    let basePrice = product.price;

    // Adjust for size
    if (selectedSizeInput.value === 'large') basePrice *= 1.1;
    if (selectedSizeInput.value === 'small') basePrice *= 0.8;

    // Adjust for finish
    if (['dark', 'premium'].includes(selectedFinishInput.value)) basePrice += 40;

    // Add extra costs
    basePrice += extraCost;

    const finalPrice = basePrice * quantityToAdd;

    // Determine the image currently shown in the customization preview
    const currentImage = document.getElementById('customizationImage')?.src || product.image;

    // Default engraving position (you can tweak this later dynamically if needed)
    const engravingPosition = { bottom: '10px', left: '50%' };

    const customizationDetails = {
        size: selectedSizeInput.value,
        sizeLabel,
        finish: selectedFinishInput.value,
        engraving: engravingText || null,
        engravingPosition,
        imageWithFinish: currentImage, // ✅ store the preview image
        giftPackaging,
        messageCard: messageCard || null,
        specialHandling,
        deliveryPreference,
        extraCost
    };

    // --- Merge base attributes + extras ---
    const attributes = [
        `Size: ${sizeLabel}`,
        `Finish: ${selectedFinishInput.value}`,
        extraAttributes
    ].filter(Boolean).join(' | ');
    console.log(attributes)

    // --- Check if item already exists in cart (same customizations) ---
    const existingItem = cartItems.find(item => {
        if (item.id !== product.id || !item.customization) return false;
        return JSON.stringify(item.customization) === JSON.stringify(customizationDetails);
    });

    if (existingItem) {
        existingItem.quantity += quantityToAdd;
    } else {
        cartItems.unshift({
            id: product.id,
            name: product.name,
            quantity: quantityToAdd,
            price: finalPrice, // ✅ Include price with extras
            customization: customizationDetails,
            attributes,
            selected: true
        });
    }

    updateCounts();
    saveState();
    closeModals();

    showSuccessNotification({
        product,
        customization: customizationDetails,
        quantity: quantityToAdd
    });
}


function buyNow() {
    if (!currentCustomizingProduct) return;

    const selectedSizeInput = document.querySelector('input[name="size"]:checked');
    const selectedFinishInput = document.querySelector('input[name="finish"]:checked');

    if (!selectedSizeInput || !selectedFinishInput) {
        // This alert will now be shown if the user somehow bypasses the disabled button
        console.error("Buy Now button was clicked without selections.");
        return;
    }

    // Robustly find the size label to prevent script crashes.
    let sizeLabel = '';
    const sizeLabelElement = selectedSizeInput.closest('label');
    if (sizeLabelElement) {
        const radioLabelSpan = sizeLabelElement.querySelector('.radio-label');
        if (radioLabelSpan) {
            sizeLabel = radioLabelSpan.textContent;
        }
    }
    if (!sizeLabel) {
        sizeLabel = selectedSizeInput.value;
    }

    const customizationDetails = {
        size: selectedSizeInput.value,
        sizeLabel: sizeLabel, // Use the safe, retrieved label
        finish: selectedFinishInput.value,
    };
    
    const quantity = parseInt(document.getElementById('quantity').textContent);

    const itemToBuy = { 
        id: currentCustomizingProduct.id,
        quantity: quantity,
        customization: customizationDetails
    };

    closeModals();
    openBillingModal([itemToBuy], 'customization'); // Open billing modal directly, noting the source
}

// Modal functionality
function openModal(type) {
    if (type === 'cart') {
        renderCartItems();
        cartModal.style.display = 'block';
    } else if (type === 'wishlist') {
        renderWishlistItems();
        wishlistModal.style.display = 'block';
    }
}

function closeModals() {
    cartModal.style.display = 'none';
    wishlistModal.style.display = 'none';
    customizationModal.style.display = 'none';
    billingModal.style.display = 'none';
}

function renderCartItems() {
    const cartItemsEl = document.getElementById('cartItems');
    const cartFooterEl = document.getElementById('cartFooter');

    if (cartItems.length === 0) {
        cartItemsEl.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty</p>
                <button class="btn-primary" onclick="closeModals()">Continue Shopping</button>
            </div>
        `;
        cartFooterEl.innerHTML = '';
        return;
    }

    const allSelected = cartItems.length > 0 && cartItems.every(item => item.selected);

    let cartHTML = `
        <div class="cart-select-all">
            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAllCartItems()" ${allSelected ? 'checked' : ''}>
            <label for="selectAllCheckbox">Select All</label>
        </div>
    `;

    cartHTML += cartItems.map((item, index) => {
        const product = PRODUCTS.find(p => p.id === item.id);
        if (!product) return '';

        let breakdown = [];
        let itemPrice = product.price;
        let basePrice = product.price;

        breakdown.push(`<div><strong>Base Price:</strong> ₱${basePrice.toFixed(2)}</div>`);

        if (item.customization) {
            // Size-based price adjustments
            if (item.customization.size === 'large') {
                const added = basePrice * 0.10;
                itemPrice += added;
                breakdown.push(`<div>Size (Large +10%): ₱${added.toFixed(2)}</div>`);
            }
            if (item.customization.size === 'small') {
                const reduced = basePrice * 0.20;
                itemPrice -= reduced;
                breakdown.push(`<div>Size (Small -20%): -₱${reduced.toFixed(2)}</div>`);
            }

            // Finish-based adjustments
            if (item.customization.finish === 'dark' || item.customization.finish === 'premium') {
                itemPrice += 40;
                breakdown.push(`<div>Finish (${item.customization.finish}): +₱40.00</div>`);
            }

            // Engraving or other extra costs
            if (item.customization.extraCost) {
                breakdown.push(`<div>Engraving/Extra: +₱${item.customization.extraCost.toFixed(2)}</div>`);
                itemPrice += item.customization.extraCost;
            }
        }

        const totalItemPrice = itemPrice * item.quantity;

        // Generate attribute text
        const attrText = item.attributes
            ? `<div class="customization-details"><small>${item.attributes}</small></div>`
            : '';

        // Generate breakdown HTML
        const breakdownHTML = `
            <div class="price-breakdown" style="font-size:13px; color:#555; margin-top:5px;">
                ${breakdown.join('')}
                <div><strong>Subtotal:</strong> ₱${itemPrice.toFixed(2)} × ${item.quantity}</div>
                <div><strong>Total:</strong> ₱${totalItemPrice.toFixed(2)}</div>
            </div>
        `;

        return `
            <div class="cart-item">
                <input type="checkbox" class="cart-item-checkbox" onchange="toggleCartItemSelection(${index})" ${item.selected ? 'checked' : ''}>
                <img src="${product.image}" alt="${product.name}">
                
                <div class="item-details">
                    <h4>${product.name}</h4>
                    ${attrText}
                    ${breakdownHTML}
                </div>

                <div class="quantity-controls">
                    <button ${item.quantity === 1 ? 'disabled' : ''} onclick="updateCartQuantity(${item.id}, ${item.quantity - 1}, ${index})">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="updateCartQuantity(${item.id}, ${item.quantity + 1}, ${index})">+</button>
                </div>

                <button class="remove-btn" onclick="removeFromCart(${item.id}, ${index})">
                    <i class="fas fa-trash"></i>
                </button>

                <button class="btn-view-engraving" 
                    onclick="viewEngraving('${product.image}', '${encodeURIComponent(JSON.stringify(product.styling))}', '${encodeURIComponent(item.customization?.engraving || '')}')">
                    <i class="fas fa-eye"></i> View Engraving
                </button>
            </div>
        `;
    }).join('');

    cartItemsEl.innerHTML = cartHTML;

    const total = getCartTotal();
    cartFooterEl.innerHTML = `
        <div class="cart-total">
            <span>Total: ₱${total.toFixed(2)}</span>
        </div>
        <div class="cart-actions">
            <button class="btn-secondary" onclick="closeModals()">Continue Shopping</button>
            <button class="btn-primary" onclick="handleCheckout()">Checkout</button>
        </div>
    `;
}


function viewEngraving(image, stylingJSON, engravingText) {
    console.log(stylingJSON)
    const container = document.getElementById('engravePreviewContainer');
    const decodedText = decodeURIComponent(engravingText);
    const style = JSON.parse(decodeURIComponent(stylingJSON || '{}'));

    container.innerHTML = `
        <div id="engravePreview" style="position:relative; display:inline-block;">
            <img src="${image}" alt="Preview" style="width:100%; border-radius:8px;">
            <div class="engraved-text"
                style="
                    position:absolute;
                    top:${style.top || '50%'};
                    left:${style.left || '50%'};
                    transform:${style.transform || 'translate(-50%, -50%)'};
                    font-family:'Times New Roman', serif;
                    font-size:1.2rem;
                    font-weight:bold;
                    color:#222;
                    text-shadow:1px 1px 2px rgba(0,0,0,0.3);
                ">
                ${decodedText || '(No engraving)'}
            </div>
        </div>
    `;

    document.getElementById('engravingModal').style.display = 'block';
}


function closeEngravingModal() {
  document.getElementById('engravingModal').style.display = 'none';
}



function renderWishlistItems() {
    const wishlistItemsEl = document.getElementById('wishlistItems');

    if (wishlistItems.length === 0) {
        wishlistItemsEl.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-heart"></i>
                <p>Your wishlist is empty</p>
                <button class="btn-primary" onclick="closeModals()">Browse Products</button>
            </div>
        `;
        return;
    }

    wishlistItemsEl.innerHTML = `
        <div class="wishlist-grid">
            ${wishlistItems.map(productId => {
                const product = PRODUCTS.find(p => p.id === productId);
                if (!product) return '';

                return `
                    <div class="wishlist-item">
                        <img src="${product.image}" alt="${product.name}">
                        <div class="item-info">
                            <h4>${product.name}</h4>
                            <p class="price">₱${product.price.toFixed(2)}</p>
                        </div>
                        <div class="item-actions">
                            <button class="btn-primary" onclick="openCustomization(${product.id})">Add to Cart</button>
                            <button class="btn-remove" onclick="toggleWishlist(event, ${product.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('')}
        </div>
    `;
}

// Billing functionality
function handleCheckout() {
    const itemsToCheckout = cartItems.filter(item => item.selected);
    if (itemsToCheckout.length === 0) {
        alert("Please select items to checkout.");
        return;
    }
    openBillingModal(itemsToCheckout, 'cart');
}

function openBillingModal(items, source = 'cart') {
    closeModals();

    const summaryItemsEl = document.getElementById('billingSummaryItems');
    const summaryBoxEl = summaryItemsEl ? summaryItemsEl.parentElement : null;
    let total = 0;

    if (!summaryItemsEl || !summaryBoxEl) {
        console.error("Billing modal summary elements not found. Check your HTML.");
        alert("Could not display billing information. An error occurred.");
        return;
    }

    const oldTotalEl = summaryBoxEl.querySelector('#billingSummaryTotal');
    if (oldTotalEl) oldTotalEl.remove();

    console.log(items)
    let summaryHTML = items.map(item => {
        const product = PRODUCTS.find(p => p.id === item.id);
        if (!product) return '';

        let itemPrice = product.price;
        let customizationDetails = [];

        // ✅ Apply existing customization logic
        if (item.customization) {
            if (item.customization.size === 'large') itemPrice *= 1.1;
            if (item.customization.size === 'small') itemPrice *= 0.8;
            if (item.customization.finish === 'dark' || item.customization.finish === 'premium') itemPrice += 40;

            // ✅ Add extraCost if exists
            if (item.customization.extraCost) itemPrice += item.customization.extraCost;

            // ✅ Add ₱50 if engraving text exists
            if (item.customization.engraving && item.customization.engraving.trim() !== '') {
                itemPrice += 50;
                customizationDetails.push(`Engraving: "${item.customization.engraving}" (+₱50)`);
            }

            // ✅ Build readable customization details
            if (item.customization.sizeLabel) customizationDetails.push(`Size: ${item.customization.sizeLabel}`);
            if (item.customization.finish) {
                const finishDisplayName = FINISH_DISPLAY_NAMES[item.customization.finish] || item.customization.finish;
                customizationDetails.push(`Finish: ${finishDisplayName}`);
            }

            // 💌 Message Card (optional text)
            if (item.customization.messageCard && item.customization.messageCard.trim() !== '') {
                customizationDetails.push(`Message Card: "${item.customization.messageCard}"`);
            }

            // 🎁 Gift Packaging
            if (item.customization.giftPackaging && item.customization.giftPackaging !== 'none') {
                customizationDetails.push(`Gift Packaging: ${item.customization.giftPackaging}`);
            }

            // ♻️ Special Handling
            if (item.customization.specialHandling && item.customization.specialHandling !== 'none') {
                customizationDetails.push(`Handling: ${item.customization.specialHandling}`);
            }

            // 🚚 Delivery Preference
            if (item.customization.deliveryPreference && item.customization.deliveryPreference !== 'standard') {
                customizationDetails.push(`Delivery: ${item.customization.deliveryPreference}`);
            }
        }

        console.log(customizationDetails)
        const customizationDetailsHTML = customizationDetails.length > 0 
            ? `<div class="summary-customization-details">${customizationDetails.join(' | ')}</div>`
            : '';

        const subtotal = itemPrice * item.quantity;
        total += subtotal;

        return `
            <div class="summary-item" data-id="${product.id}" data-productCode="${product.product_code}" 
                style="display: flex; align-items: center; gap: 15px; padding: 1rem 0; border-bottom: 1px solid #f1f3f5;">
                <img src="${product.image}" alt="${product.name}" 
                    style="width: 70px; height: 70px; border-radius: 6px; object-fit: cover;">
                <div class="item-info" 
                    style="display: flex; justify-content: space-between; flex-grow: 1; align-items: flex-start;">
                    <div>
                        <strong style="font-size: 1.05rem; font-weight: 600;">${product.name}</strong> (x${item.quantity})
                        ${customizationDetailsHTML}
                    </div>
                    <p style="font-size: 1rem; font-weight: 600; white-space: nowrap; margin-left: 1rem;">
                        ₱${subtotal.toFixed(2)}
                    </p>
                </div>
            </div>
        `;
    }).join('');

    summaryItemsEl.innerHTML = summaryHTML;

    const subtotal = total;
    const shippingFee = 50.00;
    const finalTotal = subtotal + shippingFee;

    const summaryTotalEl = document.createElement('div');
    summaryTotalEl.id = 'billingSummaryTotal';
    summaryTotalEl.innerHTML = `
        <div class="summary-line"><span>Subtotal</span><span>₱${subtotal.toFixed(2)}</span></div>
        <div class="summary-line"><span>Shipping Fee</span><span>₱${shippingFee.toFixed(2)}</span></div>
        <div class="summary-line total-line"><span>Total</span><span>₱${finalTotal.toFixed(2)}</span></div>
    `;
    summaryBoxEl.appendChild(summaryTotalEl);

    billingModal.style.display = 'block';

    // --- Render Billing Form Sections ---
    const billingFormEl = document.getElementById('billingForm');
    if (billingFormEl) {
        billingFormEl.innerHTML = `
            <div class="billing-form-section">
                <h5>Delivery Address</h5>
                <div class="address-card selected">
                    <div class="address-card-header">
                        <strong>Juan Dela Cruz</strong>
                        <button class="remove-address" title="Remove Address">&times;</button>
                    </div>
                    <p>National Capital Region, Quezon City, Brgy. Sampaguita 123 Rizal Street, Green Valley Subdivision</p>
                    <p>09123456789</p>
                </div>
                <button class="btn-secondary add-address-btn">+ Add New Address</button>
            </div>

            <div class="billing-form-section">
                <h5>Payment Method</h5>
                <div class="payment-options" id="paymentOptionsContainer">
                    <div class="payment-option selected">
                        <input type="radio" id="payment_cod" name="payment_method" value="cod" checked>
                        <label for="payment_cod">Cash on Delivery</label>
                    </div>
                    <div class="payment-option" >
                        <input type="radio" id="payment_ewallet" name="payment_method" value="ewallet">
                        <label for="payment_ewallet">E-Wallets / Card</label>
                        <div class="payment-icons">
                            
                            <img src="mik/icons/gcash.png" alt="GCash" title="GCash">
                            <img src="mik/icons/maya.png" alt="Maya">
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Add event listener to handle selection style for payment options
    const paymentOptions = document.querySelectorAll('#paymentOptionsContainer .payment-option');
    paymentOptions.forEach(option => {
        option.addEventListener('click', () => {
            // Remove 'selected' class from all options
            paymentOptions.forEach(opt => opt.classList.remove('selected'));
            // Add 'selected' class to the clicked option
            option.classList.add('selected');
            // Also check the radio button inside
            option.querySelector('input[type="radio"]').checked = true;
        });
    });

    // --- NEW: Make the summary column sticky ---
    try {
        const summaryContainer = document.getElementById('billingSummaryItems').parentElement;

        // Add a class to the summary container for styling
        if (summaryContainer) summaryContainer.classList.add('order-summary-box');
        if (summaryContainer) summaryContainer.classList.add('billing-summary-sticky-container');
    } catch (e) {
        console.error("Error applying sticky styles to billing summary:", e);
    }

    // --- Hide the redundant footer total section ---
    const footerCosts = document.querySelector('#billingModal .modal-footer .billing-costs');
    if (footerCosts) {
        footerCosts.style.display = 'none';
    }

    // --- Dynamically create and set up the cancel/back button ---
    const actionsContainer = document.querySelector('#billingModal .modal-actions');
    if (actionsContainer) {
        // Remove any existing back button to prevent duplicates on reopen
        const existingBackBtn = actionsContainer.querySelector('#cancelBilling');
        if (existingBackBtn) existingBackBtn.remove();
        
        const backButton = document.createElement('button');
        backButton.id = 'cancelBilling';
        backButton.className = 'btn-secondary';
        
        if (source === 'customization' && currentCustomizingProduct) {
            backButton.textContent = 'Back to Customization';
            backButton.addEventListener('click', () => {
                closeModals();
                openCustomization(currentCustomizingProduct.id);
            });
        } else { // Default to 'cart'
            backButton.textContent = 'Back to Cart';
            backButton.addEventListener('click', () => {
                closeModals();
                openModal('cart');
            });
        }
        actionsContainer.prepend(backButton);
    }

    // ajax
    // fetch user address and contact number
    $.ajax({
        url: '../assets/php/fetch_user_address_number.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log("fetch_user_address_number response:", response);

            if (response.status === 'success' && response.data) {
                const addr = response.data;
                const addressContainer = $('.billing-form-container');

                // Remove any old default address card
                addressContainer.find('.address-card.default-address').remove();

                // Build default address card dynamically
                const html = `
                    <div class="address-card default-address selected" 
                        data-id="${escapeHtml(addr.id)}"
                        data-barangay="${escapeHtml(addr.barangay)}"
                        data-city="${escapeHtml(addr.city)}"
                        data-province="${escapeHtml(addr.province)}"
                        data-house_no="${escapeHtml(addr.house_no)}"
                        data-mobile="${escapeHtml(addr.mobile_number)}">
                        
                        <div class="address-card-header">
                            <strong>${escapeHtml(addr.firstname)} ${escapeHtml(addr.lastname)}</strong>
                            <button class="remove-address-btn" data-id="${escapeHtml(addr.id)}" title="Remove Address">&times;</button>
                        </div>

                        <div class="address-line">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${escapeHtml(addr.barangay)}, ${escapeHtml(addr.city)}, ${escapeHtml(addr.province)}<br>${escapeHtml(addr.house_no)}</span>
                        </div>

                        <div class="address-line">
                            <i class="fas fa-phone-alt"></i>
                            <span>${escapeHtml(addr.mobile_number)}</span>
                        </div>
                    </div>
                `;

                // Add before the "Add New Address" button
                addressContainer.find('.add-address-btn').before(html);
            } else {
                console.warn("No default address found:", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching default address:", error);
        }
    });


    $.ajax({
        url: '../assets/php/fetch_user_addresses.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log(response);

            if (response.status === 'success') {
                const addresses = response.data;
                const addressContainer = $('.billing-form-container');
                let html = '';

                addresses.forEach(addr => {
                    html += `
                        <div class="address-card ${addr.is_default ? 'selected' : ''}"
                            data-id="${escapeHtml(addr.id)}"
                            data-barangay="${escapeHtml(addr.barangay)}"
                            data-city="${escapeHtml(addr.city)}"
                            data-province="${escapeHtml(addr.province)}"
                            data-house_no="${escapeHtml(addr.house_no)}"
                            data-mobile="${escapeHtml(addr.mobile_number)}">
                            
                            <div class="address-card-header">
                                <strong>${escapeHtml(addr.full_name)}</strong>
                                <button class="remove-address-btn" data-id="${addr.id}" title="Remove Address">Remove</button>
                            </div>
                            
                            <div class="address-line">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>
                                    ${escapeHtml(addr.barangay)}, ${escapeHtml(addr.city)}, ${escapeHtml(addr.province)}<br>
                                    ${escapeHtml(addr.house_no)}
                                </span>
                            </div>
                            
                            <div class="address-line">
                                <i class="fas fa-phone-alt"></i>
                                <span>${escapeHtml(addr.mobile_number)}</span>
                            </div>
                        </div>
                    `;
                });

                // ✅ Insert address cards before the "Add Address" button
                addressContainer.find('.add-address-btn').before(html);
            } else {
                console.warn(response.message || "No addresses found.");
            }
        },
        error: function (xhr, status, error) {
            console.error("❌ Error fetching addresses:", error);
        }
    });


}

// Utility functions
function handlePlaceOrder() {
    // 🧾 Collect order items
    let orderItems = [];
    $('#billingSummaryItems .billing-summary-item, #billingSummaryItems .summary-item').each(function () {
        orderItems.push({
            product_code: $(this).data('productcode'),
            name: $(this).find('.item-name, strong').first().text().trim(),
            attributes: $(this).find('.item-attributes, .summary-customization-details').text().trim(),
            qty: $(this).find('.item-qty').text().trim() || $(this).text().match(/\(x\d+\)/)?.[0] || "",
            price: $(this).find('.summary-item-price, p').last().text().trim()
        });
    });

    // 🏠 Get selected address
    const selectedCard = $(".address-card.selected");
    if (selectedCard.length === 0) {
        alert("Please select a delivery address before placing your order.");
        return;
    }

    // 🧩 Extract address details from data attributes
    const selectedAddress = {
        id: selectedCard.data("id"),
        barangay: selectedCard.data("barangay") || "",
        city: selectedCard.data("city") || "",
        province: selectedCard.data("province") || "",
        house_no: selectedCard.data("house_no") || "",
        mobile: selectedCard.data("mobile") || ""
    };

    const fullName = selectedCard.find('strong').text().trim();

    const userData = {
        name: fullName,
        mobile: selectedAddress.mobile,
        address_id: selectedAddress.id,  // ✅ send the address ID to backend
        address: {
            barangay: selectedAddress.barangay,
            city: selectedAddress.city,
            province: selectedAddress.province,
            house_no: selectedAddress.house_no
        }
    };

    // 💳 Collect payment and totals
    const paymentMethod = $('input[name="payment"]:checked').val();
    const cleanMoney = (val) => parseFloat(val.replace(/[^\d.]/g, "")) || 0;
    const subtotal = cleanMoney($('#billingSubtotal').text());
    const shipping = cleanMoney($('#billingShipping').text());
    const total = cleanMoney($('#billingSummaryTotal .total-line span:last').text());

    const orderData = {
        items: orderItems,
        user: userData,
        payment: paymentMethod,
        totals: { subtotal, shipping, total }
    };

    console.log("🧾 Sending Order Data:", orderData);

    $.ajax({
        url: "../assets/php/place_order.php",
        type: "POST",
        data: { orderData: JSON.stringify(orderData) },
        dataType: "json",
        success: function (response) {
            console.table(response);

            if (response.status === "success") {
                showToast("✅ Order placed successfully!", "success");

                // ✅ Logic to remove checked-out items from cart
                const itemsToKeep = cartItems.filter(item => !item.selected);
                cartItems = itemsToKeep;

                // ✅ If the cart is now empty, uncheck the "Select All" box
                if (document.getElementById('selectAllCheckbox')) {
                    document.getElementById('selectAllCheckbox').checked = false;
                }

                updateCounts();
                saveState();
                closeModals();

            } else {
                alert("Error: " + (response.message || "Unknown error occurred."));
            }
        },
        error: function (xhr, status, error) {
            console.error("Order error:", error);
            alert("Something went wrong while placing your order. Please try again later.");
        }
    });

}



/**
    * Reverts the main image to the currently selected state when hover ends on any option.
    */
function endOptionPreview() {
    // Simply re-run the main update function. It will look at the *checked*
    // radio buttons and restore the view to that state.
    updateCustomizationView(true);
}

/**
    * Previews the default product image on size option hover.
    */
function previewSize() {
    if (!currentCustomizingProduct) return;
    const defaultImageSrc = currentCustomizingProduct.images?.default || currentCustomizingProduct.image;
    if (defaultImageSrc) {
        document.getElementById('customizationImage').src = defaultImageSrc;
    }
}

/**
    * Previews a finish option image on hover.
    * @param {string} finishValue - The value of the finish to preview ('natural', 'dark', etc.).
    */
function previewFinish(finishValue) {
    if (!currentCustomizingProduct || !finishValue) return;

    const imageKey = finishValue;
    const previewImageSrc = currentCustomizingProduct.images[imageKey];

    if (previewImageSrc) {
        document.getElementById('customizationImage').src = previewImageSrc;
    }
}

/**
    * Previews a thumbnail image on hover without changing the selection.
    * @param {HTMLElement} thumbnailElement - The thumbnail image element being hovered over.
    */
function previewThumbnail(thumbnailElement) {
    if (thumbnailElement) {
        document.getElementById('customizationImage').src = thumbnailElement.src;
    }
}

/**
    * Reverts the main image to the currently selected (active) thumbnail when hover ends.
    */
function endPreviewThumbnail() {
    const activeThumbnail = document.querySelector('#thumbnailGallery img.active');
    // Revert to the image of the currently active thumbnail.
    if (activeThumbnail) document.getElementById('customizationImage').src = activeThumbnail.src;
}

/**
    * Creates an animation of an image flying from a source to the wishlist button.
    * @param {HTMLElement} sourceImgElement The source image element to animate from.
    */
function animateToWishlist(sourceImgElement) {
    const wishlistBtn = document.getElementById('wishlistBtn');
    if (!sourceImgElement || !wishlistBtn) return;

    const sourceRect = sourceImgElement.getBoundingClientRect();
    const targetRect = wishlistBtn.getBoundingClientRect();

    const flyer = document.createElement('img');
    flyer.src = sourceImgElement.src;
    flyer.style.position = 'fixed';
    flyer.style.zIndex = '9999';
    flyer.style.borderRadius = '8px';
    // Use 'contain' to ensure the entire image is visible and not cropped.
    flyer.style.objectFit = 'contain';
    // Add a background for images that don't fill the container.
    flyer.style.backgroundColor = '#fff';
    flyer.style.transition = 'all 0.8s cubic-bezier(0.55, 0, 1, 0.45)';

    // Set initial position and size
    flyer.style.left = `${sourceRect.left}px`;
    flyer.style.top = `${sourceRect.top}px`;
    flyer.style.width = `${sourceRect.width}px`;
    flyer.style.height = `${sourceRect.height}px`;

    document.body.appendChild(flyer);

    // Animate to target after a short delay to ensure the element is in the DOM
    setTimeout(() => {
        // Move the image so its center aligns with the wishlist button's center
        flyer.style.left = `${targetRect.left + (targetRect.width / 2) - (sourceRect.width / 2)}px`;
        flyer.style.top = `${targetRect.top + (targetRect.height / 2) - (sourceRect.height / 2)}px`;
        // Don't shrink the image, just fade it out as it reaches the destination.
        flyer.style.opacity = '0';
    }, 10);

    // Clean up and shake wishlist button
    setTimeout(() => {
        wishlistBtn.classList.add('cart-shake'); // Reuse the same shake animation
        setTimeout(() => wishlistBtn.classList.remove('cart-shake'), 400);
        flyer.remove();
    }, 800); // Must match transition duration
}

function getCartTotal() {
    return cartItems
        .filter(item => item.selected)
        .reduce((total, item) => {
            const product = PRODUCTS.find(p => p.id === item.id);
            let price = product?.price || 0;

            if (item.customization) {
                // Apply size adjustments
                if (item.customization.size === 'large') price *= 1.1;
                if (item.customization.size === 'small') price *= 0.8;

                // Apply finish adjustments
                if (item.customization.finish === 'dark' || item.customization.finish === 'premium') {
                    price += 40;
                }

                // ✅ Include any extra cost (engraving, gift wrap, etc.)
                if (item.customization.extraCost) {
                    price += item.customization.extraCost;
                }
            }

            return total + (price * item.quantity);
        }, 0);
}


function updateCounts() {
    const cartCount = cartItems.reduce((total, item) => total + item.quantity, 0);
    const cartCountEl = document.getElementById('cartCount');
    const wishlistCountEl = document.getElementById('wishlistCount');

    if (cartCountEl) {
        cartCountEl.textContent = cartCount;
        cartCountEl.style.display = cartCount > 0 ? 'flex' : 'none';
    }

    if (wishlistCountEl) {
        wishlistCountEl.textContent = wishlistItems.length;
        wishlistCountEl.style.display = wishlistItems.length > 0 ? 'flex' : 'none';
    }
}

let notificationTimer; // To manage the hide timeout

function showSuccessNotification(details) {
    const notification = document.getElementById('successNotification');
    const cartBtn = document.getElementById('cartBtn');
    if (!notification || !cartBtn) return;

    // --- STAGE 1: Show notification in the top-right ---
    clearTimeout(notificationTimer);

    // Reset any inline styles from a previous animation
    notification.style.transition = '';
    notification.style.top = '';
    notification.style.left = '';
    notification.style.width = '';
    notification.style.height = '';
    notification.style.opacity = '';
    notification.style.transform = '';

    // Populate notification with data
    const { product, customization, quantity } = details;
    document.getElementById('notificationImage').src = product.image;
    document.getElementById('notificationName').textContent = product.name;
    document.getElementById('notificationQuantity').textContent = `Quantity: ${quantity}`;
    const sizeEl = document.getElementById('notificationSize');
    const finishEl = document.getElementById('notificationFinish');
    if (customization && customization.sizeLabel && customization.finish) {
        sizeEl.textContent = `Size: ${customization.sizeLabel}`;
        const finishName = FINISH_DISPLAY_NAMES[customization.finish] || customization.finish;
        finishEl.textContent = `Finish: ${finishName}`;
        sizeEl.style.display = 'block';
        finishEl.style.display = 'block';
    } else {
        sizeEl.style.display = 'none';
        finishEl.style.display = 'none';
    }

    // Force reflow and slide in the notification
    notification.classList.remove('show');
    void notification.offsetWidth;
    notification.classList.add('show');
    
    // --- STAGE 2: After a delay, animate the notification to the cart ---
    notificationTimer = setTimeout(() => {
        const targetRect = cartBtn.getBoundingClientRect();
        const targetTop = targetRect.top + targetRect.height / 2;
        const targetLeft = targetRect.left + targetRect.width / 2;

        // Apply a new transition for the "fly" effect and set the target styles
        notification.style.transition = 'all 0.8s cubic-bezier(0.6, -0.28, 0.735, 0.045)';
        notification.style.top = `${targetTop}px`;
        notification.style.left = `${targetLeft}px`;
        notification.style.width = '0px';
        notification.style.height = '0px';
        notification.style.opacity = '0';

        // After the flight animation finishes, shake the cart
        setTimeout(() => {
            cartBtn.classList.add('cart-shake');
            setTimeout(() => cartBtn.classList.remove('cart-shake'), 400);
        }, 800); // Must match the flight transition duration
    }, 3500); // Wait 3.5 seconds before starting the flight
}

function saveState() {
    try {
        localStorage.setItem('cartItems', JSON.stringify(cartItems));
        localStorage.setItem('wishlistItems', JSON.stringify(wishlistItems));
    } catch (error) {
        console.error("Failed to save state to localStorage:", error);
    }
}

// Initialize the app
init();
