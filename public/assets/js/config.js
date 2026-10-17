/* =====================================================================
   URRI GROUP — Frontend configuration (single source of truth)
   ---------------------------------------------------------------------
   Edit ONLY this file to update social links and public contact details
   shown across the website. Backend/database settings live separately in
   /config/config.php (server-side).
   ===================================================================== */
window.URRI_CONFIG = {
  // Public contact details
  email: "admin@urrigroup.com",
  phone: "+8801895610102",
  phoneHref: "+8801895610102",
  website: "https://urrigroup.com",

  // Social media profile URLs.
  // Leave as "#" until the real profile URLs are available.
  social: {
    facebook: "#",
    instagram: "#",
    linkedin: "#"
  }
};

/* Apply configuration to any element that opts in via data attributes:
     data-social="facebook|instagram|linkedin"  -> sets href
     data-config="email|phone|website"          -> sets href + text
   This keeps social/contact links defined in one place only.        */
(function applyConfig() {
  var cfg = window.URRI_CONFIG || {};

  document.querySelectorAll("[data-social]").forEach(function (el) {
    var key = el.getAttribute("data-social");
    var url = cfg.social && cfg.social[key];
    if (url && url !== "#") {
      el.setAttribute("href", url);
      el.removeAttribute("aria-disabled");
    } else {
      // Keep placeholder but mark as not-yet-configured (still focusable label).
      el.setAttribute("href", "#");
    }
  });

  document.querySelectorAll('[data-config="email"]').forEach(function (el) {
    if (!cfg.email) return;
    el.setAttribute("href", "mailto:" + cfg.email);
    if (el.dataset.fill !== "false") el.textContent = cfg.email;
  });
  document.querySelectorAll('[data-config="phone"]').forEach(function (el) {
    if (!cfg.phone) return;
    el.setAttribute("href", "tel:" + (cfg.phoneHref || cfg.phone).replace(/\s+/g, ""));
    if (el.dataset.fill !== "false") el.textContent = cfg.phone;
  });
  document.querySelectorAll('[data-config="website"]').forEach(function (el) {
    if (!cfg.website) return;
    el.setAttribute("href", cfg.website);
    if (el.dataset.fill !== "false") el.textContent = cfg.website.replace(/^https?:\/\//, "");
  });
})();
