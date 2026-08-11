<p><?php echo htmlspecialchars($empty_message); ?></p>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
    <?php foreach ($esports as $esport): ?>
        <div class="col">
            <?php render_esports_card($esport, $card_options); ?>
        </div>
    <?php endforeach; ?>
</div>