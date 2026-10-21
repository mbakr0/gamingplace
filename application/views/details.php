<div class="product-detail">
    <div class="product-detail-image">
        <img src="<?php echo base_url('assets/images/' . rawurlencode($product->image)); ?>" alt="<?php echo html_escape($product->title); ?>">
    </div>
    <div class="product-detail-content">
        <span class="eyebrow">GAME DETAILS</span>
        <h1><?php echo html_escape($product->title); ?></h1>
        <div class="details-price">$<?php echo number_format((float)$product->price, 2); ?></div>
        <div class="details-description">
            <?php echo nl2br(html_escape($product->description)); ?>
        </div>
        <form method="post" action="<?php echo base_url('cart/add'); ?>" class="details-buy-form">
            <label for="detail-qty">Quantity</label>
            <input id="detail-qty" type="number" name="qty" class="form-control qty" value="1" min="1" max="99" step="1">
            <input type="hidden" name="item_number" value="<?php echo (int)$product->id; ?>">
            <button class="btn btn-primary" type="submit">Add to Cart</button>
        </form>
        <a class="back-link" href="<?php echo base_url('products'); ?>">&larr; Back to games</a>
    </div>
</div>
