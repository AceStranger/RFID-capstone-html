document.getElementById("form").addEventListener("submit", async function (e) {
    e.preventDefault(); // Prevent the default form submission

    // Collect form data
    const username = document.getElementById("log-in-content-input-username").value.trim();
    const password = document.getElementById("log-in-content-input-password").value;

    // Validate client-side if needed
    if (!username || !password) {
        displayMessage("Fill in All Fields", false);
        return;
    }

    // Send the POST request
    try {
        const response = await fetch("LogIn.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
                "log-in-content-input-username": username,
                "log-in-content-input-password": password,
            }),
        });

        if (!response.ok) {
            throw new Error("Network response was not ok");
        }

        const result = await response.json();

        // Handle server response
        if (result.success) {
            displayMessage(result.message, true);

            // Redirect to another page or perform actions on successful login
            setTimeout(() => {
                window.location.href = "studentDashboard.php"; // Replace with the desired page
            }, 2000);
        } else {
            displayMessage(result.message, false);
        }
    } catch (error) {
        displayMessage("An error occurred. Please try again later.", false);
        console.error("Error:", error);
    }
});
function togglePasswordVisibility() {
    const passwordInput = document.getElementById("log-in-content-input-password");
    const toggleIcon = document.querySelector(".toggle-password");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("password-show");
        toggleIcon.classList.add("password-hide"); // Add a class for the "hide" icon style
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("password-hide");
        toggleIcon.classList.add("password-show"); // Revert to the "show" icon style
    }
}

// Function to display messages
function displayMessage(message, isSuccess) {
    const messageElement = document.getElementById("message");
    messageElement.textContent = message;
    messageElement.style.color = isSuccess ? "#80f300" : "#c90000";
}
// Hash change event listener
window.addEventListener('hashchange', function() {
    const url = new URL(window.location.href);
    const fragment = url.hash;

    if (fragment === '#a') {
        window.location.href = "adminLoginPage.html";
    } else {
    }
});