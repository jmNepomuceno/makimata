$(document).ready(function () {
    function showToast(message, type = "info") {
        const container = $("#toast-container");
        const toast = $("<div>").addClass(`toast ${type}`).text(message);
        container.append(toast);
        setTimeout(() => toast.remove(), 4000);
    }
    $("#loginForm").on("submit", function (e) {
        e.preventDefault();

        let formData = $(this).serializeArray();

        $.ajax({
            url: "../assets/php/login_user.php",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                console.log(response)
                if (response.status === "success") {
                    if (response.role === "admin") {
                        window.location.href = "./admin/dashboard.php";
                    } else {
                        window.location.href = "./customer/products.php";
                    }
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
