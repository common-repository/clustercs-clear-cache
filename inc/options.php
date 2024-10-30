<div class="wrap">
    <h1>ClusterCS Clear Cache</h1>

    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
            settings_fields(CLUSTER_CS_CLEAR_CACHE::plugin_slug);

            do_settings_sections(CLUSTER_CS_CLEAR_CACHE::plugin_slug);

            submit_button();
        ?>
    </form>
</div>