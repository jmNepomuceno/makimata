// document.addEventListener('DOMContentLoaded', () => {
//     const loginForm = document.getElementById('login-form');
//     const errorMessage = document.getElementById('error-message');

//     if (loginForm) {
//         loginForm.addEventListener('submit', (e) => {
//             e.preventDefault(); // Prevent actual form submission

//             const username = document.getElementById('username').value;
//             const password = document.getElementById('password').value;

//             // --- DEMO LOGIN LOGIC ---
//             // In a real application, you would send this to a server via fetch()
//             // For now, we'll use hardcoded credentials.
//             if (username === 'admin' && password === 'password') {
//                 // Simulate successful login
//                 // You can set a session token in localStorage here
//                 localStorage.setItem('admin_logged_in', 'true');
                
//                 // Redirect to the dashboard
//                 window.location.href = 'dashboard.html';
//             } else {
//                 // Show error message
//                 errorMessage.style.display = 'block';
//             }
//         });
//     }
// });
$(document).ready(function () {
    $("#login-form").on("submit", function (e) {
        e.preventDefault();

        let formData = $(this).serializeArray();
        console.table(formData)

        $.ajax({
            url: "../assets/php/admin_login.php", // backend PHP
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                console.table(response)
                if (response.status === "success") {
                    // redirect to admin dashboard
                    window.location.href = "dashboard.php";
                } else {
                    $("#error-message").text(response.message).show();
                }
            },
            error: function () {
                $("#error-message").text("Something went wrong. Please try again.").show();
            }
        });
    });
});

