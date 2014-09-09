<div class="wrap">
    <h2>Photos Album</h2>
    <form method="post" action="options.php">
        <?php @settings_fields('wp_album_plugin-group'); ?>
        <?php @do_settings_fields('wp_album_plugin-group'); ?>

        <?php do_settings_sections('wp_album_plugin'); ?>

        <?php @submit_button(); ?>
    </form>
</div>