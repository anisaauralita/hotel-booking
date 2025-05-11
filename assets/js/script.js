document.addEventListener("DOMContentLoaded", function () {
  // Aktifkan tooltip Bootstrap
  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Validasi form
  var forms = document.querySelectorAll(".needs-validation");
  Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener(
      "submit",
      function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add("was-validated");
      },
      false
    );
  });

  // Tangani preview gambar upload
  document.querySelectorAll(".img-upload").forEach(function (input) {
    input.addEventListener("change", function (e) {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        var preview = this.nextElementSibling;

        reader.onload = function (e) {
          if (!preview.querySelector("img")) {
            var img = document.createElement("img");
            img.classList.add("img-thumbnail", "mt-2");
            img.style.maxWidth = "200px";
            preview.appendChild(img);
          }
          preview.querySelector("img").src = e.target.result;
        };

        reader.readAsDataURL(this.files[0]);
      }
    });
  });

  // Hitung total harga saat memilih tanggal
  document.querySelectorAll(".calculate-total").forEach(function (input) {
    input.addEventListener("change", function () {
      var form = this.closest("form");
      var pricePerNight = parseFloat(
        form.querySelector(".price-per-night").value
      );
      var checkIn = new Date(form.querySelector("#check_in").value);
      var checkOut = new Date(form.querySelector("#check_out").value);

      if (
        checkIn &&
        checkOut &&
        !isNaN(checkIn.getTime()) &&
        !isNaN(checkOut.getTime())
      ) {
        var nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        var total = nights * pricePerNight;

        var totalElement = form.querySelector(".total-price");
        if (totalElement) {
          totalElement.textContent = "Rp " + total.toLocaleString("id-ID");
        }
      }
    });
  });
});
// Add smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    document.querySelector(this.getAttribute("href")).scrollIntoView({
      behavior: "smooth",
    });
  });
});

// Enhanced form validation feedback
document.querySelectorAll(".needs-validation input").forEach((input) => {
  input.addEventListener("blur", function () {
    if (!this.checkValidity()) {
      this.classList.add("is-invalid");
    } else {
      this.classList.remove("is-invalid");
    }
  });
});

// Modern date picker integration
document.querySelectorAll(".date-input").forEach((input) => {
  input.addEventListener("focus", function () {
    this.type = "date";
  });

  input.addEventListener("blur", function () {
    if (!this.value) this.type = "text";
  });
});
// Add blue theme interactions
document.addEventListener("DOMContentLoaded", function () {
  // Add active state to nav links
  const currentPage = location.pathname.split("/").pop();
  document.querySelectorAll(".nav-link").forEach((link) => {
    if (link.getAttribute("href").includes(currentPage)) {
      link.classList.add("active");
    }
  });

  // Animate buttons on hover
  document.querySelectorAll(".btn").forEach((btn) => {
    btn.addEventListener("mouseenter", () => {
      btn.style.transform = "translateY(-2px)";
    });
    btn.addEventListener("mouseleave", () => {
      btn.style.transform = "translateY(0)";
    });
  });

  // Blue loading animation for forms
  document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", function () {
      const submitBtn = this.querySelector('[type="submit"]');
      if (submitBtn) {
        submitBtn.innerHTML =
          '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Processing...';
        submitBtn.disabled = true;
      }
    });
  });
});
// Dashboard Analytics Chart
document.addEventListener("DOMContentLoaded", function () {
  // Format tanggal di tabel
  document.querySelectorAll(".table td").forEach((cell) => {
    if (cell.textContent.match(/^\d{4}-\d{2}-\d{2}$/)) {
      const date = new Date(cell.textContent);
      cell.textContent = date.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
      });
    }
  });

  // Tooltip untuk stat cards
  document.querySelectorAll(".stat-card").forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-5px)";
    });
    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0)";
    });
  });

  // Date picker default to month range
  const startDate = document.getElementById("start_date");
  const endDate = document.getElementById("end_date");

  if (startDate && endDate) {
    startDate.addEventListener("change", function () {
      if (!endDate.value) {
        const date = new Date(this.value);
        date.setMonth(date.getMonth() + 1);
        date.setDate(0); // Last day of month
        endDate.valueAsDate = date;
      }
    });
  }
});
