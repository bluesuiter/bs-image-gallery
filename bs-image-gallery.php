<?php

/*
  Plugin Name: BS Image Gallery
  Plugin URI:
  Description: Free image gallery, with lot of customization possibilities.
  Author: BlueSuiter's
  Version: Beta 1.0
  Author URI:
 */

/* / Stop direct access of the file */
if (!defined('ABSPATH')) {
    die();
}

if (file_exists(dirname(__FILE__) . '/bs-image-gallery-helper.php')) {
    require_once(dirname(__FILE__) . '/bs-image-gallery-helper.php');
}

_bsigLodFile(dirname(__FILE__) . '/data/bs-data-class.php');
_bsigLodFile(dirname(__FILE__) . 'bs-image-gallery-template.php');



if (!class_exists('ImageVideoMetaController') && !class_exists('ImageVideoMetaController') && _bsigLodFile(dirname(__FILE__) . "/bs-image-gallery-controller.php")) {
    _bsigLodFile(dirname(__FILE__) . '/bs-image-gallery-controller.php');

    $objImageVideoMetaController = new ImageVideoMetaController();
    add_action('add_meta_boxes', [$objImageVideoMetaController, 'addImageVideoMetaField']);
    add_action('save_post', [$objImageVideoMetaController, 'saveImageVideoMetaFields'], 1, 2);
    add_shortcode('bsImageGallery', [$objImageVideoMetaController, 'showVideo']);
}


_bsigLodFile(dirname(__FILE__) . '/admin/bs-image-gallery-admin-interface.php');
if (_bsigLodFile(plugin_dir_path(__FILE__) . '/admin/bs-image-gallery-admin.php')) {
    $objGalleryPanel = new bsGalleryPanel();
    add_action('admin_menu', array($objGalleryPanel, 'addImageVideoAdmin'));
}

function applyGalleryFilter() {
    $gallery_shortcode = '[bsImageGallery id="' . intval($post->ID) . '"]';
}

apply_filters('the_content', 'applyGalleryFilter');

function enqueueFancyBox() {
    global $wp_scripts;

    wp_enqueue_style('fancybox-css', plugin_dir_url(__FILE__) . 'css/colorbox.css', false, '1.6.4', 'all');
    wp_enqueue_script('fancybox-js', plugin_dir_url(__FILE__) . 'js/jquery.colorbox-min.js', array('jquery'), '1.6.4', false);
    wp_enqueue_style('bxslider-css', plugin_dir_url(__FILE__) . 'bxslider/jquery.bxslider.min.css', false, '4.2.12', 'all');
    wp_enqueue_script('bxslider-js', plugin_dir_url(__FILE__) . 'bxslider/jquery.bxslider.min.js', array('jquery'), '4.2.12', false);
}

add_filter('wp_enqueue_scripts', 'enqueueFancyBox');

function bsActivatePlugin() {
    $objbsDataClass = new bsDataClass();
    $objbsDataClass->bsGalleryTable();
}

register_activation_hook(__FILE__, 'bsActivatePlugin');


if (!class_exists('bsImageGalleryTemplate') && _bsigLodFile(dirname(__FILE__) . "/bs-image-gallery-template.php")) {
    /* ShortCode */
    $objImageGalleryTemplate = new bsImageGalleryTemplate();
    add_shortcode('bsiGallery', [$objImageGalleryTemplate, 'bisServeShortcode']);
}