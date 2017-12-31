<?php

class bsImageGalleryAdminInterface extends bsDataClass
{
    
    public function bsGalleryList()
    {
        $results = $this->bsFetchData();
        $count = 0;

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Albums List</h1>
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th>Sr</th>
                        <th>Gallery Name</th>
                        <th>ShortCode</th>
                        <th>Template</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($results)){ ?>
                        <?php foreach($results as $res){ ?>
                            <tr>
                                <td><?php echo ++$count ?></td>
                                <td><?php echo $res['gallery_name'] ?></td>
                                <td>[bsiGallery id="<?php echo $res['id'] ?>"]</td>
                                <td></td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=edtbsigallery&gid=' . $res['id']) ?>">
                                        <i class="dashicons dashicons-edit"></i>
                                    </a>
                                    <a href="<?php echo admin_url('admin.php?page=delbsigallery&gid=' . $res['id']) ?>">
                                        <i class="dashicons dashicons-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php
    }   


    public function bsGalleryPanel()
    {
        $page = checkArrayValue($_GET, 'page');
        $label = 'Add';
        if($page == 'edtbsigallery')
        {
            $label = 'Update';
        }

        ?>
        <div class="wrap bsImageGallery">
            <h1 class="wp-heading-inline"><?php echo $label ?> Gallery</h1>
            <form name="ivmGallery" method="post" action="<?php echo admin_url('admin.php?page=addbsigallery') ?>">

                <?php if($page == 'addbsigallery'){ ?>
                    <?php $this->bsAddGalleryForm() ?>
                <?php } else if($page == 'edtbsigallery'){ ?>
                    <?php $this->bsEditGalleryForm() ?>
                <?php } ?>

            </form>
        </div>
        <script>var ajaxurl="<?php echo admin_url('admin-ajax.php') ?>";</script>
        <?php
        wp_enqueue_script('jquery');        /*/ jQuery*/
        wp_enqueue_media();                 /*/ This will enqueue the Media Uploader script  */     
        wp_enqueue_script('ivmScript');
        wp_enqueue_script('galleryFormScript', plugin_dir_url(__FILE__) . 'js/script.js', 'jquery', false, true);
        wp_enqueue_style('galleryFormStyle', plugin_dir_url(__FILE__) . 'css/style.css', '', '0.5.17', 'all');
    }


    public function bsAddGalleryForm()
    {
        $sliderCount = $this->bsGalleryAutoCount();
        ?>
            <div class="col-1">
                <div class="col-3">
                    <label id="galleryName">Gallery Name: </label>
                    <input type="text" name="galleryName" class="newtag form-input-tip ui-autocomplete-input" value="Gallery-<?php echo $sliderCount ?>"/>
                </div>
            
                <div class="col-3">
                    <label class="col-3">Thumbnail: </label>
                    <div class="col-2">
                        <button title="Add Thumbnail" id="thumbnail" class="button button-default upimage" name="thumbnail" type="button">
                            <span class="dashicons dashicons-upload"></span>Upload Thumbnail
                        </button>
                        <input type="hidden" name="thumbnail" size="12" readonly class="form-input-tip readonly" value=""/>
                    </div>
                </div>
            </div>

            <div class="wrap">
                <button title="Upload Image" id="upimage" class="button button-primary multiple alignright upimage" name="upimage" data-rowid="0" type="button">
                    <span class="dashicons dashicons-upload"></span> Upload Image
                </button>
                <button title="Update Gallery" id="svgallery" class="button button-primary button alignright" name="upgallery" type="submit">
                    <span class="dashicons dashicons-update"></span> Save Gallery
                </button>
            </div>

            <table class="wp-list-table widefat fixed striped posts" cellspacing="0" id="ivmGallery">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Image Title</th>
                        <th>Text</th></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="ivmGalleryBody">
                </tbody>
            </table>
            <input type="hidden" name="action" value="uploadivMGallery"/>
            <?php wp_nonce_field('save_ivm_galry', 'glryNonce', true); ?>
        <?php
    }


    public function bsEditGalleryForm()
    {
        $galleryData = '';
        if(checkArrayValue($_GET, 'gid'))
        {
            $galleryData = $this->bsFetchData($_GET['gid']);
        }
        else
        {
            wp_die('Sorry! you seems to be in wrong place.');
        }

        $id = checkArrayValue($galleryData, 'id');
        $galleryName = checkArrayValue($galleryData, 'gallery_name');
        $thumbNail = checkArrayValue($galleryData, 'thumbnail');

        $imageData = unserialize(checkArrayValue($galleryData, 'gallery_data'));
       
        $imgURL = $imageData['img_id'];
        $imgTitle = $imageData['img_title'];
        $imgDesc = $imageData['img_desc'];

        ?>
            <div class="col-1">
                <div class="col-3">
                    <label>Gallery Name: </label>
                    <input type="text" name="galleryName" size="30" class="newtag form-input-tip ui-autocomplete-input" value="<?php echo $galleryName ?>"/>
                </div>

                <div class="col-3">
                    <button title="Add Thumbnail" id="upthumbnail" class="button alignleft button-default upimage" data-field="input[name='thumbnail']" name="upthumbnail" type="button">
                        Set Thumbnail
                    </button>                     
                    <span class="thumb-prev-cont">
                        <img src="" alt="" id="thumbnail-prev" style="max-width:100%;"/>
                    </span>
                    <input type="hidden" name="thumbnail" value="<?php echo $thumbNail ?>"/>
                </div>

                <div class="col-3">
                    <button title="Upload Image" id="upimage" class="button button-primary button multiple alignnone upimage" name="upimage" data-rowid="0" type="button">
                        <span class="dashicons dashicons-upload"></span> Upload Image
                    </button>
                    <button title="Update Gallery" id="upgallery" class="button button-primary button alignnone" name="upgallery" type="submit">
                        <span class="dashicons dashicons-update"></span> Save Gallery
                    </button>
                </div>
            </div>

            <table class="wp-list-table widefat fixed striped posts" cellspacing="0" id="ivmGallery">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Text</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="ivmGalleryBody"><?php pr($imgURL) ?>
                    <?php for($ika=0; $ika < count($imgURL) ; $ika++){ ?>
                        <tr class="connectedSortable" id="bsigrow<?php echo $ika ?>" >
                            <td>
                                <img style="max-width:100px;" src="<?php echo wp_get_attachment_image_src($imgURL[$ika])[0] ?>" class="previmg" id="previmg<?php echo $ika ?>"/>
                                <input type="hidden" class="imageVal" id="image<?php echo $ika ?>" name="image[<?php echo $ika ?>]" value="<?php echo $imgURL[$ika] ?>"/>
                            </td>
                            <td>
                                <input type="text" class="titleVal col-1" id="title<?php echo $ika ?>" name="title[<?php echo $ika ?>]" value="<?php echo $imgTitle[$ika] ?>" placeholder="Title"/>
                                <input type="hidden" id="active[<?php echo $ika ?>]" name="active[<?php echo $ika ?>]" value="<?php echo $imgDesc[$ika] ?>"/>
                                <textarea class="descText col-1" id="descText<?php echo $ika ?>" name="descText[<?php echo $ika ?>]" placeholder="Description"><?php echo $imgDesc[$ika] ?></textarea>                                
                            </td>
                            <td>
                                <!-- Active/Deactive -->
                                <button title="Active/Deactive" class="actDct button" data-rowid="<?php echo $ika ?>" type="button">
                                <span class="dashicons dashicons-yes"></span></button>
                                <!-- Trash -->
                                <button title="Trash Image" class="remRow button" data-rowid="<?php echo $ika ?>" type="button">
                                <span class="dashicons dashicons-trash"></span></button>
                                <!-- Edit -->
                                <button title="Edit Image" class="edtGlryRow button" data-action="edt" data-rowid="<?php echo $ika ?>" type="button">
                                <span class="dashicons dashicons-edit"></span></button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <input type="hidden" name="action" value="uploadivMGallery"/>
            <input type="hidden" name="gkey" value="<?php echo $id ?>"/>
            <?php wp_nonce_field('update_ivm_galry', 'glryNonce', true); ?>
        <?php
    }
}