<?php
$gamingPlaceLoggedIn = (bool) $this->session->userdata('logged_in');
$gamingPlaceCartCount = (int) $this->cart->total_items();
$gamingPlaceUsername = (string) $this->session->userdata('username');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0c10">
    <meta name="description" content="Discover standout games and build a collection worth playing. Welcome to GamingPlace.">
    <title>GamingPlace — Play Beyond Ordinary</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/bootstrap.css'); ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/custom.css'); ?>" rel="stylesheet">
</head>
<body class="storefront">
    <a class="skip-link" href="#main-content">Skip to content</a>
    <div class="announcement-bar">
        <span class="announcement-pulse" aria-hidden="true"></span>
        <span>YOUR NEXT GREAT ADVENTURE STARTS HERE</span>
        <span class="announcement-divider" aria-hidden="true"></span>
        <span class="announcement-muted">A STORE BUILT AROUND THE PLAYER</span>
    </div>

    <header class="site-header">
        <div class="container-xl nav-shell">
            <a class="brand" href="<?php echo base_url('products'); ?>" aria-label="GamingPlace home">
                <span class="brand-mark" aria-hidden="true"><i class="fa-solid fa-gamepad"></i></span>
                <span class="brand-copy">
                    <span class="brand-name">GAMING<span>PLACE</span></span>
                    <span class="brand-tagline">PLAY BEYOND ORDINARY</span>
                </span>
            </a>

            <button class="nav-toggle" type="button" data-nav-toggle aria-controls="siteNav" aria-expanded="false" aria-label="Open navigation">
                <span></span><span></span><span></span>
            </button>

            <nav class="primary-nav" id="siteNav" aria-label="Main navigation">
                <a class="nav-link" href="<?php echo base_url('products#games'); ?>">Discover</a>
                <a class="nav-link" href="<?php echo base_url('products#why-gamingplace'); ?>">The experience</a>
                <a class="nav-link nav-link-mobile-cart" href="<?php echo base_url('cart'); ?>">Your bag <span class="nav-count"><?php echo $gamingPlaceCartCount; ?></span></a>
            </nav>

            <div class="nav-actions">
                <?php if ($gamingPlaceLoggedIn): ?>
                    <div class="account-status">
                        <span class="account-status-icon" aria-hidden="true"><i class="fa-regular fa-user"></i></span>
                        <span class="account-status-name"><?php echo html_escape($gamingPlaceUsername); ?></span>
                        <form method="post" action="<?php echo base_url('users/logout'); ?>">
                            <button class="logout-button" type="submit">Sign out</button>
                        </form>
                    </div>
                <?php else: ?>
                    <details class="account-popover">
                        <summary class="account-trigger"><i class="fa-regular fa-user" aria-hidden="true"></i><span>Sign in</span></summary>
                        <div class="account-dropdown">
                            <p class="dropdown-eyebrow">WELCOME BACK</p>
                            <h2>Pick up where you left off.</h2>
                            <form method="post" action="<?php echo base_url('users/login'); ?>" class="login-form">
                                <label for="nav-username">Username</label>
                                <input id="nav-username" name="username" type="text" autocomplete="username" required maxlength="50" placeholder="Your username">
                                <label for="nav-password">Password</label>
                                <input id="nav-password" name="password" type="password" autocomplete="current-password" required maxlength="72" placeholder="Your password">
                                <button type="submit" class="button button-primary button-full">Sign in <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                            </form>
                            <p class="dropdown-register">New to GamingPlace? <a href="<?php echo base_url('users/register'); ?>">Create an account</a></p>
                        </div>
                    </details>
                <?php endif; ?>

                <a class="bag-link" href="<?php echo base_url('cart'); ?>" aria-label="Shopping bag, <?php echo $gamingPlaceCartCount; ?> items">
                    <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                    <span class="bag-label">Bag</span>
                    <span class="bag-count"><?php echo $gamingPlaceCartCount; ?></span>
                </a>
            </div>
        </div>
    </header>

    <main class="site-main" id="main-content">
