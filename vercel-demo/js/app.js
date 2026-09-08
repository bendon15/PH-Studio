/**
 * PHStudio — Vercel Static Demo — Shared App Logic
 * Simulates authentication, role-based access, and formatting helpers
 * using the localStorage store in store.js. No real backend calls.
 * Portfolio Demo — Not a real photography booking service.
 */
const PHApp = (() => {
  const BASE = window.BASE ?? '';

  // ---------------------------------------------------------------
  // Formatting helpers
  // ---------------------------------------------------------------
  function money(n) {
    return '₱' + Number(n || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
  function prettyDate(dateStr, opts) {
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('en-US', opts || { month: 'short', day: 'numeric', year: 'numeric' });
  }
  function prettyTime(timeStr) {
    const [h, m] = timeStr.split(':').map(Number);
    const d = new Date();
    d.setHours(h, m);
    return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
  }
  function statusLabel(s) { return s.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase()); }
  function badgeHtml(status) {
    const map = { pending: 'badge-pending', confirmed: 'badge-confirmed', in_progress: 'badge-progress',
                  completed: 'badge-completed', cancelled: 'badge-cancelled', unpaid: 'badge-pending',
                  partial: 'badge-progress', paid: 'badge-completed', refunded: 'badge-cancelled' };
    return `<span class="badge ${map[status] || ''}">${statusLabel(status)}</span>`;
  }
  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
  }

  // ---------------------------------------------------------------
  // Auth
  // ---------------------------------------------------------------
  function getCurrentUser() {
    const db = PHStore.load();
    if (!db.currentUserId) return null;
    return db.users.find(u => u.id === db.currentUserId) || null;
  }

  function login(email, password) {
    const db = PHStore.load();
    const user = db.users.find(u => u.email.toLowerCase() === email.toLowerCase() && u.password === password);
    if (!user) return { success: false, error: 'Incorrect email or password.' };
    db.currentUserId = user.id;
    PHStore.save(db);
    return { success: true, user };
  }

  function registerClient({ full_name, email, phone, password }) {
    const db = PHStore.load();
    if (db.users.some(u => u.email.toLowerCase() === email.toLowerCase())) {
      return { success: false, error: 'An account with this email already exists.' };
    }
    const id = db.nextIds.user++;
    const user = { id, role: 'client', full_name, email, phone, password };
    db.users.push(user);
    db.currentUserId = id;
    PHStore.save(db);
    return { success: true, user };
  }

  function logout() {
    const db = PHStore.load();
    db.currentUserId = null;
    PHStore.save(db);
  }

  function requireRole(...roles) {
    const user = getCurrentUser();
    if (!user || !roles.includes(user.role)) {
      window.location.href = BASE + 'login.html';
      return null;
    }
    return user;
  }

  // ---------------------------------------------------------------
  // Public nav / footer injection
  // ---------------------------------------------------------------
  function renderHeader(active) {
    const el = document.getElementById('siteHeader');
    if (!el) return;
    const user = getCurrentUser();
    const links = [
      ['index.html', 'Home', 'home'],
      ['portfolio.html', 'Portfolio', 'portfolio'],
      ['packages.html', 'Packages', 'packages'],
      ['about.html', 'About', 'about'],
      ['faq.html', 'FAQ', 'faq'],
      ['contact.html', 'Contact', 'contact'],
    ];
    const navHtml = links.map(([href, label, key]) =>
      `<a href="${BASE}${href}" class="${active === key ? 'active-link' : ''}">${label}</a>`).join('');

    const accountHref = user ? (user.role === 'client' ? 'client-dashboard.html' : 'admin-dashboard.html') : 'login.html';
    const accountLabel = user ? 'My Account' : 'Login';

    el.innerHTML = `
      <div class="demo-notice">Portfolio Demo — Not a real photography booking service.</div>
      <header class="site-header" id="siteHeaderInner">
        <div class="container header-inner">
          <a href="${BASE}index.html" class="brand">PH<span>Studio</span></a>
          <nav class="main-nav" id="mainNav">${navHtml}</nav>
          <div class="header-actions" id="headerActions">
            <a href="${BASE}${accountHref}" class="btn btn-ghost">${accountLabel}</a>
            <a href="${BASE}booking.html" class="btn btn-gold">Book a Session</a>
          </div>
          <button class="nav-toggle" id="navToggle" aria-label="Toggle menu"><span></span><span></span><span></span></button>
        </div>
      </header>`;

    const toggle = document.getElementById('navToggle');
    const nav = document.getElementById('mainNav');
    const actions = document.getElementById('headerActions');
    toggle?.addEventListener('click', () => { nav.classList.toggle('open'); actions.classList.toggle('open'); });
  }

  function renderFooter() {
    const el = document.getElementById('siteFooter');
    if (!el) return;
    el.innerHTML = `
      <footer class="site-footer">
        <div class="container footer-grid">
          <div class="footer-brand">
            <a href="${BASE}index.html" class="brand">PH<span>Studio</span></a>
            <p>Editorial-style photography for the moments that matter most. Based in Metro Manila, available for travel.</p>
            <div class="social-links"><a href="#">IG</a><a href="#">FB</a><a href="#">PIN</a></div>
          </div>
          <div class="footer-col"><h4>Explore</h4>
            <a href="${BASE}portfolio.html">Portfolio</a><a href="${BASE}packages.html">Packages</a>
            <a href="${BASE}about.html">About</a><a href="${BASE}faq.html">FAQ</a></div>
          <div class="footer-col"><h4>Categories</h4>
            <a href="${BASE}portfolio.html?category=weddings">Weddings</a>
            <a href="${BASE}portfolio.html?category=portraits">Portraits</a>
            <a href="${BASE}portfolio.html?category=events">Events</a>
            <a href="${BASE}portfolio.html?category=corporate">Corporate</a></div>
          <div class="footer-col"><h4>Contact</h4>
            <a href="mailto:hello@phstudio.demo">hello@phstudio.demo</a>
            <a href="tel:+639170000000">+63 917 000 0000</a>
            <a href="${BASE}contact.html">Send a Message</a></div>
        </div>
        <div class="container footer-bottom">
          <p>&copy; ${new Date().getFullYear()} PHStudio BenDon™. All rights reserved.</p>
          <p class="footer-demo-note">Portfolio Demo — Not a real photography booking service.</p>
        </div>
      </footer>`;
  }

  // ---------------------------------------------------------------
  // Dashboard sidebar (used by dashboard pages with #dashSidebar)
  // ---------------------------------------------------------------
  function renderDashboardSidebar(active) {
    const el = document.getElementById('dashSidebar');
    if (!el) return;
    const user = getCurrentUser();
    if (!user) return;
    const isAdmin = user.role === 'admin' || user.role === 'photographer';
    const initials = user.full_name.split(' ').map(p => p[0]).join('').slice(0, 2).toUpperCase();

    const clientLinks = [
      ['client-dashboard.html', '📊 Dashboard', 'dashboard'],
      ['book-session.html', '📅 Book a Session', 'book'],
      ['my-bookings.html', '🗂️ My Bookings', 'bookings'],
      ['gallery.html', '🖼️ My Galleries', 'galleries'],
      ['invoices.html', '🧾 Invoices', 'invoices'],
    ];
    const adminLinks = [
      ['admin-dashboard.html', '📊 Dashboard', 'dashboard'],
      ['admin-bookings.html', '📅 Bookings', 'bookings'],
      ['admin-galleries.html', '🖼️ Galleries', 'galleries'],
    ];
    const links = isAdmin ? adminLinks : clientLinks;
    const navHtml = links.map(([href, label, key]) =>
      `<a href="${BASE}${href}" class="${active === key ? 'active' : ''}">${label}</a>`).join('');

    el.innerHTML = `
      <a href="${BASE}index.html" class="dash-brand">PH<span>Studio</span></a>
      <span class="dash-role-tag">${isAdmin ? (user.role[0].toUpperCase() + user.role.slice(1)) + ' Panel' : 'Client Portal'}</span>
      <nav class="dash-nav">${navHtml}</nav>
      <div class="dash-user-box">
        <div class="initials">${initials}</div>
        <div><div class="name">${escapeHtml(user.full_name)}</div><a href="#" id="logoutLink" class="logout">Log out</a></div>
      </div>`;

    document.getElementById('logoutLink')?.addEventListener('click', (e) => {
      e.preventDefault();
      logout();
      window.location.href = BASE + 'index.html';
    });
  }

  // ---------------------------------------------------------------
  // UI interactions (scroll reveal, FAQ accordion, lightbox, filters)
  // ---------------------------------------------------------------
  function initUI() {
    const revealEls = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window && revealEls.length) {
      const obs = new IntersectionObserver((entries) => {
        entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('is-visible'); obs.unobserve(en.target); } });
      }, { threshold: 0.15 });
      revealEls.forEach(el => obs.observe(el));
    } else {
      revealEls.forEach(el => el.classList.add('is-visible'));
    }

    document.querySelectorAll('.faq-item').forEach(item => {
      item.querySelector('.faq-question')?.addEventListener('click', () => {
        const isOpen = item.classList.contains('open');
        item.parentElement.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
        if (!isOpen) item.classList.add('open');
      });
    });

    const filterButtons = document.querySelectorAll('.filter-btn');
    const items = document.querySelectorAll('.masonry .m-item');
    filterButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        filterButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const filter = btn.dataset.filter;
        items.forEach(item => { item.style.display = (filter === 'all' || item.dataset.category === filter) ? '' : 'none'; });
      });
    });
  }

  return {
    money, prettyDate, prettyTime, statusLabel, badgeHtml, escapeHtml,
    getCurrentUser, login, registerClient, logout, requireRole,
    renderHeader, renderFooter, renderDashboardSidebar, initUI,
  };
})();

document.addEventListener('DOMContentLoaded', () => PHApp.initUI());
