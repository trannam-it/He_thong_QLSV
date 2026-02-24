// console.log("MAIN JS LOADED");
// alert("JS OK");

// ===================================
// SIDEBAR TOGGLE FUNCTIONALITY
// ===================================
function initSidebar() {
  const toggleBtn = document.getElementById("toggleSidebar");
  const sidebar = document.querySelector(".sidebar");
  const mainContent = document.querySelector(".main-content");

  if (toggleBtn) {
    toggleBtn.addEventListener("click", function () {
      sidebar.classList.toggle("collapsed");
      mainContent.classList.toggle("expanded");

      // Save state to localStorage
      const isCollapsed = sidebar.classList.contains("collapsed");
      localStorage.setItem("sidebarCollapsed", isCollapsed);
    });
  }

  // Restore sidebar state from localStorage
  const savedState = localStorage.getItem("sidebarCollapsed");
  if (savedState === "true") {
    sidebar.classList.add("collapsed");
    mainContent.classList.add("expanded");
  }

  // Mobile sidebar toggle
  const mobileToggle = document.getElementById("mobileToggleSidebar");
  if (mobileToggle) {
    mobileToggle.addEventListener("click", function () {
      sidebar.classList.toggle("mobile-active");
    });
  }

  // Close sidebar on mobile when clicking outside
  document.addEventListener("click", function (e) {
    if (window.innerWidth <= 768) {
      if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
        sidebar.classList.remove("mobile-active");
      }
    }
  });
}

// ===================================
// MODAL FUNCTIONALITY
// ===================================
function initModals() {
  // Open modal
  document.querySelectorAll("[data-modal-target]").forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const modalId = this.getAttribute("data-modal-target");
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add("active");
      }
    });
  });

  // Close modal
  document.querySelectorAll("[data-modal-close]").forEach((button) => {
    button.addEventListener("click", function () {
      const modal = this.closest(".modal-overlay");
      if (modal) {
        modal.classList.remove("active");
      }
    });
  });

  // Close modal on overlay click
  document.querySelectorAll(".modal-overlay").forEach((overlay) => {
    overlay.addEventListener("click", function (e) {
      if (e.target === this) {
        this.classList.remove("active");
      }
    });
  });
}

// ===================================
// FORM VALIDATION
// ===================================
function validateForm(formId) {
  const form = document.getElementById(formId);
  if (!form) return false;

  let isValid = true;
  const requiredFields = form.querySelectorAll("[required]");

  requiredFields.forEach((field) => {
    if (!field.value.trim()) {
      isValid = false;
      field.classList.add("error");
      showFieldError(field, "Trường này là bắt buộc");
    } else {
      field.classList.remove("error");
      removeFieldError(field);
    }
  });

  return isValid;
}

function showFieldError(field, message) {
  removeFieldError(field);
  const errorDiv = document.createElement("div");
  errorDiv.className = "field-error";
  errorDiv.style.color = "#e74a3b";
  errorDiv.style.fontSize = "0.85rem";
  errorDiv.style.marginTop = "5px";
  errorDiv.textContent = message;
  field.parentNode.appendChild(errorDiv);
}

function removeFieldError(field) {
  const existingError = field.parentNode.querySelector(".field-error");
  if (existingError) {
    existingError.remove();
  }
}

// ===================================
// CONFIRMATION DIALOG
// ===================================
function confirmAction(message, callback) {
  if (confirm(message)) {
    callback();
  }
}

// Delete confirmation
function confirmDelete(id, name, type) {
  const message = `Bạn có chắc chắn muốn xóa ${type} "${name}" không?\nHành động này không thể hoàn tác.`;
  return confirm(message);
}

// ===================================
// ALERT NOTIFICATION
// ===================================
function showAlert(message, type = "success") {
  const alertContainer = document.getElementById("alertContainer");
  if (!alertContainer) return;

  const alert = document.createElement("div");
  alert.className = `alert alert-${type}`;
  alert.innerHTML = `
        <i class="bi bi-${type === "success" ? "check-circle" : "exclamation-triangle"}"></i>
        <span>${message}</span>
    `;

  alertContainer.appendChild(alert);

  // Auto remove after 5 seconds
  setTimeout(() => {
    alert.style.opacity = "0";
    setTimeout(() => alert.remove(), 300);
  }, 5000);
}

// ===================================
// TABLE SEARCH FUNCTIONALITY
// ===================================
function initTableSearch() {
  const searchInput = document.getElementById("tableSearch");
  if (!searchInput) return;

  searchInput.addEventListener("keyup", function () {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll(".data-table tbody tr");

    tableRows.forEach((row) => {
      const text = row.textContent.toLowerCase();
      if (text.includes(searchTerm)) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });
}

// ===================================
// TABLE SORTING
// ===================================
function initTableSort() {
  const headers = document.querySelectorAll(".sortable");

  headers.forEach((header) => {
    header.style.cursor = "pointer";
    header.addEventListener("click", function () {
      const table = this.closest("table");
      const tbody = table.querySelector("tbody");
      const rows = Array.from(tbody.querySelectorAll("tr"));
      const columnIndex = Array.from(this.parentNode.children).indexOf(this);
      const isAscending = this.classList.contains("asc");

      // Remove sorting classes from all headers
      headers.forEach((h) => h.classList.remove("asc", "desc"));

      // Add sorting class to current header
      this.classList.add(isAscending ? "desc" : "asc");

      // Sort rows
      rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();

        if (isAscending) {
          return bValue.localeCompare(aValue);
        } else {
          return aValue.localeCompare(bValue);
        }
      });

      // Append sorted rows
      rows.forEach((row) => tbody.appendChild(row));
    });
  });
}

