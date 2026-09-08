  <footer class="site-footer">
    <div class="container footer-grid">
      <div class="footer-brand">
        <a href="<?= base_url('index.php') ?>" class="brand">PH<span>Studio</span></a>
        <p>Editorial-style photography for the moments that matter most. Based in Metro Manila, available for travel.</p>
        <div class="social-links">
          <a href="#" aria-label="Instagram">IG</a>
          <a href="#" aria-label="Facebook">FB</a>
          <a href="#" aria-label="Pinterest">PIN</a>
        </div>
      </div>

      <div class="footer-col">
        <h4>Explore</h4>
        <a href="<?= base_url('portfolio.php') ?>">Portfolio</a>
        <a href="<?= base_url('packages.php') ?>">Packages &amp; Pricing</a>
        <a href="<?= base_url('about.php') ?>">About the Studio</a>
        <a href="<?= base_url('faq.php') ?>">FAQ</a>
      </div>

      <div class="footer-col">
        <h4>Categories</h4>
        <a href="<?= base_url('portfolio.php?category=weddings') ?>">Weddings</a>
        <a href="<?= base_url('portfolio.php?category=portraits') ?>">Portraits</a>
        <a href="<?= base_url('portfolio.php?category=events') ?>">Events</a>
        <a href="<?= base_url('portfolio.php?category=corporate') ?>">Corporate</a>
      </div>

      <div class="footer-col">
        <h4>Contact</h4>
        <a href="mailto:hello@phstudio.demo">hello@phstudio.demo</a>
        <a href="tel:+639170000000">+63 917 000 0000</a>
        <a href="<?= base_url('contact.php') ?>">Send a Message</a>
      </div>
    </div>

    <div class="container footer-bottom">
      <p>&copy; <?= date('Y') ?> PHStudio. BenDon™ All rights reserved.</p>
      <p class="footer-demo-note">Portfolio Demo — Not a real photography booking service.</p>
    </div>
  </footer>

  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <?= $extraScripts ?? '' ?>
</body>
</html>
