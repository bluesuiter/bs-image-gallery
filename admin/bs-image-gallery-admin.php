<?php

class bsGalleryPanel extends bsImageGalleryAdminInterface {

    var $selectField = '';

    public function addImageVideoAdmin() {
        add_menu_page('BS Image Gallery', 'BS Image Gallery', 'activate_plugins', 'bsgallery', array($this, 'bsGalleryList'), 'dashicons-format-gallery', '17');
        add_submenu_page('bsgallery', 'Add - BS Image Gallery', 'Add Album', 'activate_plugins', 'addbsigallery', [$this, 'bsAddGallery']);
        add_submenu_page('', 'Edit - BS Image Gallery', '', 'activate_plugins', 'edtbsigallery', [$this, 'bsGalleryPanel']);
        add_action('wp_ajax_uploadivMGallery', 'bsSaveGallery');
    }

    public function bsAddGallery() {
        $this->message = $this->bsSaveGallery();
        if ($this->message) {
            ?>
            <script>
                document.cookie = "glryMsg=<?php echo $this->message ?>";
                window.location = "<?php echo admin_url('admin.php?page=bsgallery') ?>";
            </script>
            <?php
        }
    }

}
?>