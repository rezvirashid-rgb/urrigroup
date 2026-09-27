/* =====================================================================
   URRI GROUP — Main behaviour script
   Mobile nav, scroll reveal, company filter, contact form (AJAX).
   Kept dependency-free and small.
   ===================================================================== */
(function () {
  "use strict";

  /* ---------- Mobile navigation ---------- */
  var toggle = document.querySelector(".nav-toggle");
  var nav = document.getElementById("primary-nav");
  if (toggle && nav) {
    toggle.addEventListener("click", function () {
      var open = nav.classList.toggle("open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
    // Close menu when a link is clicked (mobile)
    nav.querySelectorAll("a").forEach(function (a) {
      a.addEventListener("click", function () {
        nav.classList.remove("open");
        toggle.setAttribute("aria-expanded", "false");
      });
    });
    // Close on Escape
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && nav.classList.contains("open")) {
        nav.classList.remove("open");
        toggle.setAttribute("aria-expanded", "false");
        toggle.focus();
      }
    });
  }

  /* ---------- Scroll reveal (IntersectionObserver) ---------- */
  var revealEls = document.querySelectorAll(".reveal");
  if (revealEls.length) {
    if ("IntersectionObserver" in window && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("in");
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: "0px 0px -40px 0px" });
      revealEls.forEach(function (el) { io.observe(el); });
      // Fail-safe: never leave content hidden if the observer doesn't fire
      // (e.g. very fast scroll, background tab, or headless render).
      window.addEventListener("load", function () {
        setTimeout(function () {
          revealEls.forEach(function (el) { el.classList.add("in"); });
        }, 1500);
      });
    } else {
      revealEls.forEach(function (el) { el.classList.add("in"); });
    }
  }

  /* ---------- Company filter (companies page) ---------- */
  var filterBar = document.querySelector("[data-filter-bar]");
  if (filterBar) {
    var cards = Array.prototype.slice.call(document.querySelectorAll("[data-pillar]"));
    var countEl = document.querySelector("[data-result-count]");
    filterBar.addEventListener("click", function (e) {
      var btn = e.target.closest(".filter-btn");
      if (!btn) return;
      filterBar.querySelectorAll(".filter-btn").forEach(function (b) { b.setAttribute("aria-pressed", "false"); });
      btn.setAttribute("aria-pressed", "true");
      var f = btn.getAttribute("data-filter");
      var shown = 0;
      cards.forEach(function (card) {
        var match = f === "all" || card.getAttribute("data-pillar") === f;
        card.style.display = match ? "" : "none";
        if (match) shown++;
      });
      if (countEl) countEl.textContent = shown;
    });
  }

  /* ---------- Contact form (progressive enhancement via fetch) ---------- */
  var form = document.getElementById("contact-form");
  if (form) {
    // Fetch a CSRF token from the backend and store it in the hidden field.
    var tokenUrl = form.getAttribute("data-token-url");
    var tokenInput = form.querySelector('input[name="csrf_token"]');
    if (tokenUrl && tokenInput) {
      fetch(tokenUrl, { headers: { "X-Requested-With": "XMLHttpRequest" }, credentials: "same-origin" })
        .then(function (r) { return r.json(); })
        .then(function (res) { if (res && res.token) tokenInput.value = res.token; })
        .catch(function () { /* Non-fatal: server also verifies request origin. */ });
    }

    var statusBox = form.querySelector(".form-status");
    var submitBtn = form.querySelector('button[type="submit"]');
    var btnLabel = submitBtn ? submitBtn.querySelector(".btn-label") : null;

    var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function setError(name, msg) {
      var field = form.querySelector('[name="' + name + '"]');
      if (!field) return;
      var box = field.closest(".field");
      var errEl = box ? box.querySelector(".error-msg") : null;
      field.setAttribute("aria-invalid", msg ? "true" : "false");
      if (errEl) errEl.textContent = msg || "";
    }

    function validate() {
      var ok = true;
      var required = ["name", "email", "business_area", "subject", "message"];
      required.forEach(function (n) {
        var el = form.querySelector('[name="' + n + '"]');
        if (el && !el.value.trim()) { setError(n, "This field is required."); ok = false; }
        else setError(n, "");
      });
      var email = form.querySelector('[name="email"]');
      if (email && email.value.trim() && !emailRe.test(email.value.trim())) {
        setError("email", "Please enter a valid email address."); ok = false;
      }
      return ok;
    }

    function showStatus(type, msg) {
      if (!statusBox) return;
      statusBox.className = "form-status show " + type;
      statusBox.textContent = msg;
      statusBox.setAttribute("role", type === "error" ? "alert" : "status");
      statusBox.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (!validate()) {
        showStatus("error", "Please correct the highlighted fields and try again.");
        return;
      }

      if (submitBtn) {
        submitBtn.disabled = true;
        if (btnLabel) btnLabel.textContent = "Sending…";
      }

      var data = new FormData(form);
      fetch(form.action, {
        method: "POST",
        body: data,
        headers: { "X-Requested-With": "XMLHttpRequest" }
      })
        .then(function (r) { return r.json().catch(function () { return { success: false, message: "Unexpected server response." }; }); })
        .then(function (res) {
          if (res.success) {
            form.reset();
            showStatus("success", res.message || "Thank you for contacting Urri Group. Your message has been received and our team will get back to you shortly.");
          } else {
            if (res.errors) {
              Object.keys(res.errors).forEach(function (k) { setError(k, res.errors[k]); });
            }
            showStatus("error", res.message || "Sorry, something went wrong. Please try again or email us directly.");
          }
        })
        .catch(function () {
          showStatus("error", "Network error. Please check your connection or email us directly at admin@urrigroup.com.");
        })
        .finally(function () {
          if (submitBtn) {
            submitBtn.disabled = false;
            if (btnLabel) btnLabel.textContent = "Send Message";
          }
        });
    });
  }

  /* ---------- Current year in footer ---------- */
  document.querySelectorAll("[data-year]").forEach(function (el) {
    el.textContent = new Date().getFullYear();
  });
})();
