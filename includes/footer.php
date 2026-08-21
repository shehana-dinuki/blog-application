<footer class="site-footer">

    <div class="footer-container">

        <!-- ByteLog name -->
        <div class="footer-brand">
            <a href="/index.php" class="footer-logo">
                Byte<span>Log</span>
            </a>

            <p>
                A space to share ideas, explore technology,
                and learn something new every day.
            </p>
        </div>


        <!-- Actions -->
        <div class="footer-section">
            <h3>Actions</h3>

            <ul>
                <li>
                    <a href="/index.php">
                        Home
                    </a>
                </li>
                <li>
                    <a href="/dashboard.php">
                        View Blogs
                    </a>
                </li>
                <li>
                    <a href="/create-blog.php">
                        Create Blog
                    </a>
                </li>
            </ul>
        </div>


        <!-- University links -->
        <div class="footer-section">
            <h3>University</h3>

            <ul>
                <li>
                    <a href="https://uom.lk/" target="_blank" rel="noopener noreferrer">
                        University of Moratuwa
                    </a>
                </li>

                <li>
                    <a href="https://uom.lk/itfac" target="_blank" rel="noopener noreferrer">
                        Faculty of Information Technology
                    </a>
                </li>
            </ul>
        </div>

    </div>


    <!-- Footer bottem line -->
    <div class="footer-bottom">

        <p>
            © 2026 ByteLog. All rights reserved.
        </p>

        <p>
            Built for <strong>IN2120 Web Programming</strong>
        </p>

    </div>

</footer>


<script src="/js/script.js"></script>

<script>
    // js script
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