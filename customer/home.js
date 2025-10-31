const PRODUCTS = [
      {
        id: 1,
        name: "Sulu-Light Lampshade",
        description: "Handcrafted bamboo lampshade",
        price: 150.0,
        category: "lampshades",
        image: "/bamboo-products.png"
      },
      {
        id: 2,
        name: "Ilaw - Kawayan",
        description: "Bamboo light fixture",
        price: 100.0,
        category: "lampshades",
        image: "/bamboo-products.png"
      },
      {
        id: 3,
        name: "HabitHook Rack",
        description: "Bamboo wall rack",
        price: 100.0,
        category: "furnitures",
        image: "/bamboo-products.png"
      },
      {
        id: 4,
        name: "Tadyaw",
        description: "Bamboo jar/container",
        price: 100.0,
        category: "mugs",
        image: "/bamboo-products.png"
      },
      {
        id: 5,
        name: "Pugad Holder",
        description: "Bamboo phone holder",
        price: 100.0,
        category: "kitchenware",
        image: "/bamboo-products.png"
      },
      {
        id: 6,
        name: "Kape - Kawayan",
        description: "Bamboo coffee mug",
        price: 100.0,
        category: "mugs",
        image: "/bamboo-products.png"
      },
      {
        id: 7,
        name: "Dayang - Dulo",
        description: "Bamboo decorative item",
        price: 100.0,
        category: "basket",
        image: "/bamboo-products.png"
      }
    ];

    let cartItems = [];
    let wishlistItems = [];

    const cartBtn = document.getElementById('cartBtn');
    const wishlistBtn = document.getElementById('wishlistBtn');
    const cartModal = document.getElementById('cartModal');
    const wishlistModal = document.getElementById('wishlistModal');
    const orderStatusModal = document.getElementById('orderStatusModal');
    const closeCartBtn = document.getElementById('closeCart');
    const closeWishlistBtn = document.getElementById('closeWishlist');
    const cartCountSpan = document.getElementById('cartCount');
    const wishlistCountSpan = document.getElementById('wishlistCount');

    const submodulesBtn = document.getElementById('submodulesBtn');
    const submodulesModal = document.getElementById('submodulesModal'); // create this modal in HTML
    const closeSubmodules = document.getElementById('closeSubmodules');

    const viewOrderStatusBtn = document.getElementById('viewOrderStatus');

    function updateCounts() {
      // Update cart and wishlist counts on both home and products page buttons
      const totalCartItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);

      // Update all cart count spans
      document.querySelectorAll('#cartCount, .cart-count').forEach(span => {
        span.textContent = totalCartItems;
        span.style.display = totalCartItems > 0 ? 'inline-block' : 'none';
      });

      // Update all wishlist count spans
      document.querySelectorAll('#wishlistCount, .wishlist-count').forEach(span => {
        span.textContent = wishlistItems.length;
        span.style.display = wishlistItems.length > 0 ? 'inline-block' : 'none';
      });
      cartCountSpan.textContent = totalCartItems;
      cartCountSpan.style.display = totalCartItems > 0 ? 'inline-block' : 'none';

      wishlistCountSpan.textContent = wishlistItems.length;
      wishlistCountSpan.style.display = wishlistItems.length > 0 ? 'inline-block' : 'none';
    }

    function addToCart(productId) {
      const product = PRODUCTS.find(p => p.id === productId);
      if (!product) return;

      const existingItem = cartItems.find(item => item.id === productId);
      if (existingItem) {
        existingItem.quantity++;
      } else {
        cartItems.push({ id: productId, quantity: 1 });
      }
      // updateCounts();
      showNotification(`${product.name} added to cart!`);
      saveCartAndWishlist();
    }

    function toggleWishlist(productId) {
      const index = wishlistItems.indexOf(productId);
      const product = PRODUCTS.find(p => p.id === productId);
      if (index > -1) {
        wishlistItems.splice(index, 1);
        showNotification(`${product.name} removed from wishlist!`);
      } else {
        wishlistItems.push(productId);
        showNotification(`${product.name} added to wishlist!`);
      }
      updateCounts();
      saveCartAndWishlist();
      // Re-render the products to show the updated wishlist status
      displayProducts(document.querySelector('.category-btn.active')?.dataset.category || 'all');
    }

    function showNotification(message) {
      const notification = document.createElement('div');
      notification.className = 'notification';
      notification.textContent = message;
      document.body.appendChild(notification);
      setTimeout(() => notification.remove(), 3000);
    }

    function renderCartItems() {
      const cartItemsContainer = document.getElementById('cartItems');
      const cartFooter = document.getElementById('cartFooter');
      if (cartItems.length === 0) {
        cartItemsContainer.innerHTML = '<p style="text-align: center; color: #555;">Your cart is empty.</p>';
        cartFooter.innerHTML = '';
        return;
      }

      let total = 0;
      cartItemsContainer.innerHTML = cartItems.map(item => {
        const product = PRODUCTS.find(p => p.id === item.id);
        if (!product) return '';
        total += product.price * item.quantity;
        return `
          <div class="cart-item" style="display: flex; align-items: center; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <img src="${product.image}" alt="${product.name}" style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px; border-radius: 4px;">
            <div style="flex-grow: 1;">
              <h4 style="margin: 0; font-size: 1em;">${product.name}</h4>
              <p style="margin: 5px 0 0; font-size: 0.9em; color: #777;">₱${product.price.toFixed(2)} x ${item.quantity}</p>
            </div>
            <button onclick="removeFromCart(${product.id})" style="background: none; border: none; color: #ff4500; font-size: 1.2em; cursor: pointer;">&times;</button>
          </div>
        `;
      }).join('');

      cartFooter.innerHTML = `
        <div style="font-size: 1.2em; font-weight: bold; margin-bottom: 15px;">Total: ₱${total.toFixed(2)}</div>
        <button style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em;">Checkout</button>
      `;
    }
 
    function removeFromCart(productId) {
      cartItems = cartItems.filter(item => item.id !== productId);
      // updateCounts();
      renderCartItems();
      saveCartAndWishlist();
    }

    function renderWishlistItems() {
      const wishlistItemsContainer = document.getElementById('wishlistItems');
      if (wishlistItems.length === 0) {
        wishlistItemsContainer.innerHTML = '<p style="text-align: center; color: #555;">Your wishlist is empty.</p>';
        return;
      }

      wishlistItemsContainer.innerHTML = wishlistItems.map(productId => {
        const product = PRODUCTS.find(p => p.id === productId);
        if (!product) return '';
        return `
          <div class="wishlist-item" style="display: flex; align-items: center; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <img src="${product.image}" alt="${product.name}" style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px; border-radius: 4px;">
            <div style="flex-grow: 1;">
              <h4 style="margin: 0; font-size: 1em;">${product.name}</h4>
              <p style="margin: 5px 0 0; font-size: 0.9em; color: #777;">₱${product.price.toFixed(2)}</p>
            </div>
            <button onclick="addToCart(${product.id}); toggleWishlist(${product.id})" style="background-color: #007bff; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8em; margin-right: 5px;">Add to Cart</button>
            <button onclick="toggleWishlist(${product.id})" style="background: none; border: none; color: #ff4500; font-size: 1.2em; cursor: pointer;">&times;</button>
          </div>
        `;
      }).join('');
    }


    function fetchOrders() {
      $.ajax({
          url: "../assets/php/fetch_user_orders.php", // your PHP file
          type: "POST",
          dataType: "json",
          success: function(response) {
              console.log(response)
              const modalBody = $("#orderStatusModal .modal-body");
              modalBody.empty();

              if (response.status === "success") {
                  const orders = response.orders;

                  if (orders.length === 0) {
                      modalBody.html('<div class="no-orders">No orders found.</div>');
                      return;
                  }

                  orders.forEach(order => {
                      const timelineSteps = ['pending', 'processing', 'shipped', 'completed'];
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

                      // Items list
                      let itemsHtml = '<ul class="order-items">';
                      order.items.forEach(item => {
                          itemsHtml += `<li>${item.name} (${item.attributes}) - Qty: ${item.qty} - ₱${parseFloat(item.price).toFixed(2)}</li>`;
                      });
                      itemsHtml += '</ul>';

                     // Assuming 'orders' is an array of orders for the current user
                      orders.forEach((order, index) => {
                          // index starts from 0, so +1 to make it human-friendly
                          const orderNumber = index + 1;

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
                                  <hr>
                              </div>
                          `);
                      });

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


    // Optional: click outside modal to close
    window.addEventListener('click', (e) => {
        if(e.target == submodulesModal){
            submodulesModal.style.display = 'none';
        }
    });

    window.addEventListener('click', (event) => {
      if (event.target === cartModal) {
        cartModal.style.display = 'none';
      }
      if (event.target === wishlistModal) {
        wishlistModal.style.display = 'none';
      }
    });

    // Load cart and wishlist from localStorage on page load
    function loadCartAndWishlist() {
      const storedCart = localStorage.getItem('cartItems');
      const storedWishlist = localStorage.getItem('wishlistItems');
      if (storedCart) cartItems = JSON.parse(storedCart);
      if (storedWishlist) wishlistItems = JSON.parse(storedWishlist);
    }

    // Save cart and wishlist to localStorage
    function saveCartAndWishlist() {
      localStorage.setItem('cartItems', JSON.stringify(cartItems));
      localStorage.setItem('wishlistItems', JSON.stringify(wishlistItems));
    }

    function displayProducts(category = 'lampshades', searchTerm = '') {
        const productsGrid = document.getElementById('productsGrid');
        if (!productsGrid) return;

        let filteredProducts = PRODUCTS;
        if (category && category !== 'all') {
            filteredProducts = PRODUCTS.filter(p => p.category === category);
        }

        productsGrid.innerHTML = '';
        if (filteredProducts.length === 0) {
            productsGrid.innerHTML = '<p style="grid-column: 1 / -1; text-align: center; padding: 2rem;">No products found in this category.</p>';
            return;
        }

        filteredProducts.forEach(product => {
            const isWishlisted = wishlistItems.includes(product.id);
            const productCardHTML = `
                <div class="product-card" data-product-id="${product.id}">
                    <div class="product-image-container">
                        <img src="${product.image}" alt="${product.name}" class="product-image">
                        <button class="wishlist-btn ${isWishlisted ? 'active' : ''}" title="Add to Wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name">${product.name}</h3>
                        <p class="product-description">${product.description}</p>
                        <p class="product-price">₱${product.price.toFixed(2)}</p>
                    </div>
                    <div class="product-actions">
                        <button class="btn-primary cart-btn">Add to Cart</button>
                    </div>
                </div>
            `;
            productsGrid.insertAdjacentHTML('beforeend', productCardHTML);
        });
    }

    function loadFeaturedProducts(filter = "new") {
      console.log("Loading filter:", filter);

      const grid = document.querySelector(".featured .grid");
      if (!grid) return;

      grid.innerHTML = `<p style="grid-column: 1 / -1; text-align:center; padding:2rem;">Loading...</p>`;

      $.ajax({
        url: "../assets/php/fetch_new_products.php", // ✅ single PHP endpoint
        type: "GET",
        data: { filter }, // pass filter param (?filter=new / bestsellers / favorites)
        dataType: "json",
        success: function (response) {
          if (response.status !== "success") {
            grid.innerHTML = `<p style="grid-column: 1 / -1; text-align:center; padding:2rem;">Failed to load products.</p>`;
            return;
          }

          const products = response.data;
          grid.innerHTML = "";

          if (!products || products.length === 0) {
            grid.innerHTML = `<p style="grid-column: 1 / -1; text-align:center; padding:2rem;">No products available.</p>`;
            return;
          }

          products.forEach(product => {
            const cardHTML = `
              <article class="card" data-product-id="${product.id}">
                <div class="card-media">
                  <img src="${product.image}" alt="${product.name}">
                </div>
                <div class="card-meta">
                  <p class="title">${product.name}</p>
                  <p class="price">₱${parseFloat(product.price).toFixed(2)}</p>
                </div>
              </article>
            `;
            grid.insertAdjacentHTML("beforeend", cardHTML);
          });
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error:", error);
          grid.innerHTML = `<p style="grid-column: 1 / -1; text-align:center; padding:2rem;">Error fetching products.</p>`;
        }
      });
    }

    document.querySelectorAll(".filter-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        const label = btn.textContent.trim().toLowerCase();
        let filter = "new";
        if (label.includes("best")) filter = "bestsellers";
        else if (label.includes("favorite")) filter = "favorites";

        loadFeaturedProducts(filter);
      });
    });





    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll(".filter-btn");

        buttons.forEach(btn => {
          btn.addEventListener("click", () => {
            buttons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            const filterType = btn.getAttribute("data-filter");
            loadFeaturedProducts(filterType);
          });
        });

        // Load default (WHAT’S NEW)
        loadFeaturedProducts("new");


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

        
  });
