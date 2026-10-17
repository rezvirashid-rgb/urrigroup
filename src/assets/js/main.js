/* =====================================================================
   URRI GROUP — Main behaviour script
   Mobile nav, scroll reveal, contact form (AJAX + pre-fill).
   SOURCE FILE — edit here, then run `php build/build.php`.
   Dependency-free.
   ===================================================================== */
(function () {
  "use strict";

  /* ---------- Mobile navigation ---------- */
  var toggle = document.querySelector(".nav-toggle");
  var nav = document.getElementById("primary-nav");
  if (toggle && nav) {
    var setOpen = function (open) {
      nav.classList.toggle("open", open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
    };
    toggle.addEventListener("click", function () {
      setOpen(!nav.classList.contains("open"));
    });
    nav.querySelectorAll("a").forEach(function (a) {
      a.addEventListener("click", function () { setOpen(false); });
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && nav.classList.contains("open")) {
        setOpen(false);
        toggle.focus();
      }
    });
  }

  /* ---------- Scroll reveal ---------- */
  var revealEls = document.querySelectorAll(".reveal");
  var showAll = function () {
    revealEls.forEach(function (el) { el.classList.add("in"); });
  };
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
      // Fail-safe: never leave content hidden if the observer doesn't fire.
      window.addEventListener("load", function () { setTimeout(showAll, 1500); });
    } else {
      showAll();
    }
  }

  /* ---------- Contact form ---------- */
  var form = document.getElementById("contact-form");
  if (form) {
    var tokenInput = form.querySelector('input[name="csrf_token"]');
    var statusBox = document.querySelector(".form-status");
    var submitBtn = form.querySelector('button[type="submit"]');
    var btnLabel = submitBtn ? submitBtn.querySelector(".btn-label") : null;
    var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var successMsg = "Thank you for contacting Urri Group. Your message has been received and our team will get back to you shortly.";

    // Fetch a CSRF token (the server also accepts same-origin requests without one).
    var tokenUrl = form.getAttribute("data-token-url");
    if (tokenUrl && tokenInput) {
      fetch(tokenUrl, { headers: { "X-Requested-With": "XMLHttpRequest" }, credentials: "same-origin" })
        .then(function (r) { return r.json(); })
        .then(function (res) { if (res && res.token) tokenInput.value = res.token; })
        .catch(function () {});
    }

    // Pre-fill from "Inquire" links: contact.html?area=Real%20Estate&about=Urri%20Real%20Estate
    var params = new URLSearchParams(window.location.search);
    var area = params.get("area");
    var about = params.get("about");
    var areaSelect = form.querySelector('[name="business_area"]');
    if (area && areaSelect) {
      Array.prototype.forEach.call(areaSelect.options, function (opt) {
        if (opt.value === area) areaSelect.value = area;
      });
    }
    var subjectInput = form.querySelector('[name="subject"]');
    if (about && subjectInput && !subjectInput.value && /^Urri [A-Za-z ]{2,30}$/.test(about)) {
      subjectInput.value = "Enquiry about " + about;
    }

    var setError = function (name, msg) {
      var field = form.querySelector('[name="' + name + '"]');
      if (!field) return;
      var errEl = document.getElementById(name + "-error");
      field.setAttribute("aria-invalid", msg ? "true" : "false");
      if (errEl) errEl.textContent = msg || "";
    };

    // Clear a field's error as soon as the visitor edits it.
    form.addEventListener("input", function (e) {
      if (e.target.name) setError(e.target.name, "");
    });

    var validate = function () {
      var ok = true;
      ["name", "email", "business_area", "subject", "message"].forEach(function (n) {
        var el = form.querySelector('[name="' + n + '"]');
        if (el && !el.value.trim()) { setError(n, "This field is required."); ok = false; }
        else setError(n, "");
      });
      var email = form.querySelector('[name="email"]');
      if (email && email.value.trim() && !emailRe.test(email.value.trim())) {
        setError("email", "Please enter a valid email address."); ok = false;
      }
      return ok;
    };

    var showStatus = function (type, msg) {
      if (!statusBox) return;
      statusBox.className = "form-status show " + type;
      statusBox.textContent = msg;
      statusBox.setAttribute("role", type === "error" ? "alert" : "status");
      statusBox.scrollIntoView({ behavior: "smooth", block: "center" });
      statusBox.focus({ preventScroll: true });
    };

    var setBusy = function (busy) {
      if (!submitBtn) return;
      submitBtn.disabled = busy;
      if (btnLabel) btnLabel.textContent = busy ? "Sending…" : "Send Message";
    };

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (!validate()) {
        showStatus("error", "Please correct the highlighted fields and try again.");
        var firstBad = form.querySelector('[aria-invalid="true"]');
        if (firstBad) firstBad.focus();
        return;
      }
      setBusy(true);

      fetch(form.action, {
        method: "POST",
        body: new FormData(form),
        headers: { "X-Requested-With": "XMLHttpRequest" },
        credentials: "same-origin"
      })
        .then(function (r) {
          return r.json().catch(function () { return { success: false, message: "Unexpected server response." }; });
        })
        .then(function (res) {
          // The server rotates the token after each accepted message.
          if (res.token && tokenInput) tokenInput.defaultValue = tokenInput.value = res.token;
          if (res.success) {
            form.reset();
            showStatus("success", res.message || successMsg);
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
        .finally(function () { setBusy(false); });
    });
  }

  /* ---------- Current year in footer ---------- */
  document.querySelectorAll("[data-year]").forEach(function (el) {
    el.textContent = new Date().getFullYear();
  });
})();
