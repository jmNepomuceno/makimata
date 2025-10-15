<?php
// Mikamata Tutorial Module - Admin Interface
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutorials - MIKAMATA Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="tutorials.css">
    <?php include("../scripts_links/header_links.php") ?>
    <style>
        body {
            zoom: 80%;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <img src="mik/logo.png" alt="MIKAMATA Logo" class="admin-logo">
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                    <li><a href="products.php"><i class="fas fa-cubes"></i> <span>Products</span></a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
                    <li><a href="customers.php"><i class="fas fa-user-group"></i> <span>Customers</span></a></li>
                    <li><a href="tutorials.php" class="active"><i class="fas fa-book-open"></i> <span>Tutorials</span></a></li>
                    <li><a href="reviews.php"><i class="fas fa-star"></i> <span>Reviews</span></a></li>
                    <li><a href="notifications.php"><i class="fas fa-bell"></i> <span>Notifications</span></a></li>
                    <li><a href="activity-logs.php"><i class="fas fa-clipboard-list"></i> <span>Activity Logs</span></a></li>
                    <li><a href="admin.php"><i class="fas fa-user-group"></i> <span>Admin</span></a></li>
                </ul>
                <div class="sidebar-bottom-nav">
                    <ul>
                        <li><a href="#" id="admin-logout-btn"><i class="fas fa-right-from-bracket"></i> <span>Logout</span></a></li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main-content">
            <header class="admin-main-header">
                <div>
                    <nav class="breadcrumbs as-title">
                        <a href="dashboard.php">Dashboard</a>
                        <span class="separator">&gt;</span>
                        <span>Tutorials</span>
                    </nav>
                    <p id="current-date"></p>
                </div>
            </header>

            <!-- Stats Cards -->
            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Tutorials</div>
                    <div class="stat-value" id="totalCount">0</div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-label">Pending Review</div>
                    <div class="stat-value" id="pendingCount">0</div>
                </div>
                <div class="stat-card approved">
                    <div class="stat-label">Approved</div>
                    <div class="stat-value" id="approvedCount">0</div>
                </div>
                <div class="stat-card rejected">
                    <div class="stat-label">Rejected</div>
                    <div class="stat-value" id="rejectedCount">0</div>
                </div>
            </section>

            <!-- Filters & Table Section -->
            <section class="admin-section">
                <div class="filters-bar">
                    <div class="filter-group">
                        <button class="filter-btn active" data-filter="all">All</button>
                        <button class="filter-btn" data-filter="pending">Pending</button>
                        <button class="filter-btn" data-filter="approved">Approved</button>
                        <button class="filter-btn" data-filter="rejected">Rejected</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="tutorials-table">
                        <thead>
                            <tr>
                                <th>Sender Name</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Video File</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tutorialsTableBody">
                            <!-- Tutorials will be loaded here by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- Add/Edit Tutorial Modal -->
    <div id="tutorialModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">View Tutorial</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="tutorialForm">
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

                    <!-- User Info Display -->
                    <div class="form-group user-info-display" style="display: none;">
                        <label>Uploaded By</label>
                        <p><strong>Name:</strong> <span id="uploaderName"></span></p>
                        <p><strong>Email:</strong> <span id="uploaderEmail"></span></p>
                    </div>

                    <div id="videoFields" style="display: block;">
                        <div class="form-group">
                            <label for="videoFile">Upload Video File *</label>
                            <input 
                                type="file" 
                                id="videoFile" 
                                name="video_file" 
                                accept="video/mp4,video/webm,video/ogg"
                            >
                            <span class="form-help">Upload the tutorial video. Max size: 50MB.</span>
                            <div id="currentVideo" style="margin-top: 8px;"></div>
                        </div>
                    </div>

                    <div id="articleFields" style="display: none;">
                        <div class="form-group">
                            <label for="articleUrl">Article URL</label>
                            <input 
                                type="url" 
                                id="articleUrl" 
                                name="article_url" 
                                placeholder="https://..."
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tutorialStatus">Status *</label>
                        <select id="tutorialStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="modalCloseBtn" onclick="tutorialAdmin.closeModal()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="tutorials.js"></script>
    <script src="admin.js"></script>
</body>
</html>
