<?php
$gamingPlaceFlashMessages = array(
    array('registered', 'success', 'Account created'),
    array('pass_login', 'success', 'Welcome back'),
    array('fail_login', 'danger', 'Sign-in issue'),
    array('fail_register', 'danger', 'Registration issue'),
    array('cart_error', 'danger', 'Cart notice'),
    array('checkout_success', 'success', 'Order complete'),
    array('checkout_info', 'info', 'Checkout update')
);
$gamingPlaceProductCount = is_array($products) ? count($products) : 0;
?>
<div class="container-xl page-content">
    <?php foreach ($gamingPlaceFlashMessages as $gamingPlaceFlash): ?>
        <?php $gamingPlaceFlashText = $this->session->flashdata($gamingPlaceFlash[0]); ?>
        <?php if ($gamingPlaceFlashText): ?>
            <div class="store-alert store-alert-<?php echo html_escape($gamingPlaceFlash[1]); ?>" role="status">
                <span><?php echo html_escape($gamingPlaceFlashText); ?></span>
                <button type="button" class="alert-dismiss" aria-label="Dismiss message" data-dismiss-alert>&times;</button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <section class="hero-section" aria-labelledby="hero-title">
        <div class="hero-copy">
            <div class="section-kicker"><span class="kicker-line"></span> THE PLAYER'S DESTINATION</div>
            <h1 id="hero-title">For the love<br>of <span>the game.</span></h1>
            <p class="hero-description">Find your next obsession. From unforgettable stories to all-out action, discover games worth making time for.</p>
            <div class="hero-actions">
                <a class="button button-primary" href="#games">Explore the collection <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                <a class="text-link" href="#why-gamingplace">Why GamingPlace <span aria-hidden="true">↗</span></a>
            </div>
            <div class="hero-micro-proof">
                <span class="proof-icon" aria-hidden="true"><i class="fa-solid fa-gamepad"></i></span>
                <span><strong><?php echo $gamingPlaceProductCount; ?> titles</strong><small>in the current collection</small></span>
                <span class="proof-separator" aria-hidden="true"></span>
                <span><strong>Made for players</strong><small>chosen for the experience</small></span>
            </div>
        </div>

        <div class="hero-visual" aria-label="Featured game artwork">
            <div class="hero-art-glow" aria-hidden="true"></div>
            <div class="hero-art-frame">
                <img src="<?php echo base_url('assets/images/ShadowofMordor.jpg'); ?>" alt="Featured game artwork" fetchpriority="high">
                <div class="hero-art-shade"></div>
                <div class="hero-art-topline"><span class="live-dot"></span> THE SPOTLIGHT</div>
                <div class="hero-art-bottom">
                    <span class="hero-art-label">STEP INTO ANOTHER WORLD</span>
                    <span class="hero-art-title">Your next story<br>starts here.</span>
                </div>
            </div>
            <div class="hero-float-card">
                <div class="hero-float-image">
                    <img src="<?php echo base_url('assets/images/Destiny.jpg'); ?>" alt="" loading="lazy">
                </div>
                <div class="hero-float-copy">
                    <span>YOUR NEXT PICK</span>
                    <strong>Adventure awaits.</strong>
                </div>
                <span class="hero-float-arrow" aria-hidden="true"><i class="fa-solid fa-arrow-up-right-from-square"></i></span>
            </div>
            <div class="hero-index" aria-hidden="true">GP / 001</div>
        </div>
    </section>

    <section class="collection-section" id="games" aria-labelledby="collection-title">
        <div class="section-heading-row">
            <div>
                <div class="section-kicker"><span class="kicker-line"></span> THE COLLECTION</div>
                <h2 class="section-title" id="collection-title">Your next favourite <span>starts here.</span></h2>
                <p class="section-description">A considered collection. A world of possibilities.</p>
            </div>
            <div class="collection-count"><span class="count-number"><?php echo $gamingPlaceProductCount; ?></span><span>games<br>to discover</span></div>
        </div>

        <div class="collection-toolbar">
            <div class="filter-pills" role="group" aria-label="Filter games by price">
                <button type="button" class="filter-pill is-active" data-price-filter="all" aria-pressed="true">All games</button>
                <button type="button" class="filter-pill" data-price-filter="under20" aria-pressed="false">Under $20</button>
                <button type="button" class="filter-pill" data-price-filter="20to40" aria-pressed="false">$20 – $40</button>
                <button type="button" class="filter-pill" data-price-filter="over40" aria-pressed="false">$40+</button>
            </div>
            <div class="collection-controls">
                <label class="search-field" for="gameSearch">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input id="gameSearch" type="search" placeholder="Find a game..." autocomplete="off" aria-label="Search games by title">
                    <span class="search-shortcut" aria-hidden="true">⌕</span>
                </label>
                <label class="sort-field" for="gameSort">
                    <span>Sort</span>
                    <select id="gameSort" aria-label="Sort games">
                        <option value="featured">Featured</option>
                        <option value="price-low">Price: low to high</option>
                        <option value="price-high">Price: high to low</option>
                        <option value="title">Title: A–Z</option>
                    </select>
                </label>
            </div>
        </div>

        <div class="collection-grid" id="gamesGrid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $gamingPlaceIndex => $product): ?>
                    <?php $gamingPlacePrice = (float) $product->price; ?>
                    <article class="game-card-wrap" data-game-title="<?php echo html_escape(strtolower((string) $product->title)); ?>" data-price="<?php echo number_format($gamingPlacePrice, 2, '.', ''); ?>" data-original-order="<?php echo (int) $gamingPlaceIndex; ?>">
                        <div class="game-card">
                            <a class="game-card-image" href="<?php echo base_url('products/details/' . (int) $product->id); ?>" aria-label="View <?php echo html_escape($product->title); ?> details">
                                <img src="<?php echo base_url('assets/images/' . rawurlencode((string) $product->image)); ?>" alt="<?php echo html_escape($product->title); ?>" loading="lazy">
                                <span class="game-image-overlay"></span>
                                <?php if ($gamingPlaceIndex === 0): ?><span class="game-card-badge"><i class="fa-solid fa-star" aria-hidden="true"></i> EDITOR'S PICK</span><?php endif; ?>
                                <span class="game-view-link">View details <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span>
                            </a>
                            <div class="game-card-content">
                                <div class="game-card-topline"><span>GAMINGPLACE SELECT</span><span class="availability-dot" aria-hidden="true"></span></div>
                                <h3 class="game-title"><a href="<?php echo base_url('products/details/' . (int) $product->id); ?>"><?php echo html_escape($product->title); ?></a></h3>
                                <div class="game-card-bottom">
                                    <div class="game-price"><span class="price-currency">$</span><?php echo number_format($gamingPlacePrice, 2); ?></div>
                                    <form method="post" action="<?php echo base_url('cart/add'); ?>" class="add-game-form">
                                        <input type="hidden" name="qty" value="1">
                                        <input type="hidden" name="item_number" value="<?php echo (int) $product->id; ?>">
                                        <button class="add-to-bag" type="submit" aria-label="Add <?php echo html_escape($product->title); ?> to bag"><i class="fa-solid fa-plus" aria-hidden="true"></i><span>Add</span></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-shop">
                    <span class="empty-shop-icon"><i class="fa-solid fa-gamepad" aria-hidden="true"></i></span>
                    <h3>The shelves are quiet.</h3>
                    <p>There are no games to show right now. Check back soon.</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="no-search-results" id="noSearchResults" hidden>
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <h3>No matches this time.</h3>
            <p>Try another title or clear your price filter.</p>
            <button type="button" class="button button-secondary" data-clear-filters>Show all games</button>
        </div>
    </section>

    <section class="experience-section" id="why-gamingplace" aria-labelledby="experience-title">
        <div class="experience-intro">
            <div class="section-kicker"><span class="kicker-line"></span> THE GAMINGPLACE DIFFERENCE</div>
            <h2 class="section-title" id="experience-title">More than a shelf.<br><span>A world to get lost in.</span></h2>
            <p>We keep the experience clear, the collection focused, and the next adventure within reach.</p>
            <a class="text-link" href="#games">Find your next game <span aria-hidden="true">↗</span></a>
        </div>
        <div class="experience-features">
            <article class="experience-feature">
                <span class="feature-number">01</span>
                <span class="feature-icon" aria-hidden="true"><i class="fa-solid fa-compass"></i></span>
                <h3>Explore with intent</h3>
                <p>Find your way through a focused collection without the noise.</p>
            </article>
            <article class="experience-feature">
                <span class="feature-number">02</span>
                <span class="feature-icon" aria-hidden="true"><i class="fa-solid fa-bolt"></i></span>
                <h3>Keep it effortless</h3>
                <p>Clear game details, straightforward pricing, and a simple shopping bag.</p>
            </article>
            <article class="experience-feature">
                <span class="feature-number">03</span>
                <span class="feature-icon" aria-hidden="true"><i class="fa-solid fa-heart"></i></span>
                <h3>Made for players</h3>
                <p>A storefront shaped around the part that matters most: what you play next.</p>
            </article>
        </div>
    </section>

    <section class="closing-cta">
        <div class="closing-cta-pattern" aria-hidden="true"></div>
        <div class="closing-cta-content">
            <span class="section-kicker"><span class="kicker-line"></span> READY PLAYER ONE?</span>
            <h2>Make room for<br><span>one more adventure.</span></h2>
            <a class="button button-light" href="#games">Find your next game <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="closing-cta-mark" aria-hidden="true"><i class="fa-solid fa-gamepad"></i></div>
    </section>
</div>
