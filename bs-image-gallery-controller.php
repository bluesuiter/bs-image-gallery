<?php

class ImageVideoMetaController
{

    public function addImageVideoMetaField()
    {
        add_meta_box('imageVideoMetaField', 'Image Video Meta Field', [$this, 'imageVideoMetaField'], ['post', 'page'], 'normal', 'default');
    }

    public function imageVideoMetaField()
    {
        global $post;
        $ika = 0;
        $postID = get_the_ID();

        $bs_gallery_column = get_post_meta($postID, '_bs_gallery_column', true);

        $imgLink = isset($bs_gallery_column['gallery_image_file']) ? $bs_gallery_column['gallery_image_file'] : '';
        $imgVideo = isset($bs_gallery_column['gallery_image_video']) ? $bs_gallery_column['gallery_image_video'] : '';
        $imgText = isset($bs_gallery_column['gallery_image_text']) ? $bs_gallery_column['gallery_image_text'] : '';
        $imgActive = isset($bs_gallery_column['gallery_image_active']) ? $bs_gallery_column['gallery_image_active'] : '';
        ?>

        <?php
        /* / Noncename needed to verify where the data originated */
        echo '<input type="hidden" name="servicemeta_noncename" id="servicemeta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
        $strFile = get_post_meta($post->ID, $key = 'gallery_image_file', true);
        $media_file = get_post_meta($post->ID, $key = '_wp_attached_file', true);
        if (!empty($media_file))
        {
            $strFile = $media_file;
        }
        ?>

        <div id="meta-box">
            <p>
                <label><strong>Gallery ID::</strong></label>
                <input type="text" name="sliderId" value="[bsImageGallery id='<?php echo get_the_ID(); ?>']" readonly/>
                <button class="upload_image_button" type="button">Upload Image</button>
            </p>
            <table class="gallery_container" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th><label><strong>Video</strong></label></th>
                        <th><label><strong>Title</strong></label></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < max(count($imgLink), count($imgVideo), count($imgText)); $i++)
                    {
                        $img = isset($imgLink[$i]) ? $imgLink[$i] : '';
                        $vid = isset($imgVideo[$i]) ? $imgVideo[$i] : '';
                        $txt = isset($imgText[$i]) ? stripslashes($imgText[$i]) : '';
                        $act = isset($imgActive[$i]) ? $imgActive[$i] : '';
                        ?>
                        <tr class="gallery_item">
                            <td class="imageCol">
                                <img src="<?php echo $img; ?>" class="prevImage"/>
                                <input id="gallery_image_file<?php echo $i; ?>" class="gallery_image_file" type="hidden" name="gallery_image_file[]" value="<?php echo $img; ?>" />
                                <input type="hidden" class="img_txt_id" name="img_txt_id[]" value="" />
                            </td> 
                            <td>
                                <input type="text" name="gallery_image_video[]" class="gallery_image_video" id="gallery_image_video<?php echo $i; ?>" placeholder="Video-Link" value="<?php echo $vid; ?>" />
                            </td>
                            <td>
                                <input type="text" name="gallery_image_text[]" class="gallery_image_text" id="gallery_image_text<?php echo $i; ?>" placeholder="Title" value="<?php echo $txt; ?>"/>
                            </td>
                            <td style="text-align:center;">
                                <input name="gallery_image_active[<?php echo $i; ?>]" class="gallery_image_active" id="gallery_image_active<?php echo $i; ?>" value="1" <?= ($act == '1' ? 'checked' : '') ?> type="checkbox"/>
                                <i class="dashicons dashicons-trash trashIt"></i>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        wp_enqueue_script('imageVideoMetaJs', plugin_dir_url(__FILE__) . '/js/script.js', 'jquery');
        wp_enqueue_style('imageVideoMetaStyle', plugin_dir_url(__FILE__) . '/css/style.css');
    }

    //
    /* /Saving the file */

