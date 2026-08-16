<footer style="border-top: 1px solid var(--border); margin-top: 80px; padding: 30px 0;">
  <div class="container" style="text-align: center;">
    <p class="meta">ByteLog — built for IN2120 Web Programming, 2026</p>
  </div>
</footer>

<script src="/blog-application/js/script.js"></script>
<script>
  // Scroll-reveal: elements fade/slide in as they enter the viewport
  const revealEls = document.querySelectorAll('.reveal');
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });
  revealEls.forEach(el => revealObserver.observe(el));
</script>
</body>
</html>