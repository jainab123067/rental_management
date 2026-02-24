    <!-- Footer -->
    <footer class="footer-landing">
        <div class="container">
            <p>&copy; <?= date('Y') ?> RentalKost. All rights reserved.</p>
            <p class="mt-2"><small>Kost Nyaman & Strategis</small></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-landing');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
            } else {
                navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.05)';
            }
        });
    </script>
</body>
</html>