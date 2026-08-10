<dt class="col-5">Category</dt>
<dd class="col-7">
    <?php echo htmlspecialchars((string) ($esports["category_name"] ?? "")); ?>
</dd>

<dt class="col-5">Score</dt>
<dd class="col-7">
    <?php echo htmlspecialchars((string) ($esports["score"] ?? "")); ?>
</dd>

<dt class="col-5">Type</dt>
<dd class="col-7">
    <?php echo htmlspecialchars((string) ($esports["type"] ?? "")); ?>
</dd>

<div class="d-flex flex-wrap gap-2 mt-auto">

    <a
        class="btn btn-primary"
        href="<?php echo project_url(
            "esports.php?id=" . (int) $esports["id"]
        ); ?>">
        View
    </a>

    <?php if (has_role("Admin")): ?>

        <a
            class="btn btn-warning"
            href="<?php echo project_url(
                "admin/edit_esports.php?id=" . (int) $esports["id"]
            ); ?>">
            Edit
        </a>

        <form
            method="post"
            action="<?php echo project_url(
                "admin/delete_esports.php?id=" . (int) $esports["id"]
                . "&return_to=esports.php"
            ); ?>">

            <?php
            render_button([
                "text" => "Delete",
                "variant" => "danger",
            ]);
            ?>

        </form>

    <?php endif; ?>

</div>