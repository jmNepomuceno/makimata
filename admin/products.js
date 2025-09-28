class ProductManager {
  constructor() {
    this.products = []
    this.filteredProducts = []
    this.sortKey = 'id';
    this.sortDirection = 'asc';
    this.init()
  }

  init() {
    this.loadProducts()
    this.setupEventListeners()
    this.initializeFilters()
    this.updateSortIcons();
  }

  _createLog(action, description, severity = 'info') {
      try {
          const logs = JSON.parse(localStorage.getItem('mikamataActivityLogs') || '[]');
          const newLog = {
              id: `log-${Date.now()}`,
              user: { name: 'Admin User', avatar: '../placeholder.svg?height=32&width=32&text=A' },
              action: action,
              description: description,
              severity: severity,
              icon: action === 'create' ? 'fa-plus' : (action === 'update' ? 'fa-edit' : 'fa-trash'),
              timestamp: new Date().toISOString(),
              ip: '127.0.0.1', // Mock IP
              details: {}
          };
          logs.unshift(newLog);
          localStorage.setItem('mikamataActivityLogs', JSON.stringify(logs.slice(0, 100)));
      } catch (error) {
          console.error("Failed to create activity log:", error);
      }
  }

  _createNotification(title, message, type = 'product') {
      try {
          const notifications = JSON.parse(localStorage.getItem('mikamataNotifications') || '[]');
          const newNotification = {
              id: `notif-${Date.now()}`,
              type: type,
              icon: 'fa-cube',
              title: title,
              message: message,
              recipient: 'Admin',
              date: new Date().toISOString(),
              status: 'sent',
              link: 'products.html', // Link back to the products page
              targetId: null // Could be the new product ID
          };
          notifications.unshift(newNotification);
          localStorage.setItem('mikamataNotifications', JSON.stringify(notifications.slice(0, 50))); // Keep it from growing too large
      } catch (error) {
          console.error("Failed to create notification:", error);
      }
  }

  async loadProducts() {
    try {
      // --- DATABASE INTEGRATION PLACEHOLDER ---
      const savedProducts = localStorage.getItem('mikamataProducts');
      this.products = savedProducts ? JSON.parse(savedProducts) : [];

      if (this.products.length === 0) {
        console.warn("No products found. Using fallback mock data. Connect to your database.");
      }

    } catch (error) {
      console.error("Error loading products from localStorage:", error);
      this.products = [];
    }

    this.filteredProducts = [...this.products];
    this.renderProducts();
    this.updateProductStats();
  }

  async saveProductsToStorage() {
    // --- DATABASE INTEGRATION PLACEHOLDER ---
    localStorage.setItem('mikamataProducts', JSON.stringify(this.products));
  }

  setupEventListeners() {
    const searchInput = document.querySelector("#product-search")
    if (searchInput) {
      searchInput.addEventListener("input", e => {
        this.filterAndRender()
      })
    }

    const addBtn = document.getElementById("add-product-btn");
    if (addBtn) { 
      addBtn.addEventListener("click", () => {
        this.openProductModal()
      })
    }

    const categoryFilter = document.querySelector("#category-filter");
    if (categoryFilter) {
      categoryFilter.addEventListener("change", () => this.filterAndRender());
    }

    const minPriceInput = document.getElementById("min-price");
    if (minPriceInput) minPriceInput.addEventListener("input", () => this.filterAndRender());
    const maxPriceInput = document.getElementById("max-price");
    if (maxPriceInput) maxPriceInput.addEventListener("input", () => this.filterAndRender());

    // Sorting listeners
    document.querySelectorAll(".products-table th.sortable").forEach(header => {
      header.addEventListener("click", () => {
        const key = header.dataset.sort;
        if (this.sortKey === key) {
          this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
          this.sortKey = key;
          this.sortDirection = 'asc';
        }
        this.filterAndRender();
      });
    });
    const closeBtn = document.querySelector("#product-modal .modal-close");
    if (closeBtn) {
      closeBtn.addEventListener("click", () => this.closeProductModal());
    }
    const cancelBtn = document.getElementById("cancel-btn");
    if (cancelBtn) {
      cancelBtn.addEventListener("click", () => this.closeProductModal());
    }

    window.addEventListener("click", (event) => {
      if (event.target === document.getElementById("product-modal")) this.closeProductModal();
    });

    const productForm = document.getElementById("product-form");
    if (productForm) {
      productForm.addEventListener("submit", (e) => this.saveProduct(e));
    }

    this.setupImageUploadListeners();

    const addSizeBtn = document.getElementById("add-size-option-btn");
    if (addSizeBtn) {
      addSizeBtn.addEventListener("click", () => this.addSizeOption());
    }
    const addColorBtn = document.getElementById("add-color-option-btn");
    if (addColorBtn) {
      addColorBtn.addEventListener("click", () => this.addColorOption());
    }

    const bulkActivateBtn = document.getElementById("bulk-activate");
    if (bulkActivateBtn) bulkActivateBtn.addEventListener("click", () => this.performBulkAction('activate'));

    const bulkDeleteBtn = document.getElementById("bulk-delete");
    if (bulkDeleteBtn) bulkDeleteBtn.addEventListener("click", () => this.performBulkAction('delete'));

    const selectAllCheckbox = document.getElementById("select-all");
    if (selectAllCheckbox) selectAllCheckbox.addEventListener("change", (e) => this.toggleBulkSelect(e.target.checked));

    const tableBody = document.getElementById("products-table-body");
    if (tableBody) {
      tableBody.addEventListener('click', (event) => {
        const target = event.target;
        const editButton = target.closest('.edit-btn');
        const deleteButton = target.closest('.delete-btn');

        if (editButton) {
          const productId = parseInt(editButton.dataset.id);
          if (productId) {
            this.editProduct(productId);
          }
        }

        if (deleteButton) {
          const productId = parseInt(deleteButton.dataset.id);
          if (productId) {
            this.deleteProduct(productId);
          }
        }
      });
    }
  }

  filterAndRender() {
    const searchQuery = document.getElementById("product-search")?.value.toLowerCase() || "";
    const categoryFilter = document.getElementById("category-filter")?.value || "";
    const minPrice = parseFloat(document.getElementById("min-price")?.value) || 0;
    const maxPrice = parseFloat(document.getElementById("max-price")?.value) || Infinity;

    this.filteredProducts = this.products.filter(
      (product) => {
        const matchesSearch = product.name.toLowerCase().includes(searchQuery) ||
                              (product.description && product.description.toLowerCase().includes(searchQuery));

        const matchesCategory = !categoryFilter || product.category === categoryFilter;

        // Price filter check
        const matchesPrice = product.price >= minPrice && product.price <= maxPrice;

        return matchesSearch && matchesCategory && matchesPrice;
      }
    );

    this.filteredProducts.sort((a, b) => {
      let valA = a[this.sortKey];
      let valB = b[this.sortKey];

      if (typeof valA === 'string') {
        valA = valA.toLowerCase();
        valB = valB.toLowerCase();
      }

      if (valA < valB) {
        return this.sortDirection === 'asc' ? -1 : 1;
      }
      if (valA > valB) {
        return this.sortDirection === 'asc' ? 1 : -1;
      }
      return 0;
    });

    this.renderProducts();
    this.updateSortIcons();
  }

  toggleBulkSelect(isChecked) {
    document.querySelectorAll(".product-checkbox").forEach(checkbox => {
      checkbox.checked = isChecked;
    });
    this.updateBulkActionsVisibility();
  }

  updateSortIcons() {
    document.querySelectorAll(".products-table th.sortable").forEach(header => {
      const icon = header.querySelector("i");
      if (icon) {
        if (header.dataset.sort === this.sortKey) {
          icon.className = this.sortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
        } else {
          icon.className = 'fas fa-sort';
        }
      }
    });
  }

  renderProducts() {
    this.renderTableView()
  }

  renderTableView() {
    const tableBody = document.getElementById("products-table-body");
    if (!tableBody) return;
    tableBody.innerHTML = this.filteredProducts
                          .map(
                            (product) => `
                            <tr>
                                <td><input type="checkbox" class="product-checkbox" data-id="${product.id}" onchange="productManager.updateBulkActionsVisibility()"></td>
                                <td>#${product.id}</td>
                                <td class="product-info-cell">
                                    <img src="${ 
                                      (product.image && product.image.startsWith('data:image'))
                                        ? product.image
                                        : (product.image || 'mik/products/placeholder.png')
                                    }" alt="${product.name}" class="product-thumbnail">
                                    <span>${product.name}</span>
                                </td>
                                <td><span class="category-badge ${product.category}">${product.category}</span></td>
                                <td>₱${product.price.toLocaleString()}</td>
                                <td><span class="stock-badge ${product.stock > 10 ? 'normal' : (product.stock > 0 ? 'low' : 'out')}">${product.stock}</span></td>
                                <td>${product.dateAdded || 'N/A'}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon edit-btn" data-id="${product.id}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon delete-btn" data-id="${product.id}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `,
                          )
                          .join("");

  }

  openProductModal(productId = null) {
    const modal = document.querySelector("#product-modal")
    const modalTitle = document.getElementById("modal-title");
    if (modal) {
      if (productId) {
        const product = this.products.find((p) => p.id === productId)
        if (product) this.populateProductForm(product);
      } else {
        this.clearProductForm()
      }
      if (modalTitle) modalTitle.textContent = productId ? "Edit Product" : "Add New Product";
      modal.style.display = "flex"
    }
  }

  closeProductModal() {
    const modal = document.querySelector("#product-modal");
    if (modal) {
      modal.style.display = "none";
      this.clearProductForm();
    }
  }

  clearProductForm() {
    const form = document.getElementById("product-form");
    if (form) form.reset();
    const productIdInput = document.getElementById("product-id");
    // document.getElementById("image-preview").innerHTML = '';
    // document.getElementById("size-options-container").innerHTML = '';
    // document.getElementById("color-options-container").innerHTML = '';
    if (productIdInput) productIdInput.value = "";
  }

  // Populate product form for editing
  populateProductForm(product) {
    document.getElementById("product-id").value = product.id;
    document.getElementById("product-name").value = product.name;
    document.getElementById("product-description").value = product.description;
    document.getElementById("base-price").value = product.price;
    document.getElementById("category-id").value = product.category;
    document.getElementById("stock-quantity").value = product.stock;
    
    const imagePreviewContainer = document.getElementById("image-preview");
    imagePreviewContainer.innerHTML = '';
    const mainImage = product.image || '/mik/products/placeholder.png';
    this.createImagePreview(mainImage, "current-image");

    if (product.sizeOptions && Array.isArray(product.sizeOptions)) {
      product.sizeOptions.forEach(opt => this.addSizeOption(opt));
    }
    if (product.colorOptions && Array.isArray(product.colorOptions)) {
      product.colorOptions.forEach(opt => this.addColorOption(opt));
    }
  }

  editProduct(id) {
    this.openProductModal(id)
  }

  async saveProduct(event) {
    const savingToast = await showToast('Saving product...', 'info', { duration: 0 });
    event.preventDefault();
    const form = event.target;
    const productId = form.querySelector("#product-id").value;
    const isNewProduct = !productId;
    
    const productData = {
      name: form.querySelector("#product-name").value,
      description: form.querySelector("#product-description").value,
      price: parseFloat(form.querySelector("#base-price").value),
      category: form.querySelector("#category-id").value,
      stock: parseInt(form.querySelector("#stock-quantity").value),
      sizeOptions: this.getCustomizationOptions('size'),
      colorOptions: this.getCustomizationOptions('color'),
      image: this.getUploadedImageUrls()[0] || "/mik/products/placeholder.png"
    };

    try {
      const formData = new FormData();

      // Basic product info
      formData.append('product_id', productId || '');
      formData.append('name', form.querySelector("#product-name").value);
      formData.append('description', form.querySelector("#product-description").value);
      formData.append('price', parseFloat(form.querySelector("#base-price").value));
      formData.append('category', form.querySelector("#category-id").value);
      formData.append('stock', parseInt(form.querySelector("#stock-quantity").value));
      formData.append('low_stock_threshold', parseInt(form.querySelector("#low-stock-threshold").value));

      // Main product image
      const imageFiles = form.querySelector("#product-images").files;
      let mainImageName = "";
      if (imageFiles.length > 0) {
          mainImageName = imageFiles[0].name;
          formData.append("main_image_file", imageFiles[0]); // ✅ send actual file
      }
      formData.append('main_image', mainImageName);

      // Color/Finish images
      const colorInputs = form.querySelectorAll('#color-options-container input[type="file"]');
      let colorImages = { burly: "", coffee: "", rust_brown: "" };

      colorInputs.forEach(input => {
          const colorKey = input.name.match(/\[(.*)\]/)[1]; 
          if (input.files.length > 0) {
              colorImages[colorKey] = input.files[0].name;
              formData.append(`color_image_files[${colorKey}]`, input.files[0]); // ✅ send actual file
          }
      });

      // Final images object (only file names, not paths)
      const imagesJson = {
          dark: colorImages.burly || "",
          default: mainImageName || "",
          natural: colorImages.coffee || "",
          premium: colorImages.rust_brown || ""
      };
      formData.append("images", JSON.stringify(imagesJson));

      // Debug print
      for (let [key, value] of formData.entries()) {
          console.log(key, value);
      }

      $.ajax({
          url: '../assets/php_admin/save_product.php',
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(response) {
              console.log(response);
          },
          error: function(err) {
              console.error('AJAX error:', err);
          }
      });



      let response;
      if (isNewProduct) {
        productData.id = Date.now();
        this.products.unshift(productData);
        this._createLog('create', `Created new product: "${productData.name}"`);
        this._createNotification('New Product Added', `Product "${productData.name}" was successfully added.`);
        showToast('Product added successfully!', 'success', { toastInstance: savingToast });

      } else {
        const index = this.products.findIndex(p => p.id == productId);
        if (index !== -1) {
            this.products[index] = { ...this.products[index], ...productData, id: parseInt(productId) };
        }
        this._createLog('update', `Updated product: "${productData.name}" (ID: ${productId})`);
        showToast('Product updated successfully!', 'success', { toastInstance: savingToast });
      }

      this.closeProductModal();
    } catch (error) {
      console.error('Failed to save product:', error);
      showToast('Failed to save product. See console for details.', 'error', { toastInstance: savingToast });
    }

    this.saveProductsToStorage();
    this.filterAndRender();
    this.updateProductStats();
    this.initializeFilters();
  }

  async deleteProduct(id) {
    const confirmed = await showConfirmation('Are you sure you want to delete this product?', 'Delete');
    if (confirmed) {
      const deletingToast = await showToast('Deleting product...', 'info', { duration: 0 });
      try {
        // --- DATABASE INTEGRATION PLACEHOLDER ---

        this.products = this.products.filter((p) => p.id !== id);
        this._createLog('delete', `Deleted product with ID: ${id}`, 'warning');
        this.saveProductsToStorage();
        showToast('Product deleted successfully.', 'success', { toastInstance: deletingToast });
        this.filterAndRender();
        this.updateProductStats()
      } catch (error) {
        console.error("Error deleting product:", error)
        showToast('Failed to delete product.', 'error', { toastInstance: deletingToast });
      }
    }
  }

  updateProductStats() {
    const totalProductsEl = document.getElementById('total-products');
    if (totalProductsEl) totalProductsEl.textContent = this.products.length;

    const lowStockCountEl = document.getElementById('low-stock-count');
    if (lowStockCountEl) lowStockCountEl.textContent = this.products.filter(p => p.stock > 0 && p.stock < 10).length;

    const totalCategoriesEl = document.getElementById('total-categories');
    if (totalCategoriesEl) totalCategoriesEl.textContent = [...new Set(this.products.map(p => p.category))].length;

    const avgPriceEl = document.getElementById('avg-price');
    if (avgPriceEl) {
      const avgPrice = this.products.length > 0 ? this.products.reduce((acc, p) => acc + p.price, 0) / this.products.length : 0;
      avgPriceEl.textContent = `₱${avgPrice.toFixed(2)}`;
    }
  }

  initializeFilters() {
    const categories = [...new Set(this.products.map((p) => p.category))]
    const categoryFilter = document.querySelector("#category-filter")

    if (categoryFilter) {
      const currentValue = categoryFilter.value;
      categoryFilter.innerHTML = '<option value="">All Categories</option>' + categories.map(cat => {
        const catLabel = cat.charAt(0).toUpperCase() + cat.slice(1);
        return `<option value="${cat}">${catLabel}</option>`;
      }).join('');
      categoryFilter.value = currentValue;
    }
  }

  updateBulkActionsVisibility() {
    const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
    const bulkActionsContainer = document.getElementById('bulk-actions');
    const selectedCountSpan = document.querySelector('.selected-count');

    if (bulkActionsContainer && selectedCountSpan) {
      if (checkedBoxes.length > 0) {
        bulkActionsContainer.style.display = 'flex';
        selectedCountSpan.textContent = `${checkedBoxes.length} items selected`;
      } else {
        bulkActionsContainer.style.display = 'none';
      }
    }
  }

  async performBulkAction(action) {
    const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => parseInt(cb.dataset.id));

    if (selectedIds.length === 0) {
      showToast("Please select at least one product.", 'warning');
      return;
    }

    if (action === 'delete') {
      const confirmed = await showConfirmation(`Are you sure you want to delete ${selectedIds.length} products?`, 'Delete');
      if (confirmed) {
        this.products = this.products.filter(p => !selectedIds.includes(p.id));
      } else {
        return; // Abort if user cancels
      }
    } else {
      this.products.forEach(product => {
        // Other actions can be placed here if needed in the future
      });
    }

    this.saveProductsToStorage();
    this.filterAndRender();
    showToast(`${selectedIds.length} products have been deleted.`, 'error');
    this.updateProductStats();

    document.getElementById('select-all').checked = false;
    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = false);
    this.updateBulkActionsVisibility();
  }

  // --- Customization Options ---
  addSizeOption(option = { name: '', image: '', adjustmentType: 'fixed', adjustmentValue: 0 }) {
    const container = document.getElementById('size-options-container');
    const row = document.createElement('div');
    row.classList.add('option-row');
    const imagePreview = option.image ? `<img src="${option.image}" style="width:100%; height:100%; object-fit:cover;">` : '<i class="fas fa-ruler-combined"></i>';

    row.innerHTML = `
      <label class="option-image-upload">
        ${imagePreview}
        <input type="file" class="size-image-input" accept="image/*" style="display:none;">
      </label>
      <input type="number" class="form-input size-adj-value" placeholder="Value" value="${option.adjustmentValue}" step="0.01">
      <select class="form-select size-adj-type">
        <option value="fixed" ${option.adjustmentType === 'fixed' ? 'selected' : ''}>₱</option>
        <option value="percent" ${option.adjustmentType === 'percent' ? 'selected' : ''}>%</option>
      </select>
      <button type="button" class="btn-icon remove-option-btn"><i class="fas fa-trash"></i></button>
    `;
    container.appendChild(row);
    row.querySelector('.remove-option-btn').addEventListener('click', () => row.remove());

    const fileInput = row.querySelector('.size-image-input');
    const imageLabel = row.querySelector('.option-image-upload');
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                imageLabel.innerHTML = `<img src="${event.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
            };
            reader.readAsDataURL(file);
        }
    });
  }

  addColorOption(option = { name: '', adjustmentType: 'fixed', adjustmentValue: 0, image: '' }) {
    const container = document.getElementById('color-options-container');
    const row = document.createElement('div');
    row.classList.add('option-row', 'color-option-row');
    const imagePreview = option.image ? `<img src="${option.image}" style="width:100%; height:100%; object-fit:cover;">` : '<i class="fas fa-image"></i>';

    row.innerHTML = `
      <label class="option-image-upload">
        ${imagePreview}
        <input type="file" class="color-image-input" accept="image/*" style="display:none;">
      </label>
      <input type="number" class="form-input color-adj-value" placeholder="Value" value="${option.adjustmentValue}" step="0.01">
      <select class="form-select color-adj-type">
        <option value="fixed" ${option.adjustmentType === 'fixed' ? 'selected' : ''}>₱</option>
        <option value="percent" ${option.adjustmentType === 'percent' ? 'selected' : ''}>%</option>
      </select>
      <button type="button" class="btn-icon remove-option-btn"><i class="fas fa-trash"></i></button>
    `;
    container.appendChild(row);
    row.querySelector('.remove-option-btn').addEventListener('click', () => row.remove());
    
    const fileInput = row.querySelector('.color-image-input');
    const imageLabel = row.querySelector('.option-image-upload');
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                imageLabel.innerHTML = `<img src="${event.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
            };
            reader.readAsDataURL(file);
        }
    });
  }

  getCustomizationOptions(type) {
    const options = [];
    const containerId = `${type}-options-container`;
    const container = document.getElementById(containerId);
    if (!container) return options;

    container.querySelectorAll('.option-row').forEach(row => {
      const valueInput = row.querySelector(`.${type}-adj-value`);
      const typeInput = row.querySelector(`.${type}-adj-type`);
      const imageEl = row.querySelector('.option-image-upload img');

      if (imageEl) {
        let option = {
          adjustmentValue: parseFloat(valueInput.value) || 0,
          adjustmentType: typeInput.value,
          image: imageEl.src,
          name: imageEl.src.substring(imageEl.src.length - 30) 
        };

        options.push(option);
      }
    });

    return options;
  }

  // --- Image Upload Functionality ---
  setupImageUploadListeners() {
    const uploadArea = document.querySelector(".image-upload-area");
    const fileInput = document.getElementById("product-images");

    if (!uploadArea || !fileInput) return;

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      uploadArea.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
      }, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
      uploadArea.addEventListener(eventName, () => uploadArea.classList.add('highlight'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      uploadArea.addEventListener(eventName, () => uploadArea.classList.remove('highlight'), false);
    });

    uploadArea.addEventListener('drop', (e) => {
      fileInput.files = e.dataTransfer.files;
      this.handleFiles(fileInput.files);
    }, false);

    fileInput.addEventListener('change', (e) => {
      this.handleFiles(e.target.files);
    });
  }

  createImagePreview(src, name) {
    const previewContainer = document.getElementById("image-preview");
    if (previewContainer.children.length === 0) {
        const placeholder = document.querySelector('.upload-placeholder');
        if(placeholder) placeholder.style.display = 'none';
    }

    const previewItem = document.createElement('div');
    previewItem.classList.add('image-preview-item');
    previewItem.innerHTML = `
      <img src="${src}" alt="${name}">
      <button type="button" class="image-preview-remove">&times;</button>
    `;
    previewContainer.appendChild(previewItem);

    previewItem.querySelector('.image-preview-remove').addEventListener('click', () => {
      previewItem.remove();
      if (previewContainer.children.length === 0) {
        const placeholder = document.querySelector('.upload-placeholder');
        if(placeholder) placeholder.style.display = 'block';
      }
    });
  }

  handleFiles(files) {
    const previewContainer = document.getElementById("image-preview");
    if (files.length > 0) {
        document.querySelector('.upload-placeholder').style.display = 'none';
    }

    [...files].forEach(file => {
      if (!file.type.startsWith('image/')){ return }

      const reader = new FileReader();
      reader.onload = (e) => {
        this.createImagePreview(e.target.result, file.name);
      };
      reader.readAsDataURL(file);
    });
  }

  getUploadedImageUrls() {
    const images = [];
    document.querySelectorAll('.image-preview-item img').forEach(img => {
      images.push(img.src);
    });
    return images;
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const currentDateEl = document.getElementById('current-date');
  if (currentDateEl) {
    currentDateEl.textContent = new Date().toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }
  window.productManager = new ProductManager();

})
