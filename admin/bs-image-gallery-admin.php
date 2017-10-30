<?php 

class _ivmGalleryPanel extends bsImageGalleryAdminInterface
{
    
    var $selectField = '';

    public function addImageVideoAdmin()
    {
        add_menu_page('BS Image Gallery', 'BS Image Gallery', 'activate_plugins', 'ivmgallery', array($this, 'ivmGalleryList'), 'dashicons-format-gallery', '17');
        add_submenu_page('ivmgallery', 'Add - BS Image Gallery', 'Add Album', 'activate_plugins', 'addbsigallery', [$this, 'ivmAddGallery']);
        add_submenu_page('', 'Edit - BS Image Gallery', '', 'activate_plugins', 'edtbsigallery', [$this, 'ivmGalleryPanel']);
        add_action('wp_ajax_uploadivMGallery', 'ivmSaveGallery');
    }


    public function ivmAddGallery()
    {
        $this->ivmSaveGallery();    

        $this->ivmGalleryPanel();
    }
    
}




?>