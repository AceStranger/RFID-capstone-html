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
  
    // Attach event listeners for navigation
    Object.entries(sidebarLinks).forEach(([className, url]) => {
      const element = document.querySelector(`.sidebar-${className}`);
      if (element) {
        element.addEventListener('click', () => {
          window.location.href = url;
        });
      }
    });
  
    // Toggle sidebar function that adjusts based on screen width
    function toggleSidebar() {
      const sidebar = document.querySelector('.main-sidebar-content');
      const mainContentContainer = document.querySelector('.main-content-container');
      const toggleButton = document.querySelector('.sidebar-toggle-btn');
      const mainContent = document.querySelector('.main-content');
        
      if (sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
        sidebar.style.right = '0px';
        toggleButton.style.top = '20px';
        toggleButton.style.left = '0px';
        mainContentContainer.style.gridTemplateColumns = '250px 1fr';
        toggleButton.textContent = '<';
      } else {
        toggleButton.style.top = '50%';
        toggleButton.style.left = '0px';
        sidebar.classList.add('active');
        mainContentContainer.style.gridTemplateColumns = '0px 1fr';
        toggleButton.textContent = '>';
      }
    }
  
    // Attach toggle event
    const toggleButton = document.querySelector('.sidebar-toggle-btn');
    toggleButton.addEventListener('click', toggleSidebar);
  
  
    // Optional: Call resize to set initial state on load
    window.dispatchEvent(new Event('resize'));
  });
  