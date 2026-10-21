    </main>

    <footer class="site-footer">
        <div class="footer-main container-xl">
            <div class="footer-brand-column">
                <a class="brand footer-brand" href="<?php echo base_url('products'); ?>" aria-label="GamingPlace home">
                    <span class="brand-mark" aria-hidden="true"><i class="fa-solid fa-gamepad"></i></span>
                    <span class="brand-copy">
                        <span class="brand-name">GAMING<span>PLACE</span></span>
                        <span class="brand-tagline">PLAY BEYOND ORDINARY</span>
                    </span>
                </a>
                <p>For the stories you get lost in, the worlds you return to, and the next game you cannot wait to play.</p>
            </div>
            <div class="footer-links-column">
                <span class="footer-heading">EXPLORE</span>
                <a href="<?php echo base_url('products#games'); ?>">Game collection</a>
                <a href="<?php echo base_url('products#why-gamingplace'); ?>">Our approach</a>
                <a href="<?php echo base_url('cart'); ?>">Your shopping bag</a>
            </div>
            <div class="footer-links-column">
                <span class="footer-heading">YOUR ACCOUNT</span>
                <?php if ($this->session->userdata('logged_in')): ?>
                    <a href="<?php echo base_url('cart'); ?>">Continue shopping</a>
                    <form method="post" action="<?php echo base_url('users/logout'); ?>">
                        <button class="footer-logout" type="submit">Sign out</button>
                    </form>
                <?php else: ?>
                    <a href="<?php echo base_url('users/register'); ?>">Create an account</a>
                    <a href="<?php echo base_url('products'); ?>">Browse games</a>
                <?php endif; ?>
            </div>
            <div class="footer-note">
                <span class="footer-note-icon" aria-hidden="true"><i class="fa-solid fa-gamepad"></i></span>
                <span>LESS SCROLLING.<br>MORE PLAYING.</span>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container-xl footer-bottom-inner">
                <span>&copy; <?php echo date('Y'); ?> GamingPlace. Made for players.</span>
                <span class="footer-bottom-note"><span class="footer-status-dot" aria-hidden="true"></span> YOUR NEXT WORLD IS WAITING</span>
            </div>
        </div>
    </footer>

    <script src="<?php echo base_url('assets/js/storefront.js'); ?>" defer></script>
</body>
</html>
