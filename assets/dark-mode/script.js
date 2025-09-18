$(document).ready(function () {
    const toggleBtn = document.getElementById("darkModeToggle");
    const table = $("#dataTable");

    // === Cek dark mode sebelum DataTables inisialisasi ===
    if (localStorage.getItem("darkMode") === "enabled") {
        enableDarkMode(true);
    }

    // === Toggle Dark Mode Button ===
    toggleBtn.addEventListener("click", () => {
        if (document.body.classList.contains("dark-mode")) {
            disableDarkMode();
        } else {
            enableDarkMode();
        }
    });

    function enableDarkMode(init = false) {
        document.body.classList.add("dark-mode");
        table.addClass("table-dark");
        localStorage.setItem("darkMode", "enabled");
        toggleBtn.textContent = "☀️ ";
        applyDataTableDarkMode(true);
        if (init) table.DataTable().draw(false);
    }

    function disableDarkMode() {
        document.body.classList.remove("dark-mode");
        table.removeClass("table-dark");
        localStorage.setItem("darkMode", "disabled");
        toggleBtn.textContent = "🌙 ";
        applyDataTableDarkMode(false);
    }

    function applyDataTableDarkMode(enable) {
        const wrapper = $(".dataTables_wrapper");
        if (!wrapper.length) return;

        if (enable) {
            wrapper.find(".dataTables_filter input, .dataTables_length select").css({
                "background-color": "#212529",
                "color": "#f8f9fa",
                "border": "1px solid #495057"
            });
            wrapper.find(".page-link").css({
                "background-color": "#212529",
                "color": "#f8f9fa",
                "border": "1px solid #495057"
            });
            wrapper.find(".page-item.active .page-link").css({
                "background-color": "#495057",
                "color": "#fff"
            });
            wrapper.find(".dataTables_info").css({ color: "#dee2e6" });
        } else {
            wrapper.find(".dataTables_filter input, .dataTables_length select").removeAttr("style");
            wrapper.find(".page-link").removeAttr("style");
            wrapper.find(".page-item.active .page-link").removeAttr("style");
            wrapper.find(".dataTables_info").removeAttr("style");
        }
    }
});
