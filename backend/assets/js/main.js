/**
 * PHStudio — Public Site Interactions
 * Vanilla JS, no dependencies.
 */
document.addEventListener('DOMContentLoaded', () => {

  // -------------------------------------------------------------
  // Mobile nav toggle
  // -------------------------------------------------------------
  const navToggle = document.getElementById('navToggle');
  const mainNav = document.getElementById('mainNav');
  const headerActions = document.querySelector('.header-actions');

  if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
      mainNav.classList.toggle('open');
      if (headerActions) headerActions.classList.toggle('open');
    });
  }

  // -------------------------------------------------------------
  // Scroll reveal animation
  // -------------------------------------------------------------
  const revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealEls.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    revealEls.forEach(el => observer.observe(el));
  } else {
    revealEls.forEach(el => el.classList.add('is-visible'));
  }

  // -------------------------------------------------------------
  // FAQ accordion
  // -------------------------------------------------------------
  document.querySelectorAll('.faq-item').forEach(item => {
    const question = item.querySelector('.faq-question');
    if (!question) return;
    question.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      item.parentElement.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
      if (!isOpen) item.classList.add('open');
    });
  });

  // -------------------------------------------------------------
  // Portfolio category filter (client-side show/hide)
  // -------------------------------------------------------------
  const filterButtons = document.querySelectorAll('.filter-btn');
  const masonryItems = document.querySelectorAll('.masonry .m-item');
  if (filterButtons.length && masonryItems.length) {
    filterButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        filterButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const filter = btn.dataset.filter;
        masonryItems.forEach(item => {
          const match = filter === 'all' || item.dataset.category === filter;
          item.style.display = match ? '' : 'none';
        });
      });
    });
  }

  // -------------------------------------------------------------
  // Sticky header shadow on scroll
  // -------------------------------------------------------------
  const header = document.getElementById('siteHeader');
  if (header) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 40) header.style.boxShadow = '0 10px 30px rgba(0,0,0,0.35)';
      else header.style.boxShadow = 'none';
    });
  }

  // -------------------------------------------------------------
  // Lightbox for masonry / gallery images
  // -------------------------------------------------------------
  const lightboxTriggers = document.querySelectorAll('[data-lightbox]');
  if (lightboxTriggers.length) {
    const overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    overlay.innerHTML = '<img src="" alt=""><button class="lightbox-close" aria-label="Close">&times;</button>';
    Object.assign(overlay.style, {
      position: 'fixed', inset: '0', background: 'rgba(11,11,12,0.94)',
      display: 'none', alignItems: 'center', justifyContent: 'center',
      zIndex: '999', cursor: 'zoom-out'
    });
    const img = overlay.querySelector('img');
    Object.assign(img.style, { maxWidth: '90%', maxHeight: '86%', borderRadius: '4px', boxShadow: '0 30px 80px rgba(0,0,0,0.6)' });
    const closeBtn = overlay.querySelector('.lightbox-close');
    Object.assign(closeBtn.style, {
      position: 'absolute', top: '24px', right: '34px', fontSize: '36px',
      color: '#fff', background: 'none', border: 'none', cursor: 'pointer'
    });
    document.body.appendChild(overlay);

    lightboxTriggers.forEach(el => {
      el.addEventListener('click', () => {
        const src = el.dataset.lightbox || el.querySelector('img')?.src;
        if (!src) return;
        img.src = src;
        overlay.style.display = 'flex';
      });
    });
    const close = () => { overlay.style.display = 'none'; img.src = ''; };
    overlay.addEventListener('click', close);
    closeBtn.addEventListener('click', close);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
  }
});
