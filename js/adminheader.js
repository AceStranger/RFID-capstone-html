document.addEventListener("DOMContentLoaded", function() {
    var trigger = document.getElementById('header-content-menu-event');
    var menu = document.querySelector('.header-content-menu-event-menu-list');
    const profileName = document.querySelector('.side-profile-content-item-name');
    const menuList = document.querySelector('.side-profile-content-menu-list');

    
    trigger.addEventListener('mouseover', function() {
        menu.style.display = 'flex';
    });

    trigger.addEventListener('mouseout', function() {
        setTimeout(function() {
            if (!menu.matches(':hover')) {
                menu.style.display = 'none';
            }
        }, 400); // Match the transition duration and make it low
    });

    menu.addEventListener('mouseleave', function() {
        menu.style.display = 'none';
    });
    // Add a click event listener to the profile name element
    profileName.addEventListener('click', function() {
        // Toggle the display property of the menu list element
        if (menuList.style.display === 'none' || menuList.style.display === '') {
            menuList.style.display = 'flex';
        } else {
            menuList.style.display = 'none';
        }
    });
});
