<?php
$gamingPlaceCartItems = $this->cart->contents();
$gamingPlaceSubtotal = (float) $this->cart->total();
$gamingPlaceShipping = (float) $this->config->item('shipping');
$gamingPlaceTax = (float) $this->config->item('tax');
$gamingPlaceGrandTotal = $gamingPlaceSubtotal + $gamingPlaceShipping + $gamingPlaceTax;
?>
<div class="container-xl page-content cart-page">
    <?php foreach (array(array('cart_error','danger'), array('checkout_info','info'), array('checkout_success','success')) as $gamingPlaceCartFlash): ?>
        <?php $gamingPlaceCartFlashText = $this->session->flashdata($gamingPlaceCartFlash[0]); ?>
        <?php if ($gamingPlaceCartFlashText): ?>
            <div class="store-alert store-alert-<?php echo html_escape($gamingPlaceCartFlash[1]); ?>" role="status">
                <span><?php echo html_escape($gamingPlaceCartFlashText); ?></span>
                <button type="button" class="alert-dismiss" aria-label="Dismiss message" data-dismiss-alert>&times;</button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo base_url('products'); ?>">Home</a><span aria-hidden="true">/</span><span>Your bag</span>
    </nav>

    <div class="cart-heading">
        <div>
            <div class="section-kicker"><span class="kicker-line"></span> YOUR NEXT ADVENTURE</div>
            <h1 class="page-title">Your <span>shopping bag.</span></h1>
            <p>Everything you picked, all in one place.</p>
        </div>
        <a class="text-link" href="<?php echo base_url('products#games'); ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Keep exploring</a>
    </div>

    <?php if (!empty($gamingPlaceCartItems)): ?>
        <div class="cart-layout">
            <section class="cart-items-panel" aria-labelledby="cart-items-heading">
                <div class="panel-heading">
                    <h2 id="cart-items-heading">Your selection <span class="item-count-pill"><?php echo (int) $this->cart->total_items(); ?></span></h2>
                    <span class="panel-muted">Prices in USD</span>
                </div>

                <form method="post" action="<?php echo base_url('cart/update'); ?>" class="cart-update-form">
                    <?php foreach ($gamingPlaceCartItems as $gamingPlaceItem): ?>
                        <?php $gamingPlaceItemProduct = $this->Product_model->get_product_details((int) $gamingPlaceItem['id']); ?>
                        <article class="cart-line-item">
                            <a class="cart-item-art" href="<?php echo base_url('products/details/' . (int) $gamingPlaceItem['id']); ?>">
                                <?php if ($gamingPlaceItemProduct): ?>
                                    <img src="<?php echo base_url('assets/images/' . rawurlencode((string) $gamingPlaceItemProduct->image)); ?>" alt="<?php echo html_escape($gamingPlaceItem['name']); ?>" loading="lazy">
                                <?php else: ?>
                                    <span class="cart-item-art-fallback"><i class="fa-solid fa-gamepad" aria-hidden="true"></i></span>
                                <?php endif; ?>
                            </a>
                            <div class="cart-item-info">
                                <span class="cart-item-overline">GAMINGPLACE SELECT</span>
                                <h3><a href="<?php echo base_url('products/details/' . (int) $gamingPlaceItem['id']); ?>"><?php echo html_escape($gamingPlaceItem['name']); ?></a></h3>
                                <span class="cart-item-unit-price">$<?php echo number_format((float) $gamingPlaceItem['price'], 2); ?> <span>each</span></span>
                                <span class="cart-item-row-total">Line total: $<?php echo number_format((float) $gamingPlaceItem['subtotal'], 2); ?></span>
                            </div>
                            <div class="cart-item-quantity">
                                <label for="qty-<?php echo html_escape($gamingPlaceItem['rowid']); ?>">Qty</label>
                                <input id="qty-<?php echo html_escape($gamingPlaceItem['rowid']); ?>" type="number" name="qty[<?php echo html_escape($gamingPlaceItem['rowid']); ?>]" value="<?php echo (int) $gamingPlaceItem['qty']; ?>" min="0" max="99" step="1" aria-label="Quantity for <?php echo html_escape($gamingPlaceItem['name']); ?>">
                            </div>
                        </article>
                    <?php endforeach; ?>
                    <div class="cart-update-row">
                        <span>Set quantity to 0 to remove an item.</span>
                        <button class="button button-secondary" type="submit"><i class="fa-solid fa-rotate" aria-hidden="true"></i> Update bag</button>
                    </div>
                </form>
            </section>

            <aside class="cart-summary-panel" aria-labelledby="summary-heading">
                <div class="summary-heading">
                    <span class="summary-overline">THE DETAILS</span>
                    <h2 id="summary-heading">Order summary</h2>
                </div>
                <div class="summary-lines">
                    <div><span>Subtotal</span><strong>$<?php echo number_format($gamingPlaceSubtotal, 2); ?></strong></div>
                    <div><span>Shipping</span><strong>$<?php echo number_format($gamingPlaceShipping, 2); ?></strong></div>
                    <div><span>Tax</span><strong>$<?php echo number_format($gamingPlaceTax, 2); ?></strong></div>
                </div>
                <div class="summary-grand-total"><span>Estimated total</span><strong>$<?php echo number_format($gamingPlaceGrandTotal, 2); ?></strong></div>
                <?php if (!$this->session->userdata('logged_in')): ?>
                    <div class="checkout-notice"><i class="fa-regular fa-user" aria-hidden="true"></i><p>Sign in to continue to checkout, or create an account to get started.</p></div>
                    <a class="button button-primary button-full" href="<?php echo base_url('users/register'); ?>">Create account <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a class="summary-sign-in" href="#main-content" data-open-account>Already have an account? Sign in</a>
                <?php else: ?>
                    <form method="post" action="<?php echo base_url('cart/process'); ?>" class="checkout-form">
                        <?php foreach ($gamingPlaceCartItems as $gamingPlaceItem): ?>
                            <input type="hidden" name="item_code[]" value="<?php echo (int) $gamingPlaceItem['id']; ?>">
                            <input type="hidden" name="item_qty[]" value="<?php echo (int) $gamingPlaceItem['qty']; ?>">
                        <?php endforeach; ?>
                        <div class="checkout-form-heading">Shipping information</div>
                        <label for="checkout-address">Street address</label>
                        <input id="checkout-address" type="text" name="address" required maxlength="255" autocomplete="address-line1" placeholder="Street and building">
                        <label for="checkout-address2">Apartment / unit <span>(optional)</span></label>
                        <input id="checkout-address2" type="text" name="address2" maxlength="255" autocomplete="address-line2" placeholder="Apartment, suite, etc.">
                        <div class="checkout-two-columns">
                            <div><label for="checkout-city">City</label><input id="checkout-city" type="text" name="city" required maxlength="100" autocomplete="address-level2" placeholder="City"></div>
                            <div><label for="checkout-state">State / region</label><input id="checkout-state" type="text" name="state" required maxlength="100" autocomplete="address-level1" placeholder="Region"></div>
                        </div>
                        <div class="checkout-two-columns">
                            <div><label for="checkout-zipcode">Postal code</label><input id="checkout-zipcode" type="text" name="zipcode" required maxlength="20" autocomplete="postal-code" placeholder="Postal code"></div>
                            <div><label for="checkout-country">Country</label>
                                <select id="checkout-country" name="country" required autocomplete="country">
                                    <option value="EG" selected>Egypt</option>
                                    <option value="SA">Saudi Arabia</option>
                                    <option value="AE">United Arab Emirates</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                </select>
                            </div>
                        </div>
                        <button class="button button-primary button-full checkout-submit" type="submit">Continue to payment <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                        <p class="checkout-footnote"><i class="fa-solid fa-lock" aria-hidden="true"></i> Payment is handled through the configured payment provider.</p>
                    </form>
                <?php endif; ?>
                <div class="summary-assurance"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i><span>Review your selection before placing an order.</span></div>
            </aside>
        </div>
    <?php else: ?>
        <section class="empty-cart">
            <span class="empty-cart-icon"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i></span>
            <span class="section-kicker"><span class="kicker-line"></span> NOTHING IN HERE JUST YET</span>
            <h2>Your bag is waiting<br><span>for a new adventure.</span></h2>
            <p>Explore the collection and add a game that belongs in your next session.</p>
            <a class="button button-primary" href="<?php echo base_url('products#games'); ?>">Explore games <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </section>
    <?php endif; ?>
</div>
