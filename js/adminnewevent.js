document.addEventListener("DOMContentLoaded", function() {
    const maxDuplicateLimit = 4;
    const originalActivityName = document.querySelector(".create-new-event-content-activity-name");
    const originalTimeInOut = document.querySelector(".create-new-event-content-time-in-out-IO");
    const originalAttendanceDurationIn = document.querySelector(".create-new-event-content-attendance-duration-in");
    const originalAttendanceDurationOut = document.querySelector(".create-new-event-content-attendance-duration-out");
    const parentDiv = document.querySelector(".create-new-event-content-time-in-out");
    let duplicateCount = 0;

    // Function to update the button visibility
    function updateButtonVisibility() {
        const button = document.querySelector(".add-new-time-in-out-button");
        if (button) {
            button.style.display = duplicateCount >= maxDuplicateLimit - 1 ? 'none' : 'block';
        }
    }

    // Event listener for the button click
    document.querySelector(".create-new-event-content").addEventListener("click", function(event) {
        if (event.target && event.target.classList.contains("add-new-time-in-out-button")) {
            if (duplicateCount < maxDuplicateLimit - 1) {
                duplicateCount++;
                // Clone the elements
                const attendanceDurationIn = originalAttendanceDurationIn.cloneNode(true);

                const attendanceDurationOut = originalAttendanceDurationOut.cloneNode(true);

                const activityName = originalActivityName.cloneNode(true);

                const timeInOut = originalTimeInOut.cloneNode(true);

                // Clear input values
                attendanceDurationIn.querySelectorAll("input").forEach(input => input.value = "");
                attendanceDurationOut.querySelectorAll("input").forEach(input => input.value = "");
                activityName.querySelectorAll("input").forEach(input => input.value = "");
                timeInOut.querySelectorAll("input").forEach(input => input.value = "");

                // Append the cloned elements
                parentDiv.appendChild(attendanceDurationIn);
                parentDiv.appendChild(attendanceDurationOut);
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
