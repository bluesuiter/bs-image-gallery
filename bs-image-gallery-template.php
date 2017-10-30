<?php

class bsImageGalleryTemplate{

    public function bisServeShortcode($atts)
    {
        ob_start();

        $id = checkArrayValue($atts, 'id');
        switch(checkArrayValue($atts, 'template'))
        {
            case 'polaroid':
                $this->bisPolaroidTemplate($id);
                break;
            case 'slider':
                $this->bisSliderTemplate($id);
                break;
            default:
                $this->bisDefaultTemplate($id);
                break;
        }   
        return ob_get_clean();
    }

    public function bisPolaroidTemplate($id)
    {
        $objDataClass = new bsDataClass();
        $result = $objDataClass->bsiFetchData($id, array('id', 'gallery_name', 'gallery_data', 'thumbnail', 'status'), true);

        if(!empty($result))
        {
        ?>
            <div id="bsi-polaroid-gallery" class="bsiGallery bsi-polaroid">
                <div class="">
                    <?php
                    foreach($result as $res)
                    {
                        $galleryName = $res['gallery_name'];
                        $gallery = unserialize($res['gallery_data']);

                        $column = $gallery['column'];
                        $imgId = $gallery['img_id'];
                        $imgTitle = $gallery['img_title'];
                        $imgDesc = $gallery['img_desc'];
                    ?>
                        <div class="bsi-polaroid-thumbnail" title="<?php echo $galleryName ?>">
                            <img class="galleryTrigger" data-id="<?php echo $res['id'] ?>" src="<?php echo $res['thumbnail'] ?>" alt="<?php echo $galleryName ?>"/>
                            <p><?php echo $galleryName ?></p>
                        </div>

                        <div id="album<?php echo $res['id'] ?>" style="display:none" class="albumContainer">
                            <?php for($ika = 0;$ika < max(count($imgURL), count($imgTitle), count($imgDesc)); $ika++){ ?>
                                <?php $imgSrc = wp_get_attachment_image_src($imgId[$ika], 'full') ?>
                                <a class="fancybox-media" rel="gallery<?php echo $res['id'] ?>" href="<?php echo $imgSrc[0] ?>" title="<?php echo $imgTitle[$ika] ?>">
                                    <img src="<?php echo $imgSrc[0] ?>" alt="<?php echo $imgTitle[$ika] ?>">
                                </a>
                            <?php } ?>
                        </div>
                    <?php
                    } 
                    ?>
                </div>
            </div>
            <script>
                jQuery(function($){
                    $('#bsi-polaroid-gallery').on('click', 'img.galleryTrigger', function(){
                        var dataId = $(this).attr('data-id');
                        $('#album' + dataId).find('a:first-child').trigger('click');
                    });
                    $(".fancybox-media").colorbox({transition:"none", width:"75%", height:"75%"});
                })
            </script>
            <?php
        }
    }


    public function bisDefaultTemplate($id)
    {
        $objDataClass = new bsDataClass();
        $result = $objDataClass->bsiFetchData($id, array('id', 'gallery_name', 'gallery_data', 'thumbnail', 'status'));

        if(!empty($result))
        {
        ?>
            <div id="bsi-default-gallery" class="bsi-default-gallery">
                <div class="albumContainer">
                    <?php
                        $galleryName = $result['gallery_name'];
                        $gallery = unserialize($result['gallery_data']);

                        $column = $gallery['column'];
                        $imgId = $gallery['img_id'];
                        $imgTitle = $gallery['img_title'];
                        $imgDesc = $gallery['img_desc'];
                    ?>
                    
                    <?php for($ika = 0;$ika < max(count($imgURL), count($imgTitle), count($imgDesc)); $ika++){ ?>
                        <?php $imgSrc = wp_get_attachment_image_src($imgId[$ika], 'full') ?>
                        <a class="fancybox-media" rel="gallery<?php echo $res['id'] ?>" href="<?php echo $imgSrc[0] ?>" title="<?php echo $imgTitle[$ika] ?>">
                            <img src="<?php echo $imgSrc[0] ?>" alt="<?php echo $imgTitle[$ika] ?>">
                            <p><?php echo $imgTitle[$ika] ?></p>
                        </a>
                    <?php } ?>

                </div>
            </div>
            <script>
                jQuery(function($){
                    $(".fancybox-media").colorbox({rel:$(this).attr('rel'), title: false, maxHeight:"75%", transition:"fade"});
                })
            </script>
            <?php
        }
    }

