<?php
defined('ABSPATH') or exit;

$currentDuplicateCount = get_option('pmc_duplicate_meta_count', 0);
?>

<div class="wrap">
    <h1>
        <?php echo PMC_PLUGIN_NAME; ?>
    </h1>

    <form method="post" style="margin-top: 30px;padding: 5px 20px 0px 20px;background-color: rgba(0,0,0,.07);">
        <h2 class="title">
            <?php _e('Count Duplicate Meta', 'post-meta-cleaner'); ?>
        </h2>

        <p>
            <?php echo sprintf(__('Last count: %s', 'post-meta-cleaner'), number_format($currentDuplicateCount)); ?>
        </p>

        <input type="hidden" name="count_duplicate_meta" value="true">
        <?php submit_button('Count'); ?>
    </form>

    <form method="post" style="margin-top: 30px;padding: 5px 20px 0px 20px;background-color: rgba(0,0,0,.07);">
        <h2 class="title">
            <?php _e('Delete Duplicate Meta', 'post-meta-cleaner'); ?>
        </h2>

        <p>
            <?php _e('This will delete all duplicate meta from the database.', 'post-meta-cleaner'); ?>
        </p>

        <p>
            <strong>
                <?php _e('This action cannot be undone. Please take a backup first.', 'post-meta-cleaner'); ?>
            </strong>
        </p>

        <input type="hidden" name="delete_duplicate_meta" value="true">

        <?php if ($currentDuplicateCount == 0) { ?>
            <p class="submit">
                <button class="button button-primary" disabled>
                    <?php _e('Delete', 'post-meta-cleaner'); ?>
                </button>
            </p>
        <?php } else { ?>
            <?php submit_button('Delete'); ?>
        <?php } ?>
    </form>
</div>
