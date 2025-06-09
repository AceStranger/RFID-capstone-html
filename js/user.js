document.addEventListener("DOMContentLoaded", function() {
    const mainContentContainer = document.querySelector(".main-content-container");
    console.log("non");
    
    if (mainContentContainer) {
        mainContentContainer.style.display = "flex";
        mainContentContainer.style.flexDirection = "column";
    }
});