    public function bisSliderTemplate($id)
    {
        $objDataClass = new bsDataClass();
        $result = $objDataClass->bsiFetchData($id, array('id', 'gallery_name', 'gallery_data', 'thumbnail', 'status'));

        if(!empty($result)){
        	$galleryName = $result['gallery_name'];
        ?>
        <div class="gallery_section <?php echo $galleryName?>">
            <?php
                $gallery = unserialize($result['gallery_data']);

                $column = $gallery['column'];
                $imgId = $gallery['img_id'];
                $imgTitle = $gallery['img_title'];
                $imgDesc = $gallery['img_desc'];

                for($ika = 0;$ika < max(count($imgURL), count($imgTitle)); $ika++){
                    ?>
                    <?php $imgSrc = wp_get_attachment_image_src($imgId[$ika], 'full') ?>
                    <img width="100%" src="<?php echo $imgSrc[0]; ?>" alt="<?php echo $imgTitle[$ika]; ?>"/>
                    <?php
                }
            }
            ?>
        </div>  
        <script>
        	jQuery(function($){
        		$('.<?php echo $galleryName?>').bxSlider({useCSS:true});
        	});
        </script>
        <?php
    }


    public function fancyBoxTemplate($bs_gallery_column)
    {
        $imgLink = isset($bs_gallery_column['gallery_image_file']) ? $bs_gallery_column['gallery_image_file'] : '';
        $imgVideo = isset($bs_gallery_column['gallery_image_video']) ? $bs_gallery_column['gallery_image_video'] : '';
        $imgText = isset($bs_gallery_column['gallery_image_text']) ? $bs_gallery_column['gallery_image_text'] : '';
        $imgActive = isset($bs_gallery_column['gallery_image_active']) ? $bs_gallery_column['gallery_image_active'] : '';
        $ik = 0;

        $count = max(count($imgLink), count($imgVideo), count($imgText));
        ?>
        <div class="gallery_section">
            <?php
            for ($ika = 0; $ika < $count; $ika++)
            {
                $img = isset($imgLink[$ika]) ? wp_get_attachment_image_src($imgLink[$ika], 'full') : '';
                $vid = isset($imgVideo[$ika]) ? $imgVideo[$ika] : '';
                $txt = isset($imgText[$ika]) ? stripslashes($imgText[$ika]) : '';
                $act = isset($imgActive[$ika]) ? $imgActive[$ika] : '';

                if ($act == '1')
                {
                    ?>
                    <div class="span4 gallery_itm item_<?= $ik ?>">
                        <div class="gallery_img">
                            <?php
                                if($fancyBox)
                                {
                                    $this->imageWithFancyBox($vid, $img[0], $txt);
                                }
                                else
                                {
                                    $this->imageWithOutFancyBox($img[0], $txt);
                                }
                            ?>
                        </div>
                    </div>
                    <?php
                    ++$ik;
                    if ($ik == 3)
                    {
                        $ik = 0;
                    }
                }
            }
            ?>
        </div>            
        <?php
    }
    

    public function fireBx($class)
    {
        ?>
        <script type="text/javascript">
            jQuery(function($)
            {
                $('.<?php echo $class; ?>').bxSlider({
                  minSlides: 3,
                  maxSlides: 4,
                  slideWidth: 170,
                  slideMargin: 10
                });
            });
        </script>
        <?php
    }

    public function fancyBoxCall()
    {
        ?>
        <script>
            jQuery(document).ready(function () {
                if(typeof jQuery.fancybox == 'function') 
                {
                    console.log("fancy box loaded");
                } 
                else 
                {
                    jQuery('.fancybox-media').fancybox(
                    {
                        wrapCSS: 'sagwrap',
                        mouseWheel: false,
                        prevEffect: 'none',
                        nextEffect: 'none',
                        scrolling: "no",
                        openEasing: 'swing',
                        nextEasing: 'swing',
                        openMethod: 'zoomIn',
                        closeMethod: 'zoomOut',
                        closeSpeed: 500,
                        openSpeed: 500,
                        scrollOutside: false,
                        closeBtn: true,
                        helpers: 
                        {
                            title: 
                            {
                                type: 'inside'
                            },
                            overlay: 
                            {
                                locked: true,
                                css: 
                                {
                                    'background': 'rgba(255, 255, 255, 0.95)'
                                }
                            },
                        },
                    });                     
                }
            });
        </script>
        <?php
    }

    function imageWithFancyBox($vid, $img, $txt)
    {
        if($vid)
        {
            echo '<a class="fancybox-media fancybox.iframe" rel="gallery1" href="'. $vid .'">';
        }
        else
        {
            echo '<a class="fancybox-media" rel="gallery1" href="'. $img .'">';
        }
        ?>
            <img width="100%" src="<?php echo $img; ?>" alt="<?php echo $txt; ?>"/>
            <span class="gallery_titl" style="display:none;"><?php echo $txt; ?></span>
        </a>
        <?php
    }


    function imageWithOutFancyBox($img, $txt)
    {
        ?>
            <img width="100%" src="<?php echo str_replace(['http:', 'https:'], ['', ''], ($img)); ?>" alt="<?php echo $txt; ?>"/>
            <span class="gallery_titl" style="display:none;"><?php echo $txt; ?></span>
        <?php        
    }
}