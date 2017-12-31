var rowId = 0;


jQuery(function($)
{
    addSortingAbility();


    /* Field selection on basis on tempaltes */
    $('#seltemplate').on('change', function(){
        $('#templateOption .templateOption').removeAttr('style');
        $('#'+$(this).val()).show();
    });

    /* Ajax action handler for uploading images */
    /*$('form[name="ivmGallery"]').submit(function(){
        var data = $(this).serialize();
        $.ajax({
            url    : ajaxurl,
            data   : {action: 'uploadivMGallery', data: data},
            method : 'post',
            dataType   : 'json'
        }).done(function(res){
            alert(res);
        });
    });*/

    jQuery('#ivmGalleryBody').on('click', 'button.actGlryRow', function(){
            var rowId = jQuery(this).attr('data-rowid');
            var actFld = jQuery('tr#bsigrow' + rowId + ' #active' + rowId);
            actFld.val() == 1 ? actFld.val(0) : actFld.val(1);
            
            if(jQuery(this).find('span').hasClass('dashicons-no'))
            {
                jQuery(this).find('span').removeClass('dashicons-no').addClass('dashicons-yes');            
            }else{
                jQuery(this).find('span').removeClass('dashicons-yes').addClass('dashicons-no');            
            }
    });


    jQuery('#ivmGalleryBody').on('click', 'button.remGlryRow', function(){
        if(confirm('This will remove image from gallery list.'))
        {
            jQuery('tr#bsigrow' + jQuery(this).attr('data-rowid')).remove();
            correctIndexing();
        }
    });

    
    /* Image Uploading Code */
    var i = 0;
    var file_frame, id, img;
    
    $('div.bsImageGallery').on('click', 'button.upimage, button.edtGlryRow', function (slider)
    {
        $this = $(this);
        slider.preventDefault();
        
        /*/ If the media frame already exists, reopen it.*/
        if (file_frame) {
            file_frame.open();
            return false;
        }

        /*/ Create the media frame.*/
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'BS-Image-Gallery',
            button: {
                text: jQuery(this).data('uploader_button_text'),
            },
            multiple: ($this.hasClass('multiple') ? true : false) /*/ Set to true to allow multiple files to be selected*/
        });


        /*/ When a file is selected, run a callback.*/
        file_frame.on('select', function ()
        {
            attachment = file_frame.state().get('selection').toJSON();
            rowId = $this.attr('data-rowid');
 
            if($this.attr('data-action') == 'edt') 
            {
                $('#bsigrow'+rowId).find('img.previmg').attr('src', attachment[0].url);
                $('#bsigrow'+rowId).find('#image'+rowId).val(attachment[0].id);
            }
            else if($this.attr('data-field'))
            {
                $('#thumbnail-prev').attr('src', attachment[0].url)
                $($this.attr('data-field')).val(attachment[0].id);
            }
            else
            {
                len = ivmSetGalleryData(attachment, rowId); 
                $('#upimage, #addRow').attr('data-rowid', len);
            }
            correctIndexing();
            addSortingAbility();
        });       

        /*/ Finally, open the modal*/
        file_frame.open();
    });   
    /* Image Uploading Code */
});


function addSortingAbility()
{
    /* Events to perform on Sorting */
    jQuery("#ivmGalleryBody").sortable({
      stop: function() {
          correctIndexing();
      }
    }).disableSelection();
}


function ivmSetGalleryData(attachment, rowId)
{
    var ika = 0;
    var len = attachment.length;
    var dataArr = [];

    for(ika = 0; ika < len; ika++)
    {   
        dataArr.url = attachment[ika].url;
        dataArr.id = attachment[ika].id;
        dataArr.title = attachment[ika].title;
        
        if(attachment[ika].title == '')
        {
            dataArr.title = attachment[ika].name;
        }
        
        if(len != ika)
        {
            rowTemplate = ivmRowTemplate(dataArr, rowId);
            jQuery('#ivmGalleryBody').append(rowTemplate);
            rowId = parseInt(rowId) + 1;
        }
    }

    return (rowId);
}


function ivmRowTemplate(attachment, rowId)
{
    return rowTemplate =  '<tr class="connectedSortable" id="bsigrow'+ rowId +'"><td>\
                        <img style="max-width:100px;" src="'+ attachment.url +'" class="previmg" id="previmg'+ rowId +'"/>\
                        <input type="hidden" class="imageVal" id="image'+ rowId +'" name="image['+ rowId +']" value="'+ attachment.id +'"/>\
                        </td><td>\
                        <input type="text" class="col-1 titleVal" id="title'+ rowId +'" name="title['+ rowId +']" value="'+ attachment.title +'"/>\
                        <input type="hidden" id="active'+ rowId +'" name="active['+ rowId +']" value="1"/>\
                        </td><td>\
                        <textarea class="descText col-1" id="descText'+ rowId +'" name="descText['+ rowId +']"></textarea>\
                        </td><td>\
                        <button title="Active/Deactive" class="actGlryRow button" data-rowid="'+ rowId +'" type="button">\
                        <span class="dashicons dashicons-no"></span></button>\
                        <button title="Trash Image" class="remGlryRow button" data-rowid="'+ rowId +'" type="button">\
                        <span class="dashicons dashicons-trash"></span></button>\
                        <button title="Edit Image" class="edtGlryRow button" data-action="edt" data-rowid="'+ rowId +'" type="button">\
                        <span class="dashicons dashicons-edit"></span></button></td></tr>';
}


function correctIndexing()
{
    var _ikA = 0;
    jQuery('#ivmGallery > tbody').find('tr').each(function(){
        jQuery(this).attr('id', 'bsigrow' + _ikA);
        jQuery(this).find('.previmg').attr({id:'previmg' + _ikA});
        jQuery(this).find('.imageVal').attr({id: 'image' + _ikA, name:'image['+ _ikA +']'});
        jQuery(this).find('.titleVal').attr({id: 'title' + _ikA, name:'title['+ _ikA +']'});
        jQuery(this).find('.active').attr({id: 'active' + _ikA, name:'active['+ _ikA +']'});
        jQuery(this).find('.descText').attr({id: 'descText' + _ikA, name:'descText['+ _ikA +']'});
        jQuery(this).find('button.actGlryRow, button.edtGlryRow, button.edtGlryRow').attr('data-rowid', + _ikA);
        // jQuery(this).find('button.remRow').attr('data-rowid', + _ikA);
        // jQuery(this).find('button.edtRow').attr('data-rowid', + _ikA);
        ++_ikA;
    });
    _ikA = 0;
    return false;
}