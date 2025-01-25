const userfn = document.querySelector('.side-profile-content-name');
const userpfp = document.querySelector('.side-profile-content-avatar');
const menuListContainer = document.querySelector('.side-profile-content-user-menu-list-container');

// Add click event listeners after the elements are defined
userfn.addEventListener('click', toggleUserMenu);
userpfp.addEventListener('click', toggleUserMenu);

// Function to toggle the user menu list container
function toggleUserMenu() {
    // Toggle the display style of the user menu list container
    if (menuListContainer.style.display === 'flex') {
        menuListContainer.style.display = 'none';
    } else {
        menuListContainer.style.display = 'flex';
    }
}