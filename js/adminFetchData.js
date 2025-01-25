document.addEventListener("DOMContentLoaded", function () {
    const departmentSelect = document.getElementById("department");
    const programSelect = document.getElementById("program");
    const yearSelect = document.getElementById("year");
    const sectionSelect = document.getElementById("section");

    // Fetch departments on page load
    fetch('fetchDepartments.php')
        .then(response => response.json())
        .then(departments => {
            departments.forEach(dept => {
                const option = document.createElement("option");
                option.value = dept.department_id;
                option.textContent = dept.department_name;
                departmentSelect.appendChild(option);
            });
        });

    // Fetch programs based on department selection
    departmentSelect.addEventListener("change", function () {
        const departmentId = this.value;

        // Clear program, year, and section options
        programSelect.innerHTML = '<option value="" disabled selected>Select Program</option>';
        yearSelect.innerHTML = '<option value="" disabled selected>Select Year/Grade Level</option>';
        sectionSelect.innerHTML = '<option value="" disabled selected>Select Section</option>';

        fetch(`fetchPrograms.php?department_id=${departmentId}`)
            .then(response => response.json())
            .then(programs => {
                programs.forEach(program => {
                    const option = document.createElement("option");
                    option.value = program.program_id;
                    option.textContent = program.program_name;
                    option.dataset.years = program.program_year_grade_level;
                    option.dataset.sections = program.section;
                    programSelect.appendChild(option);
                });
            });
    });

    // Populate year and section based on program selection
    programSelect.addEventListener("change", function () {
        const selectedOption = programSelect.options[programSelect.selectedIndex];
        const years = selectedOption.dataset.years.split(",");
        const sections = selectedOption.dataset.sections.split(",");

        yearSelect.innerHTML = '<option value="" disabled selected>Select Year/Grade Level</option>';
        sections.forEach(year => {
            const option = document.createElement("option");
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        });

        sectionSelect.innerHTML = '<option value="" disabled selected>Select Section</option>';
        sections.forEach(section => {
            const option = document.createElement("option");
            option.value = section;
            option.textContent = section;
            sectionSelect.appendChild(option);
        });
    });
});
