
function toggleRecords(id) {
    var listElement = document.getElementById(id);
    var recordElement = listElement.parentElement;
    var buttonElement = recordElement.querySelector('.event-content-record-ud-btn');

    if (listElement.style.display === 'none') {
        listElement.style.display = 'flex';
        recordElement.style.height = 'auto';
        buttonElement.textContent = '▲';
    } else {
        listElement.style.display = 'none';
        recordElement.style.height = 'auto';
        buttonElement.textContent = '▼';
    }
}
// Get all elements with the class 'record-row-content-activity'
const activityElements = document.querySelectorAll('.record-row-content-activity');

// Loop through each activity element
activityElements.forEach((element) => {
    // Get the text content of the element
    let textContent = element.textContent.trim();

    // Check if the text content exceeds 35 characters
    if (textContent.length > 35) {
        // Shorten the text content to 35 characters and add three dots at the end
        let shortenedText = textContent.substring(0, 35) + '...';

        // Set the shortened text content to the element
        element.textContent = shortenedText;

        // Store the full text content in the 'title' attribute
        element.setAttribute('title', textContent);
    }
});