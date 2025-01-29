document.addEventListener("DOMContentLoaded", function () {
    const sidebarLinks = {
        dashboard: "adminDashboardPage.php",
        user: "adminUserPage.php?users-account-content-filter-select-role=all&users-account-content-filter-select-col=none&users-account-content-search-input=&filterSubmit=Search",
        officer: "adminOfficerPage.php",
        dean: "adminDeanPage.php",
        student: "adminStudentPage.php",
        event: "adminEventPage.php",
        organization: "adminOrganizationPage.php",
        program: "adminProgramPage.php",
        department: "adminDepartmentPage.php",
        organizationData: "adminOrganizationData.php",
        sanction: "adminSanctionPage.php",
        report: "adminReportPage.php",
        rfid: "adminRFIDPage.php",
        activityLog: "adminActivityLogPage.php",
        logout: "LogOut.php"
    };

    // Attach event listeners if elements exist
    for (const [className, url] of Object.entries(sidebarLinks)) {
        const element = document.querySelector(`.sidebar-${className}`);
        if (element) {
            element.addEventListener('click', function () {
                window.location.href = url;
            });
        }
    }
    function adjustGrid() {
        const mainSidebarContent = document.querySelector('.main-sidebar-content');
      
        if (!mainSidebarContent) {
          const mainContentContainer = document.querySelector('.main-content-container');
          mainContentContainer.style.gridTemplateAreas = '"main"';
          mainContentContainer.style.gridTemplateColumns = '1fr';
        }
      }
      
      // Call the function to adjust the grid on page load
      adjustGrid();
});


