<div class="container-xl page-content account-page">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo base_url('products'); ?>">Home</a><span aria-hidden="true">/</span><span>Create account</span>
    </nav>

    <section class="account-layout">
        <div class="account-form-panel">
            <div class="section-kicker"><span class="kicker-line"></span> JOIN THE COMMUNITY</div>
            <h1>Create your<br><span>player profile.</span></h1>
            <p class="account-intro">Keep your shopping experience close, and make your next pick with ease.</p>

            <?php echo validation_errors('<div class="store-alert store-alert-danger" role="alert">', '</div>'); ?>
            <?php $gamingPlaceRegisterError = $this->session->flashdata('fail_register'); ?>
            <?php if ($gamingPlaceRegisterError): ?>
                <div class="store-alert store-alert-danger" role="alert"><?php echo html_escape($gamingPlaceRegisterError); ?></div>
            <?php endif; ?>

            <form role="form" method="post" action="<?php echo base_url('users/register'); ?>" class="register-form">
                <div class="form-grid-two">
                    <div class="form-field">
                        <label for="register-first-name">First name</label>
                        <input id="register-first-name" type="text" name="first_name" value="<?php echo html_escape(set_value('first_name')); ?>" autocomplete="given-name" required maxlength="100" placeholder="First name">
                    </div>
                    <div class="form-field">
                        <label for="register-last-name">Last name</label>
                        <input id="register-last-name" type="text" name="last_name" value="<?php echo html_escape(set_value('last_name')); ?>" autocomplete="family-name" required maxlength="100" placeholder="Last name">
                    </div>
                </div>
                <div class="form-field">
                    <label for="register-email">Email address</label>
                    <input id="register-email" type="email" name="email" value="<?php echo html_escape(set_value('email')); ?>" autocomplete="email" required maxlength="190" placeholder="you@example.com">
                </div>
                <div class="form-field">
                    <label for="register-username">Username</label>
                    <input id="register-username" type="text" name="username" value="<?php echo html_escape(set_value('username')); ?>" autocomplete="username" minlength="4" maxlength="50" required placeholder="Choose a username">
                    <small>At least 4 characters.</small>
                </div>
                <div class="form-grid-two">
                    <div class="form-field">
                        <label for="register-password">Password</label>
                        <input id="register-password" type="password" name="password" autocomplete="new-password" minlength="8" maxlength="72" required placeholder="At least 8 characters">
                    </div>
                    <div class="form-field">
                        <label for="register-password-confirm">Confirm password</label>
                        <input id="register-password-confirm" type="password" name="password2" autocomplete="new-password" minlength="8" maxlength="72" required placeholder="Repeat password">
                    </div>
                </div>
                <button type="submit" name="submit" class="button button-primary button-full">Create my account <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                <p class="form-fine-print">By creating an account, you agree to use GamingPlace responsibly.</p>
            </form>
            <p class="account-switch">Already have an account? <a href="<?php echo base_url('products'); ?>" data-open-account>Sign in instead</a></p>
        </div>

        <aside class="account-visual">
            <div class="account-visual-image">
                <img src="<?php echo base_url('assets/images/WatchDogs.jpg'); ?>" alt="" loading="lazy">
                <div class="account-visual-overlay"></div>
            </div>
            <div class="account-visual-copy">
                <span class="account-visual-number">G / P — 001</span>
                <span class="account-visual-kicker">YOUR NEXT CHAPTER</span>
                <h2>Good games.<br>Great stories.<br><span>Your kind of world.</span></h2>
                <p>A little closer to whatever you want to play next.</p>
            </div>
            <div class="account-visual-symbol" aria-hidden="true"><i class="fa-solid fa-gamepad"></i></div>
        </aside>
    </section>
</div>
