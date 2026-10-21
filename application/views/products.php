<?php
$flashMessages = array(
    array('registered', 'success'),
    array('pass_login', 'success'),
    array('fail_login', 'danger')
);
foreach ($flashMessages as $flash):
    if ($this->session->flashdata($flash[0])): ?>
        <div class="alert alert-<?php echo $flash[1]; ?> alert-dismissible fade show" role="alert">
            <?php echo html_escape($this->session->flashdata($flash[0])); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif;
endforeach;
?>

<div class="shop-toolbar">
    <div>
        <span class="eyebrow">GAME STORE</span>
        <h1>Latest Games</h1>
        <p>Browse the collection and add your favorites to the cart.</p>
    </div>
    <div class="shop-search">
        <input id="gameSearch" type="search" class="form-control" placeholder="Search games..." aria-label="Search games">
    </div>
</div>

<div class="row" id="gamesGrid">
<?php if (!empty($products)): ?>
<?php foreach ($products as $product): ?>
<div class="col-sm-6 col-lg-4 game-card-wrap" data-game-title="<?php echo html_escape(strtolower($product->title)); ?>">
    <article class="game-card">
        <a class="game-image" href="<?php echo base_url('products/details/' . (int)$product->id); ?>">
            <img loading="lazy" src="<?php echo base_url('assets/images/' . rawurlencode($product->image)); ?>" alt="<?php echo html_escape($product->title); ?>">
        </a>
        <div class="game-card-body">
            <div class="game-price">$<?php echo number_format((float)$product->price, 2); ?></div>
            <h2 class="game-title"><?php echo html_escape($product->title); ?></h2>
            <form method="post" action="<?php echo base_url('cart/add'); ?>" class="add-game-form">
                <label class="sr-only" for="qty-<?php echo (int)$product->id; ?>">Quantity</label>
                <input id="qty-<?php echo (int)$product->id; ?>" type="number" name="qty" class="form-control qty" value="1" min="1" max="99" step="1">
                <input type="hidden" name="item_number" value="<?php echo (int)$product->id; ?>">
                <button class="btn btn-primary" type="submit">Add to Cart</button>
            </form>
        </div>
    </article>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="col-12"><div class="empty-shop"><h2>No games available</h2><p>Please check back later.</p></div></div>
<?php endif; ?>
</div>

<script>
(function () {
    var input = document.getElementById('gameSearch');
    if (!input) return;
    input.addEventListener('input', function () {
        var query = this.value.trim().toLowerCase();
        document.querySelectorAll('.game-card-wrap').forEach(function (card) {
            card.style.display = !query || card.dataset.gameTitle.indexOf(query) !== -1 ? '' : 'none';
        });
    });
})();
</script>
