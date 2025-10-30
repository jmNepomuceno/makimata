<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up</title>
    <link rel="stylesheet" href="signup.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php include("../scripts_links/header_links.php") ?>

    <style>
        /* Modal Overlay */
        .custom-modal {
            display: none; 
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        /* Modal Box */
        .custom-modal-content {
            background-color: #fff;
            margin: auto;
            border-radius: 8px;
            max-width: 800px;
            width: 90%;
            overflow: hidden;
            animation: fadeIn 0.25s ease;
        }

        /* Header */
        #privacyModal .modal-header {
            background-color: var(--mikamata-green, #4a7a4c);
            color: #fff;
            border-bottom: none;
            padding: 1rem;
        }

        /* Title */
        #privacyModal .modal-title {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Close Button */
        #privacyModal .btn-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
        }

        /* Body */
        #privacyModal .modal-body {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #333;
            padding: 1.5rem;
            background: #f9f9f9;
            max-height: 70vh;
            overflow-y: auto;
        }

        #privacyModal h6 {
            color: var(--mikamata-green, #4a7a4c);
            font-weight: 600;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        /* Footer */
        #privacyModal .modal-footer {
            background-color: #f1f1f1;
            border-top: none;
            padding: 1rem;
            text-align: right;
        }

        #privacyModal .btn-secondary {
            background-color: var(--mikamata-green, #4a7a4c);
            border: none;
            color: #fff;
            padding: 0.5rem 1.25rem;
            border-radius: 4px;
            transition: background 0.2s;
        }

        #privacyModal .btn-secondary:hover {
            background-color: #365c3d;
        }

        .password-rules {
            font-size: 0.9em;
            margin-top: 5px;
            color: #ccc;
        }

        .password-rules .valid {
            color: green;
        }
        .password-rules .invalid {
            color: red;
        }


        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Left Image Column -->
        <div class="left-column">
            <img src="mik/b2.png" alt="Bamboo Decoration" class="side-image" />
            <div class="image-overlay">
                <div class="left-column-text">
                   
                    <p>Local crafts, for the growth of the community.</p>
                </div>
            </div>
        </div>

        <!-- Right Form Column -->
        <div class="right-column">
            <div class="logo">
                <img src="mik/logo.png" alt="Mikamata Logo" />
            </div>

            <h2>Create Your Account</h2>

            <form id="signupForm" class="two-column">
                <!-- First Name -->
                <div class="form-group">
                    <label for="firstname"><i class="fa-solid fa-user"></i> First Name</label>
                    <input type="text" id="firstname" name="firstname" required placeholder="First name" />
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label for="lastname"><i class="fa-solid fa-user"></i> Last Name</label>
                    <input type="text" id="lastname" name="lastname" required placeholder="Last name" />
                </div>

                <!-- Email -->
                <div class="form-group full-width">
                    <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required placeholder="your@email.com" />
                </div>

                <!-- Contact Number -->
                <div class="form-group full-width">
                    <label for="contact"><i class="fa-solid fa-phone"></i> Contact Number</label>
                    <input type="text" id="contact" name="contact" required placeholder="0912-345-6789" />
                </div>

                <!-- Address Section -->
                <div class="form-group full-width">
                    <label for="region"><i class="fa-solid fa-map"></i> Region</label>
                    <select id="region" name="region" required>
                        <option value="">Select Region</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="province"><i class="fa-solid fa-map"></i> Province</label>
                    <select id="province" name="province" required>
                        <option value="">Select Province</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="city"><i class="fa-solid fa-city"></i> Town / City</label>
                    <select id="city" name="city" required>
                        <option value="">Select City</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="barangay"><i class="fa-solid fa-map-pin"></i> Barangay</label>
                    <select id="barangay" name="barangay" required>
                        <option value="">Select Barangay</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="address"><i class="fa-solid fa-home"></i> House No. & Street</label>
                    <input type="text" id="address" name="address" placeholder="House number and street" required />
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required placeholder="Password" />
                </div>

                <div id="passwordRequirements" class="password-rules">
                    <p>Password must contain:</p>
                    <ul>
                        <li id="reqLength" class="invalid">At least 8 characters</li>
                        <li id="reqUpper" class="invalid">An uppercase letter (A–Z)</li>
                        <li id="reqLower" class="invalid">A lowercase letter (a–z)</li>
                        <li id="reqNumber" class="invalid">A number (0–9)</li>
                        <li id="reqSpecial" class="invalid">A special character (!@#$%^&*)</li>
                    </ul>
                </div>

                <div class="form-group">
                    <label for="confirmPassword"><i class="fa-solid fa-lock"></i> Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Confirm password" />
                </div>

                <!-- Privacy Policy Link -->
                <div class="terms full-width">
                    <input type="checkbox" id="privacy" name="privacy" required />
                    <label for="privacy">
                        I agree that my information will be used to process my account and keep it secure.
                        <a href="#" data-bs-toggle="modal" id="openPrivacyModal">Privacy Policy</a>
                    </label>
                </div>

                <!-- CAPTCHA -->
                <div class="captcha full-width">
                    <div class="captcha-box">
                        <input type="checkbox" id="captcha" name="captcha" required />
                        <label for="captcha">I'm not a robot</label>
                        <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="captcha logo">
                    </div>
                </div>

                <!-- Submit -->
                <div class="full-width">
                    <button type="submit" class="signup-btn">Create Account</button>
                </div>
            </form>

            <p class="login-link">Already have an account? <a href="../index.php">Log in</a></p>
        </div>
    </div>


    <div id="toast-container"></div>

    <div id="otpModal" class="custom-modal">
        <div class="custom-modal-content">
            <h2>Verify Your Account</h2>
            <p>Enter the OTP sent to <span id="otpMobile"></span></p>
            <input type="hidden" id="hiddenMobile">
            <input type="text" id="otpCode" maxlength="6" placeholder="Enter OTP">
            
            <!-- Timer display -->
            <p id="otpTimer" style="font-weight:bold; color:#d9534f; margin-top:10px;"></p>

            <div class="modal-actions">
                <button id="verifyOtpBtn">Verify</button>
                <button id="closeOtpBtn">Cancel</button>
                <button id="resendOtpBtn" style="display:none;">Resend OTP</button>
            </div>
            <div id="result"></div>
        </div>
    </div>

    <!-- Modal -->
    <div id="privacyModal" class="custom-modal">
        <div class="custom-modal-content modal-lg">
            <div class="modal-header">
                <h5 class="modal-title">Privacy Policy & About Us</h5>
                <button type="button" class="btn-close" id="closePrivacyModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Welcome to Mikamata!</h6>
                <p>
                    Here, we celebrate the art of bamboo craftsmanship by offering you a one-stop hub where you can design custom bamboo creations, learn about bamboo, and shop from a vibrant marketplace — all while supporting local artisans and discovering how simple it is to bring your bamboo vision to life.
                </p>

                <h6>About Us</h6>
                <p>
                    <strong>MIKAMATA</strong> (Mithiin Kapakanan Makamatan Tagumpas Livelihood and Handicrafts) is a community-based group established in 2011. The group focuses on making handmade bamboo products such as lampshades, mugs, earrings, phone holders, and furniture.
                </p>

                <h6>Privacy Policy</h6>
                <p>
                    Your personal information (such as your name, email, and contact details) will be used solely for processing your account, ensuring secure access, and providing better service. We do not share your data with third parties without consent.
                </p>
                <p>
                    By creating an account, you agree to allow Mikamata to store and use your information responsibly in accordance with this Privacy Policy.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closePrivacyFooter">Close</button>
            </div>
        </div>
    </div>

    <div id="loadingOverlay">
        <div class="spinner"></div>
        <p>Sending verification email...</p>
    </div>



    <!-- <script src="address.js"></script> -->

    <script src="../assets/js/signup.js"></script>
    <script src="../assets/js/load_locations.js"></script>

    <script>
        const privacyModal = document.getElementById('privacyModal');
        const openPrivacyModal = document.getElementById('openPrivacyModal');
        const closePrivacyModal = document.getElementById('closePrivacyModal');
        const closePrivacyFooter = document.getElementById('closePrivacyFooter');

        // Open
        openPrivacyModal.addEventListener('click', e => {
            e.preventDefault();
            privacyModal.style.display = 'flex';
        });

        // Close (header or footer)
        [closePrivacyModal, closePrivacyFooter].forEach(btn => {
            btn.addEventListener('click', () => privacyModal.style.display = 'none');
        });

        // Close when clicking outside
        privacyModal.addEventListener('click', e => {
            if (e.target === privacyModal) {
                privacyModal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
