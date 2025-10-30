$(document).ready(function () {
    // Show OTP modal
    function showOtpModal(email) {
        openOtpModal()
        $("#otpEmail").text(email);   // make sure your modal has <span id="otpEmail"></span>
        $("#otpModal").css("display", "flex");
    }

    // Hide OTP modal
    function hideOtpModal() {
        $("#otpModal").hide();
    }
    $("#closeOtpBtn").on("click", hideOtpModal);

    // Toast helper
    function showToast(message, type = "info") {
        const container = $("#toast-container");
        const toast = $("<div>").addClass(`toast ${type}`).text(message);
        container.append(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    

    // Signup submit
    $("#signupForm").on("submit", function (e) {
        e.preventDefault();

        let password = $("#password").val();
        let confirmPassword = $("#confirmPassword").val();

        // Check password strength
        const validPassword =
            password.length >= 8 &&
            /[A-Z]/.test(password) &&
            /[a-z]/.test(password) &&
            /\d/.test(password) &&
            /[!@#$%^&*(),.?":{}|<>]/.test(password);

        if (!validPassword) {
            alert("❌ Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.");
            return;
        }

        if (password !== confirmPassword) {
            alert("⚠️ Passwords do not match!");
            return;
        }

        let formData = $(this).serializeArray();
        formData.push({ name: "province", value: $("#province option:selected").text() });
        formData.push({ name: "city", value: $("#city option:selected").text() });
        formData.push({ name: "barangay", value: $("#barangay option:selected").text() });
        formData.push({ name: "region", value: $("#region option:selected").text() });

        // Show loading
        $("#loadingOverlay").fadeIn();

        $.ajax({
            url: "../assets/php/add_user.php",
            type: "POST",
            data: $.param(formData),
            dataType: "json",
            success: function (response) {
                console.log("AJAX Response:", response);

                if (response.status === "pending") {
                    showToast("✅ Account created! OTP sent to your email.", "success");
                    $("#hiddenEmail").val(response.email);
                    showOtpModal(response.email);
                } else {
                    showToast("⚠️ " + response.message, "error");
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", xhr.responseText, status, error);
                showToast("⚠️ Something went wrong. Check console for details.", "error");
            },
            complete: function () {
                $("#loadingOverlay").fadeOut();
            }
        });
    });

    // Verify OTP
    $("#verifyOtpBtn").click(function() {
        let mobile = $("#contact").val().trim();
        let otp = $("#otpCode").val().trim();

        if (!mobile || !otp) {
            $("#result").html("<span style='color:red;'>Both fields are required.</span>");
            return;
        }

        $.ajax({
            url: "../assets/php/verify_otp.php",
            type: "POST",
            data: { mobile: mobile, otp: otp },
            dataType: "json",
            success: function(res) {
                console.log(res)
                if (res.status === "success") {
                    $("#result").html("<span style='color:green;'>" + res.message + "</span>");
                    hideOtpModal();
                    showToast("✅ Your account has been successfully verified!", "success");
                                // redirect after toast disappears
                    setTimeout(() => {
                        location.href = "http://192.168.100.13:8050/";
                    }, 4000); // match showToast removal time
                } else if (res.status === "expired") {
                    $("#result").html("<span style='color:red;'>" + res.message + "</span>");
                    $("#resendOtpBtn").show(); // show resend button
                } else {
                    $("#result").html("<span style='color:red;'>" + res.message + "</span>");
                }
            },
            error: function(xhr, status, error) {
                $("#result").html("<span style='color:red;'>Server error: " + error + "</span>");
            }
        });
    });

    $("#resendOtpBtn").click(function() {
        let mobile = $("#hiddenMobile").val().trim();
        mobile = "09196044820"

        $.ajax({
            url: "../assets/php/resend_otp.php",
            type: "POST",
            data: { mobile: mobile },
            dataType: "json",
            success: function(res) {
                if (res.status === "success") {
                    $("#result").html("<span style='color:green;'>" + res.message + "</span>");
                    $("#resendOtpBtn").hide(); // hide again
                } else {
                    $("#result").html("<span style='color:red;'>" + res.message + "</span>");
                }
            }
        });
    });


    let countdown;
    let timerDuration = 5000; // seconds

    function startOtpTimer() {
        let timeLeft = timerDuration;
        const resendBtn = document.getElementById("resendOtpBtn");
        const timerEl = document.getElementById("otpTimer");

        resendBtn.style.display = "none"; // hide initially
        timerEl.textContent = `Resend available in ${timeLeft}s`;

        countdown = setInterval(() => {
            timeLeft--;
            timerEl.textContent = `Resend available in ${timeLeft}s`;

            if (timeLeft <= 0) {
                clearInterval(countdown);
                timerEl.textContent = "";
                resendBtn.style.display = "inline-block";
            }
        }, 1000);
    }

    // Start timer whenever modal is opened
    function openOtpModal(mobile) {
        document.getElementById("otpMobile").textContent = mobile;
        document.getElementById("hiddenMobile").value = mobile;
        document.getElementById("otpModal").style.display = "block";
        startOtpTimer();
    }

    // Close modal and clear timer
    document.getElementById("closeOtpBtn").addEventListener("click", () => {
        clearInterval(countdown);
        document.getElementById("otpModal").style.display = "none";
        document.getElementById("otpTimer").textContent = "";
    });

    // Restart timer when Resend clicked
    document.getElementById("resendOtpBtn").addEventListener("click", () => {
        clearInterval(countdown);
        startOtpTimer();
        // TODO: Call your resend OTP AJAX here
    });

    // Password live validation
    $("#password").on("input", function () {
        const password = $(this).val();

        // Check conditions
        const lengthOK = password.length >= 8;
        const upperOK = /[A-Z]/.test(password);
        const lowerOK = /[a-z]/.test(password);
        const numberOK = /\d/.test(password);
        const specialOK = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        // Update UI
        $("#reqLength").toggleClass("valid", lengthOK).toggleClass("invalid", !lengthOK);
        $("#reqUpper").toggleClass("valid", upperOK).toggleClass("invalid", !upperOK);
        $("#reqLower").toggleClass("valid", lowerOK).toggleClass("invalid", !lowerOK);
        $("#reqNumber").toggleClass("valid", numberOK).toggleClass("invalid", !numberOK);
        $("#reqSpecial").toggleClass("valid", specialOK).toggleClass("invalid", !specialOK);
    });




    // showOtpModal()

});

// so i installed this right
// composer require phpmailer/phpmailer

// if i just copy and paste the whole program file into another pc or desktop, will it also be copied and implemented on the new device?