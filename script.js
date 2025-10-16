(function router() {
  const routes = Array.from(document.querySelectorAll(".route"));
  const menuLinks = Array.from(document.querySelectorAll(".menu a"));
  function showRoute(hash) {
    const target = (hash || "#home").replace("#", "");
    routes.forEach(sec => sec.hidden = sec.id !== target);
    menuLinks.forEach(a => a.classList.toggle("active", a.getAttribute("href") === `#${target}`));
    const section = document.getElementById(target);
    if (section) section.setAttribute("tabindex", "-1"), section.focus();
  }
  window.addEventListener("hashchange", () => showRoute(location.hash));
  document.addEventListener("DOMContentLoaded", () => showRoute(location.hash || "#home"));
})();

(function mobileNav() {
  const body = document.body;
  const toggle = document.querySelector(".nav-toggle");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.querySelector(".nav-overlay");
  const links = sidebar ? sidebar.querySelectorAll("a[href^='#']") : [];

  if (!toggle || !sidebar || !overlay) return;

  function openNav() {
    body.classList.add("nav-open");
    overlay.hidden = false;
    toggle.setAttribute("aria-expanded", "true");
  }
  function closeNav() {
    body.classList.remove("nav-open");
    overlay.hidden = true;
    toggle.setAttribute("aria-expanded", "false");
    toggle.focus();
  }
  function toggleNav() {
    body.classList.contains("nav-open") ? closeNav() : openNav();
  }

  toggle.addEventListener("click", toggleNav);
  overlay.addEventListener("click", closeNav);


  links.forEach(a => a.addEventListener("click", closeNav));


  window.addEventListener("keydown", e => {
    if (e.key === "Escape" && body.classList.contains("nav-open")) closeNav();
  });

  
  window.addEventListener("hashchange", closeNav);
})();

// Grouped lightbox that supports images and PDFs per artifact card
(function groupedLightbox() {
  const cards = Array.from(document.querySelectorAll(".gallery .card"));
  const lb = document.getElementById("lightbox");
  if (!lb || !cards.length) return;

  const lbImg = lb.querySelector(".lb-img");
  const lbCap = lb.querySelector(".lb-cap");
  const btnClose = lb.querySelector(".lb-close");
  const btnPrev = lb.querySelector(".lb-prev");
  const btnNext = lb.querySelector(".lb-next");

  let slides = [];  // [{type:'image'|'pdf', src:'...'}]
  let idx = 0;

  // Create (or reuse) an iframe element for PDFs
  let lbFrame = lb.querySelector(".lb-frame");
  if (!lbFrame) {
    lbFrame = document.createElement("iframe");
    lbFrame.className = "lb-frame";
    lbFrame.setAttribute("title", "Document viewer");
    lbFrame.setAttribute("frameborder", "0");
    lbFrame.setAttribute("loading", "lazy");
    lbFrame.style.display = "none"; // hidden by default
    lb.insertBefore(lbFrame, lbCap);
  }

  function parseList(el, attr) {
    const raw = (el.getAttribute(attr) || "").trim();
    return raw ? raw.split("|").map(s => s.trim()) : [];
  }

  function openFromCard(card) {
    const srcs = parseList(card, "data-slides");
    const types = parseList(card, "data-types");
    const title = card.getAttribute("data-title") || "";

    slides = srcs.map((src, i) => ({
      type: (types[i] || "image").toLowerCase(),
      src
    }));

    // start on the first slide for that artifact
    idx = 0;
    show(idx, title);
    lb.hidden = false;
    document.body.style.overflow = "hidden";
    btnClose.focus();
  }

  function show(i, title) {
    idx = ((i % slides.length) + slides.length) % slides.length;
    const s = slides[idx];

    if (s.type === "pdf") {
      // Show iframe, hide image
      lbFrame.src = s.src;
      lbFrame.style.display = "block";
      lbImg.src = "";
      lbImg.style.display = "none";
    } else {
      // Show image, hide iframe
      lbImg.src = s.src;
      lbImg.alt = title ? `${title} — slide ${idx + 1}` : "";
      lbImg.style.display = "block";
      lbFrame.src = "";
      lbFrame.style.display = "none";
    }

    lbCap.textContent = title ? `${title} - ${idx + 1} / ${slides.length}` : `${idx + 1} / ${slides.length}`;
  }

  function close() {
    lb.hidden = true;
    lbImg.src = "";
    lbFrame.src = "";
    document.body.style.overflow = "";
  }
  function prev() { show(idx - 1); }
  function next() { show(idx + 1); }

  // Card click opens its grouped slides
  cards.forEach(card => {
    const cover = card.querySelector(".thumb");
    (cover || card).addEventListener("click", () => openFromCard(card));
    (cover || card).setAttribute("tabindex", "0");
    (cover || card).addEventListener("keydown", e => {
      if (e.key === "Enter" || e.key === " ") { e.preventDefault(); openFromCard(card); }
    });
  });

  btnClose.addEventListener("click", (e) => { 
  e.preventDefault(); e.stopPropagation(); 
  close(); 
});
btnPrev.addEventListener("click", (e) => { 
  e.preventDefault(); e.stopPropagation(); 
  prev(); 
});
btnNext.addEventListener("click", (e) => { 
  e.preventDefault(); e.stopPropagation(); 
  next(); 
});

  lb.addEventListener("click", e => { if (e.target === lb) close(); });

  window.addEventListener("keydown", e => {
    if (lb.hidden) return;
    if (e.key === "Escape") close();
    if (e.key === "ArrowLeft") prev();
    if (e.key === "ArrowRight") next();
  });

    // --- Touch / swipe / tap navigation (mobile) ---
  let touchStartX = 0, touchStartY = 0, tracking = false;
  const SWIPE_THRESHOLD = 28; // px of horizontal movement to count as a swipe

  function onTouchStart(e) {
    const t = e.changedTouches[0];
    touchStartX = t.clientX;
    touchStartY = t.clientY;
    tracking = true;
  }

  function onTouchEnd(e) {
    if (!tracking) return;
    tracking = false;
    const t = e.changedTouches[0];
    const dx = t.clientX - touchStartX;
    const dy = t.clientY - touchStartY;
    // prefer horizontal, ignore vertical scrolls
    if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > SWIPE_THRESHOLD) {
      dx < 0 ? next() : prev();
    }
  }

  // capture swipes anywhere on the lightbox overlay
  lb.addEventListener("touchstart", onTouchStart, { passive: true });
  lb.addEventListener("touchend",   onTouchEnd,   { passive: true });

  // quick tap navigation on the image: right half = next, left half = prev
  if (lbImg) {
    lbImg.addEventListener("click", e => {
      if (lb.hidden) return;
      const mid = window.innerWidth / 2;
      (e.clientX > mid) ? next() : prev();
    });
  }
})();