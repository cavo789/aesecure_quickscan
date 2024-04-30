/**
 * Style UI for Gallery Uploader
 */
var GalleryContent = window.GalleryContent || {};
"use strict";
jQuery.extend(GalleryContent, {

    //init uploader
    initUploader: function () {
        jQuery('#gallery_files').sortable();
        jQuery('#gallery_files').find('li.media').each(function (i, el) {
            var uploader    =   jQuery(el);
            uploader.find('.delete_grid_image').on('click', function (event) {
                event.preventDefault();
                if (confirm('Are you sure?')) {
                    uploader.remove();

                    if (jQuery('#gallery_files li.media').length <= 0) {
                        jQuery('#gallery_files').find('li.empty').fadeIn();
                    }
                    GalleryContent.ui_add_log('File \''+uploader.attr('data-name')+'\' deleted');
                }
            })
        })
    },

    // add the log
    ui_add_log: function (message, color) {
        var d = new Date();

        var dateString = (('0' + d.getHours())).slice(-2) + ':' +
            (('0' + d.getMinutes())).slice(-2) + ':' +
            (('0' + d.getSeconds())).slice(-2);

        color = (typeof color === 'undefined' ? 'muted' : color);

        var template = jQuery('#gallery_debug_template').text();
        template = template.replace('%%date%%', dateString);
        template = template.replace('%%message%%', message);
        template = template.replace('%%color%%', color);

        jQuery('#gallery_debug').find('li.empty').fadeOut(); // remove the 'no messages yet'
        jQuery('#gallery_debug').prepend(template);
    },

    // Creates a new file and add it to our list
    ui_multi_add_file: function (id, file) {
        var uploader = jQuery('#gallery_files_template').text();
        uploader = uploader.replace('%%filename%%', file.name);

        uploader = jQuery(uploader);
        uploader.prop('id', 'uploaderFile' + id);
        uploader.data('file-id', id);

        if (typeof FileReader !== 'undefined'){
            var reader      = new FileReader();

            reader.onload = function (e) {
                uploader.find('img').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }

        uploader.attr('data-source', 'client');
        jQuery('#gallery_files').prepend(uploader);
    },

    // Changes the status messages on our list
    ui_multi_update_file_status: function (id, status, message) {
        jQuery('#uploaderFile' + id).find('span').html(message).prop('class', 'status text-' + status);
    },

    // Updates a file progress, depending on the parameters it may animate it or change the color.
    ui_multi_update_file_progress: function (id, percent, color, active) {
        color = (typeof color === 'undefined' ? false : color);
        active = (typeof active === 'undefined' ? true : active);

        var bar = jQuery('#uploaderFile' + id).find('div.progress-bar');

        bar.width(percent + '%').attr('aria-valuenow', percent);
        bar.toggleClass('progress-bar-striped progress-bar-animated', active);

        if (percent === 0){
            bar.html('');
        } else {
            bar.html(percent + '%');
        }

        if (color !== false){
            bar.removeClass('bg-success bg-info bg-warning bg-danger');
            bar.addClass('bg-' + color);
        }
    },

    ui_multi_update_file_data: function (id, file) {
        var uploader    = jQuery('#uploaderFile' + id);
        jQuery('#gallery_files').find("[data-name='"+file.name+"']").remove();
        uploader.find('.filename').text(file.name);
        uploader.attr('data-name', file.name);
        uploader.find('.gallery_url').attr('value', file.name);
        uploader.find('.grid_image_featured').attr('value', file.name);
        if (jQuery('#gallery_files').find('li.empty').is(':visible')) {
            uploader.find('.grid_image_featured').attr('checked','checked');
        }

        jQuery('#gallery_files').find('li.empty').fadeOut(); // remove the 'no files yet'
        uploader.find('.delete_grid_image').on('click', function (event) {
            event.preventDefault();
            if (confirm('Are you sure?')) {
                uploader.remove();

                if (jQuery('#gallery_files li.media').length <= 0) {
                    jQuery('#gallery_files').find('li.empty').fadeIn();
                }
                GalleryContent.ui_add_log('File \''+file.name+'\' deleted');
            }
        });
    }
});
