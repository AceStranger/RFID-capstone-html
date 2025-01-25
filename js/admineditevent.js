document.addEventListener("DOMContentLoaded", function() {
    const maxDuplicateLimit = 4;
    const originalActivityName = document.querySelector(".edit-event-content-activity-name");
    const originalTimeInOut = document.querySelector(".edit-event-content-time-in-out-IO");
    const parentDiv = document.querySelector(".edit-event-content-time-in-out");
    let duplicateCount = 0;

    // Function to update the button visibility
    function updateButtonVisibility() {
        const button = document.querySelector(".add-new-time-in-out-button");
        if (button) {
            button.style.display = duplicateCount >= maxDuplicateLimit - 1 ? 'none' : 'block';
        }
    }

    // Event listener for the button click
    document.querySelector(".edit-event-content").addEventListener("click", function(event) {
        if (event.target && event.target.classList.contains("add-new-time-in-out-button")) {
            if (duplicateCount < maxDuplicateLimit - 1) {
                duplicateCount++;
                // Clone the elements
                const activityName = originalActivityName.cloneNode(true);
                const timeInOut = originalTimeInOut.cloneNode(true);

                // Clear input values
                activityName.querySelectorAll("input").forEach(input => input.value = "");
                timeInOut.querySelectorAll("input").forEach(input => input.value = "");

                // Append the cloned elements
                parentDiv.appendChild(activityName);
                parentDiv.appendChild(timeInOut);

                // Update button visibility
                updateButtonVisibility();
            }
        }
    });

    // Initial state: remove or hide the button if duplicates are at the limit
    updateButtonVisibility();
});
