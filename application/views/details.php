<div class="container-xl page-content detail-page">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo base_url('products'); ?>">Home</a><span aria-hidden="true">/</span><a href="<?php echo base_url('products#games'); ?>">Collection</a><span aria-hidden="true">/</span><span><?php echo html_escape($product->title); ?></span>
    </nav>

    <section class="product-detail">
        <div class="product-detail-visual">
            <div class="detail-image-wrap">
                <img src="<?php echo base_url('assets/images/' . rawurlencode((string) $product->image)); ?>" alt="<?php echo html_escape($product->title); ?>" fetchpriority="high">
                <span class="detail-image-label"><span class="availability-dot" aria-hidden="true"></span> GAMINGPLACE SELECT</span>
            </div>
            <div class="detail-visual-note"><i class="fa-solid fa-gamepad" aria-hidden="true"></i><span>One more world to discover.</span></div>
        </div>

        <div class="product-detail-content">
            <div class="section-kicker"><span class="kicker-line"></span> GAME DETAILS</div>
            <span class="detail-overline">YOUR NEXT PLAY</span>
            <h1><?php echo html_escape($product->title); ?></h1>
            <div class="detail-price-row">
                <span class="details-price">$<?php echo number_format((float) $product->price, 2); ?></span>
                <span class="detail-price-caption">Listed price</span>
            </div>
            <div class="details-description">
                <?php if (trim((string) $product->description) !== ''): ?>
                    <?php echo nl2br(html_escape($product->description)); ?>
                <?php else: ?>
                    <p>Discover this title and decide whether it belongs in your next gaming session.</p>
                <?php endif; ?>
            </div>

            <div class="detail-meta-list">
                <div><i class="fa-solid fa-check" aria-hidden="true"></i><span>Clear product details</span></div>
                <div><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i><span>Added directly to your shopping bag</span></div>
            </div>

            <form method="post" action="<?php echo base_url('cart/add'); ?>" class="details-buy-form">
                <input type="hidden" name="item_number" value="<?php echo (int) $product->id; ?>">
                <div class="detail-quantity">
                    <label for="detail-qty">Quantity</label>
                    <input id="detail-qty" type="number" name="qty" class="form-control qty-input" value="1" min="1" max="99" step="1" required>
                </div>
                <button class="button button-primary detail-add-button" type="submit"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i> Add to bag <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
            </form>
            <a class="back-link" href="<?php echo base_url('products#games'); ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to the collection</a>
        </div>
    </section>

    <section class="detail-aftercare">
        <div><i class="fa-solid fa-compass" aria-hidden="true"></i><span><strong>Keep exploring</strong><small>Your next favourite might be one click away.</small></span></div>
        <a class="button button-secondary" href="<?php echo base_url('products#games'); ?>">Browse all games <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </section>
</div>
