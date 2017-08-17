jQuery('body').on('click', '.upload_image_button', function (slider)
{
// Uploading files
    var file_frame;
    slider.preventDefault();
    // If the media frame already exists, reopen it.
    if (file_frame) {
        file_frame.open();
        return;
    }

// Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
        title: jQuery(this).data('uploader_title'),
        button: {text: jQuery(this).data('uploader_button_text'), },
        multiple: true // Set to true to allow multiple files to be selected
    });
    // When a file is selected, run a callback.
    file_frame.on('select', function ()
    {
        // We set multiple to false so only get one image from the uploader
        //attachment = file_frame.state().get('selection').first().toJSON();
        // here are some of the variables you could use for the attachment;
        //var all = JSON.stringify( attachment );      
        //var id = attachment.id;
        //var title = attachment.title;
        //var filename = attachment.filename;
        //var url = attachment.url;
        //var link = attachment.link;
        //var alt = attachment.alt;
        //var author = attachment.author;
        //var description = attachment.description;
        //var caption = attachment.caption;
        //var name = attachment.name;
        //var status = attachment.status;
        //var uploadedTo = attachment.uploadedTo;
        //var date = attachment.date;
        //var modified = attachment.modified;
        //var type = attachment.type;
        //var subtype = attachment.subtype;
        //var icon = attachment.icon;
        //var dateFormatted = attachment.dateFormatted;
        //var editLink = attachment.editLink;
        //var fileLength = attachment.fileLength;

        attachment = file_frame.state().get('selection').toJSON();
        if (attachment.length > 1)
        {
            setValueOfGalleryItem(attachment);
        } else {
            setValueOfGalleryItem(attachment);
        }
    });
    // Finally, open the modal
    file_frame.open();
    jQuery('.upload_image_button').die("click");
});


jQuery(document).ready(function ()
{
    jQuery('button[name="delte"]').click(function ()
    {
        jQuery(this).closest('p').remove();
    });
    jQuery('button[name="AddGallery"]').click(function ()
    {
        addGalleryItem();
    });


    jQuery('button[name="RemGallery"]').click(function ()
    {
        if (jQuery('.gallery_item').size() < 2)
        {
            jQuery('.gallery_item').find(':input').val('');
            jQuery('.gallery_item .prevImage').attr('src', '');
        } else
        {
            jQuery('.gallery_item:last').remove();
        }
        jQuery('button[name="AddGallery"]').show();
    });

    /**tRASH Button */
    jQuery('i.trashIt').click(function () {
        if (jQuery('.gallery_item').size() > 1) {
            jQuery(this).closest('.gallery_item').remove();
            updateItemAttr();
        } else {
            jQuery('.gallery_item').find(':input').val('');
            jQuery('.gallery_item .prevImage').attr('src', '');
            updateItemAttr();
        }
    });
});


function updateItemAttr()
{
    var _ikA = 0;
    jQuery('.gallery_container').find('.gallery_item').each(function () {
        jQuery(this).find(".gallery_image_file").attr('id', 'gallery_image_file' + _ikA);
        jQuery(this).find(".gallery_image_video").attr('id', 'gallery_image_video' + _ikA);
        jQuery(this).find(".gallery_image_text").attr('id', 'gallery_image_text' + _ikA);
        jQuery(this).find(".gallery_image_active").attr('id', 'gallery_image_active' + _ikA).attr('name', 'gallery_image_active[' + _ikA + ']');
        ++_ikA;
    });
}

function setValueOfGalleryItem(attachment)
{
    var lEn = attachment.length;
    var data = [];
    var _siz = jQuery('.gallery_item').size();
    for (var $imgCnt = 0; $imgCnt < lEn; $imgCnt++) {
        if ($imgCnt == lEn)
        {
            return false;
        }

        data.id = attachment[$imgCnt].id;
        data.url = attachment[$imgCnt].url;
        data.title = attachment[$imgCnt].title;

        var template = setValueInTemplate(($imgCnt + _siz), data);
        jQuery('.gallery_container tbody').append(template);
        document.getElementById('gallery_image_active' + ($imgCnt + _siz)).click();
    }
}


function setValueInTemplate(srNo, data)
{
    var template = '<tr class="gallery_item">' +
            '<td class="imageCol">' +
            '<img src="' + data.url + '" class="prevImage"/>' +
            '<input id="gallery_image_file' + srNo + '" class="gallery_image_file" type="hidden" name="gallery_image_file[]" value="' + data.url + '" />' +
            '<input type="hidden" class="img_txt_id" name="img_txt_id[]" value="' + data.id + '" />' +
            '</td>' +
            '<td>' +
            '<input type="text" name="gallery_image_video[]" class="gallery_image_video" id="gallery_image_video' + srNo + '" placeholder="Video-Link" value="" />' +
            '</td>' +
            '<td>' +
            '<input type="text" name="gallery_image_text[]" class="gallery_image_text" id="gallery_image_text' + srNo + '" placeholder="Title" value="' + data.title + '"/>' +
            '</td>' +
            '<td style="text-align:center;">' +
            '<input name="gallery_image_active[' + srNo + ']" class="gallery_image_active" id="gallery_image_active' + srNo + '" value="1" type="checkbox"/>' +
            '<i class="dashicons dashicons-trash trashIt"></i>' +
            '</td>' +
            '</tr>';
    return template;
}