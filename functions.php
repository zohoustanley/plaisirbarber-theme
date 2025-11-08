<?php

function plaisirbarber_setup() {
    // Images mises en avant, etc.
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');

    // Menus
    register_nav_menus([
        'primary' => __('Menu principal', 'plaisirbarber'),
    ]);
}
add_action('after_setup_theme', 'plaisirbarber_setup');

function plaisirbarber_assets() {
    // CSS principal
    wp_enqueue_style(
        'plaisirbarber-style',
        get_stylesheet_uri(),
        [],
        filemtime(get_stylesheet_directory() . '/style.css')
    );

    // JS (optionnel pour le moment)
    wp_enqueue_script(
        'plaisirbarber-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'plaisirbarber_assets');