// ===================================
// DROPDOWN MENU
// ===================================
function initDropdowns() {
  document.querySelectorAll(".dropdown-toggle").forEach((toggle) => {
    toggle.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      // Close other dropdowns
      document.querySelectorAll(".dropdown-menu").forEach((menu) => {
        if (menu !== this.nextElementSibling) {
          menu.classList.remove("active");
        }
      });

      // Toggle current dropdown
      const menu = this.nextElementSibling;
      if (menu && menu.classList.contains("dropdown-menu")) {
        menu.classList.toggle("active");
      }
    });
  });

  // Close dropdowns when clicking outside
  document.addEventListener("click", function () {
    document.querySelectorAll(".dropdown-menu").forEach((menu) => {
      menu.classList.remove("active");
    });
  });
}

// ===================================
// TABS FUNCTIONALITY
// ===================================
function initTabs() {
  document.querySelectorAll(".tab-link").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      // Remove active class from all tabs and contents
      const tabContainer = this.closest(".tabs-container");
      tabContainer
        .querySelectorAll(".tab-link")
        .forEach((l) => l.classList.remove("active"));
      tabContainer
        .querySelectorAll(".tab-content")
        .forEach((c) => c.classList.remove("active"));

      // Add active class to clicked tab and corresponding content
      this.classList.add("active");
      const targetId = this.getAttribute("data-tab");
      const targetContent = document.getElementById(targetId);
      if (targetContent) {
        targetContent.classList.add("active");
      }
    });
  });
}

// ===================================
// FILE UPLOAD PREVIEW
// ===================================
function initFileUpload() {
  document.querySelectorAll('input[type="file"]').forEach((input) => {
    input.addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          const preview = document.getElementById(input.id + "Preview");
          if (preview) {
            preview.src = e.target.result;
            preview.style.display = "block";
          }
        };
        reader.readAsDataURL(file);
      }
    });
  });
}

// ===================================
// AUTO-HIDE ALERTS
// ===================================
function initAutoHideAlerts() {
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.transition = "opacity 0.3s";
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 300);
    }, 5000);
  });
}

// ===================================
// EXPORT TABLE TO EXCEL
// ===================================
function exportTableToExcel(tableId, filename = "export.xls") {
  const table = document.getElementById(tableId);
  if (!table) return;

  const html = table.outerHTML;
  const url = "data:application/vnd.ms-excel," + encodeURIComponent(html);
  const downloadLink = document.createElement("a");
  downloadLink.href = url;
  downloadLink.download = filename;
  downloadLink.click();
}

// ===================================
// PRINT TABLE
// ===================================
function printTable(tableId) {
  const table = document.getElementById(tableId);
  if (!table) return;

  const printWindow = window.open("", "", "height=600,width=800");
  printWindow.document.write("<html><head><title>Print</title>");
  printWindow.document.write(
    "<style>table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:8px;}th{background:#4e73df;color:white;}</style>",
  );
  printWindow.document.write("</head><body>");
  printWindow.document.write(table.outerHTML);
  printWindow.document.write("</body></html>");
  printWindow.document.close();
  printWindow.print();
}

// ===================================
// FORM AUTO-SAVE (Draft)
// ===================================
function initAutoSave(formId) {
  const form = document.getElementById(formId);
  if (!form) return;

  const fields = form.querySelectorAll("input, textarea, select");

  fields.forEach((field) => {
    // Load saved value
    const savedValue = localStorage.getItem(`draft_${formId}_${field.name}`);
    if (savedValue && !field.value) {
      field.value = savedValue;
    }

    // Save on change
    field.addEventListener("input", function () {
      localStorage.setItem(`draft_${formId}_${field.name}`, this.value);
    });
  });

  // Clear draft on submit
  form.addEventListener("submit", function () {
    fields.forEach((field) => {
      localStorage.removeItem(`draft_${formId}_${field.name}`);
    });
  });
}

// ===================================
// CHARACTER COUNTER
// ===================================
function initCharCounter() {
  document.querySelectorAll("[data-max-length]").forEach((field) => {
    const maxLength = field.getAttribute("data-max-length");
    const counter = document.createElement("div");
    counter.className = "char-counter";
    counter.style.fontSize = "0.85rem";
    counter.style.color = "#858796";
    counter.style.marginTop = "5px";
    field.parentNode.appendChild(counter);

    function updateCounter() {
      const remaining = maxLength - field.value.length;
      counter.textContent = `${remaining} ký tự còn lại`;
      counter.style.color = remaining < 10 ? "#e74a3b" : "#858796";
    }

    updateCounter();
    field.addEventListener("input", updateCounter);
  });
}

// ===================================
// INITIALIZE ALL FUNCTIONS ON PAGE LOAD
// ===================================
document.addEventListener("DOMContentLoaded", function () {
  initSidebar();
  initModals();
  initTableSearch();
  initTableSort();
  initDropdowns();
  initTabs();
  initFileUpload();
  initAutoHideAlerts();
  initCharCounter();

  console.log("Student Management System initialized successfully!");
});

// ===================================
// UTILITY FUNCTIONS
// ===================================

// Format date to Vietnamese format
function formatDate(dateString) {
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
}

// Format number with thousand separator
function formatNumber(number) {
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Debounce function
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}
