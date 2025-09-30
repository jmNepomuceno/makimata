$(document).ready(function () {
    function showToast(message, type = "info") {
        const container = document.getElementById("toast-container");

        const toast = document.createElement("div");
        toast.className = `toast ${type}`;
        toast.innerText = message;

        container.appendChild(toast);

        // Remove after animation ends (4s total)
        setTimeout(() => {
            toast.remove();
        }, 4000);
    }

    $("#signupForm").on("submit", function (e) {
        e.preventDefault();

        let password = $("#password").val();
        let confirmPassword = $("#confirmPassword").val();

        if (password !== confirmPassword) {
            alert("Passwords do not match!");
            return;
        }

        // serialize everything first
        let formData = $(this).serializeArray();

        // replace the location fields with their TEXT instead of VALUE
        let provinceText = $("#province option:selected").text();
        let cityText     = $("#city option:selected").text();
        let barangayText = $("#barangay option:selected").text();
        let regionText   = $("#region option:selected").text();

        formData.push({ name: "province", value: provinceText });
        formData.push({ name: "city", value: cityText });
        formData.push({ name: "barangay", value: barangayText });
        formData.push({ name: "region", value: regionText });

        console.table(formData); // check before sending

        $.ajax({
            url: "../assets/php/add_user.php",
            type: "POST",
            data: $.param(formData), // convert array back into query string
            dataType: "json",
            success: function (response) {
                console.table(response);
                if (response.status === "success") {
                    showToast("✅ Account created successfully!", "success");
                    $("#signupForm")[0].reset();
                } else {
                    showToast("⚠️ Error: " + response.message, "error");
                }
            },
            error: function () {
                alert("Something went wrong. Please try again.");
            }
        });
    });
})