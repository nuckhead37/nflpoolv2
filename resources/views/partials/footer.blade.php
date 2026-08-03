        <div id='footer-container'>
            <div id='copy'>&copy; {{ now()->year }} Clive & Jim. Why anyone would want to copy this is beyond us....</div>
        </div>
    </div>
<script>
    document.getElementById('mobile-menu-button').addEventListener('click', function () {
        const menu = document.getElementById('header-links');

        menu.classList.toggle('menu-open');

        const isOpen = menu.classList.contains('menu-open');

        this.innerHTML = isOpen ? '✕' : '☰';
        this.setAttribute('aria-expanded', isOpen);
        this.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
    });
</script>
</body>
</html>