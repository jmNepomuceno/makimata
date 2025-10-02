$(document).ready(function () {
    function showToast(message, type = "info") {
        const container = $("#toast-container");
        const toast = $("<div>").addClass(`toast ${type}`).text(message);
        container.append(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    $("#loginForm").on("submit", function (e) {
        e.preventDefault();

        // serialize everything first
        let formData = $(this).serializeArray();
        console.table(formData)

        $.ajax({
            url: "../assets/php/login_user.php", // backend PHP
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    // alert("Login successful!");
                    // Redirect to dashboard or homepage
                    window.location.href = "./customer/home.php";
                } else {
                    showToast("⚠️ " + response.message, "error");
                }
            },
            error: function () {
                alert("Something went wrong. Please try again.");
            }
        });
    });
});
