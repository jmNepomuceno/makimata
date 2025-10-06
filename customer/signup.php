<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up</title>
    <link rel="stylesheet" href="signup.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php include("../scripts_links/header_links.php") ?>
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

                <div class="form-group">
                    <label for="confirmPassword"><i class="fa-solid fa-lock"></i> Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Confirm password" />
                </div>

                <!-- Privacy Policy -->
                <div class="terms full-width">
                    <input type="checkbox" id="privacy" name="privacy" required />
                    <label for="privacy">I agree that my information will be used to process my account and keep it secure. <a href="#">Privacy Policy</a></label>
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


    <div id="loadingOverlay">
        <div class="spinner"></div>
        <p>Sending verification email...</p>
    </div>



    <!-- <script src="address.js"></script> -->

    <script src="../assets/js/signup.js"></script>
    <script src="../assets/js/load_locations.js"></script>
</body>
</html>