    public function saveImageVideoMetaFields($post_id, $post)
    {
        /* / verify this came from the our screen and with proper authorization, */
        /* / because save_post can be triggered at other times */
        if (isset($_POST['servicemeta_noncename']) && !wp_verify_nonce($_POST['servicemeta_noncename'], plugin_basename(__FILE__)))
        {
            return $post->ID;
        }

        /* / Is the user allowed to edit the post? */
        if (!current_user_can('edit_post', $post->ID))
        {
            return $post->ID;
        }

        /* / We need to find and save the data */
        /* / We'll put it into an array to make it easier to loop though. */
        $gallery_data['gallery_image_file'] = isset($_POST['gallery_image_file']) ? $_POST['gallery_image_file'] : '';
        $gallery_data['gallery_image_video'] = isset($_POST['gallery_image_video']) ? $_POST['gallery_image_video'] : '';
        $gallery_data['gallery_image_text'] = isset($_POST['gallery_image_text']) ? $_POST['gallery_image_text'] : '';
        $gallery_data['gallery_image_active'] = isset($_POST['gallery_image_active']) ? $_POST['gallery_image_active'] : '';

        /* / Add values of $gallery_data as custom fields */
        if ($post->post_type == 'revision')
        {
            return;
        }

        $key = '_bs_gallery_column';
        $value = $gallery_data;

        if (get_post_meta($post->ID, $key, FALSE))
        {
            /* / If the custom field already has a value it will update */
            update_post_meta($post->ID, $key, $value);
        }
        else
        {
            /* / If the custom field doesn't have a value it will add */
            add_post_meta($post->ID, $key, $value);
        }

        if (!$value)
        {
            delete_post_meta($post->ID, $key); // Delete if blank value
        }
    }

    //
    /*     * ****  SHORT CODE  ******* */

    public function showVideo($atts)
    {
        ob_start();
        $postID = isset($atts['id']) ? $atts['id'] : '';
        $fancyBox = isset($atts['popup']) && $atts['popup'] == 'true' ? $atts['popup'] : false;
        $bs_gallery_column = get_post_meta($postID, '_bs_gallery_column', true);
        $template = '';

        if(isset($atts['carousel']) && $atts['carousel']==true)
        {
            $template = "carousel";
        }
        else
        {
            $this->fancyBoxCall();
        }

        if($template == 'carousel')
        {
            $this->fireBx('gallery_section');
            $this->carouselTemplate($bs_gallery_column);
        }
        else
        {
            $this->fancyBoxTemplate($bs_gallery_column);
        }
        
         /*
          <div class="popBox" style="display: none;"></div>
          <script type="text/javascript">
          /*    jQuery(document).ready(function ()
          {
          if (screen.width <= 1000)
          {
          var wid = (screen.width * 0.9);
          var ht = Math.round(56.38 / 100 * wid);
          jQuery('iframe.vid_iframe').attr('width', (wid) + 'px');
          jQuery('iframe.vid_iframe').attr('height', (ht) + 'px');
          }

          var _id = video = plyr = '';
          jQuery('.videoTrigger').click(function ()
          {
          _id = '#' + jQuery(this).attr('data-id');
          plyr = _id + " iframe";
          jQuery('.popBox').show().fadeIn();
          jQuery(_id).fadeIn();
          video = jQuery(plyr).attr('src');
          jQuery(plyr).attr('src', video + "/?autoplay=1");
          });

          jQuery('.popBox, .closePop').click(function ()
          {
          jQuery('.popBox').fadeOut();
          var videoURL = jQuery(plyr).prop('src');
          videoURL = videoURL.replace("/?autoplay=1", "");
          jQuery(plyr).prop('src', '');
          jQuery(plyr).prop('src', videoURL);
          jQuery('.pop_section').fadeOut();
          });
          });
          </script> */ ?>
        <?php
        return ob_get_clean();
    }
    /*     * *************************************************************************** */
    /*     * *************************************************************************************** */

    


    function getCats($id, $taxonomy)
    {
        $cat_args = array('parent' => $id, 'number' => 10, 'hide_empty' => false);
        $termParent = get_terms($taxonomy, $cat_args);
        return $termParent;
    }

    function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $test = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return $test;
    }

    
}
