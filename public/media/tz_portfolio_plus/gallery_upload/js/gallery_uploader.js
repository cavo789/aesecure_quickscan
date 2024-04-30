jQuery(function($){
    /*
     * For the sake keeping the code clean and the examples simple this file
     * contains only the plugin configuration & callbacks.
     *
     * UI functions ui_* can be located in: style_ui.js
     */
    GalleryContent.initUploader();
    $('#gallery_uploader').dmUploader({ //
        url: GalleryContent.ajaxUrl,
        maxFileSize: GalleryContent.maxFileSize*1000000, // 3 Megs
        allowedTypes: 'image/*',
        extFilter: GalleryContent.extFilter,
        onDragEnter: function(){
            // Happens when dragging something over the DnD area
            this.addClass('active');
        },
        onDragLeave: function(){
            // Happens when dragging something OUT of the DnD area
            this.removeClass('active');
        },
        onInit: function(){
            // Plugin is ready to use
            GalleryContent.ui_add_log('Penguin initialized :)', 'info');
        },
        onComplete: function(){
            // All files in the queue are processed (success or error)
            GalleryContent.ui_add_log('All pending tranfers finished');
        },
        onNewFile: function(id, file){
            // When a new file is added using the file selector or the DnD area
            GalleryContent.ui_add_log('New file added #' + id);
            GalleryContent.ui_multi_add_file(id, file);
        },
        onBeforeUpload: function(id){
            // about tho start uploading a file
            GalleryContent.ui_add_log('Starting the upload of #' + id);
            GalleryContent.ui_multi_update_file_status(id, 'uploading', 'Uploading...');
            GalleryContent.ui_multi_update_file_progress(id, 0, '', true);
        },
        onUploadCanceled: function(id) {
            // Happens when a file is directly canceled by the user.
            GalleryContent.ui_multi_update_file_status(id, 'warning', 'Canceled by User');
            GalleryContent.ui_multi_update_file_progress(id, 0, 'warning', false);
        },
        onUploadProgress: function(id, percent){
            // Updating file progress
            GalleryContent.ui_multi_update_file_progress(id, percent);
        },
        onUploadSuccess: function(id, data){
            // A file was successfully uploaded
            GalleryContent.ui_add_log('Server Response for file #' + id + ': ' + JSON.stringify(data));
            GalleryContent.ui_add_log('Upload of file #' + id + ' COMPLETED', 'success');
            GalleryContent.ui_multi_update_file_data(id, data);
            GalleryContent.ui_multi_update_file_status(id, 'success', 'Upload Complete');
            GalleryContent.ui_multi_update_file_progress(id, 100, 'success', false);
        },
        onUploadError: function(id, xhr, status, message){
            GalleryContent.ui_multi_update_file_status(id, 'danger', message);
            GalleryContent.ui_multi_update_file_progress(id, 0, 'danger', false);
        },
        onFallbackMode: function(){
            // When the browser doesn't support this plugin :(
            GalleryContent.ui_add_log('Plugin cant be used here, running Fallback callback', 'danger');
        },
        onFileSizeError: function(file){
            GalleryContent.ui_add_log('File \'' + file.name + '\' cannot be added: size excess limit', 'danger');
        }
        ,
        onFileExtError: function(file){
            GalleryContent.ui_add_log('File \'' + file.name + '\' cannot be added: extension invalid', 'danger');
        }
    });
});