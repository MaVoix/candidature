$(document).on('click', '#close-preview', function(){
    $('.image-preview').popover('hide');
    // Hover befor close the preview
    $('.image-preview').hover(
        function () {
            $('.image-preview').popover('show');
        },
        function () {
            $('.image-preview').popover('hide');
        }
    );
});

$(function() {
    // Create the close button
    var closebtn = $('<button/>', {
        type:"button",
        text: 'x',
        id: 'close-preview',
        style: 'font-size: initial;',
    });
    closebtn.attr("class","close pull-right");
    // Set the popover default content
    $('.image-preview').popover({
        trigger:'manual',
        html:true,
        title: "<strong>Aperçu</strong>"+$(closebtn)[0].outerHTML,
        content: "Pas d'image",
        placement:'bottom'
    });
    // Clear event
    $('.image-preview-clear').click(function(){
      //  $('.image-preview').attr("data-content","").popover('hide');
        $(".drop-zone").css("background-image", "url('/css/images/dropzone.png')");
        $('.image-preview-filename').val("");
        $('.image-preview-clear').hide();
        $('.image-preview input:file').val("");
        $(".image-preview-input-title").text("Choisir");
    });

    $('.image-preview-input').on('click',function(){
        $('.image-preview input:file').trigger("click");
    });
    // Create the preview image
    $('.image-preview input:file').change(function (){
        var img = $('<img/>', {
            id: 'dynamic',
            height:160
        });

        img.css('display','block');
        img.css('margin','auto');
        var file = this.files[0];
        var reader = new FileReader();
        // Set preview image into the popover data-content
        reader.onload = function (e) {
            $(".image-preview-input-title").text("Autre");
            $(".image-preview-clear").show();
            $(".image-preview-filename").val(file.name);
            img.attr('src', e.target.result);
            $('#imageData').val(e.target.result);
            $('#imageFilename').val(file.name);
            $(".drop-zone").css("background-image", "url("+e.target.result+")");

        }
        reader.readAsDataURL(file);
    });



    $(document).on('dragenter', '.drop-zone', function() {
        $(this).css('border', '2px dashed #000000' );
        return false;
    });

    $(document).on('dragover', '.drop-zone', function(e){
        e.preventDefault();
        e.stopPropagation();
        $(this).css('border', '2px dashed #000000');
        return false;
    });

    $(document).on('dragleave', '.drop-zone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css('border', '2px dashed #bfbfbf');
        return false;
    });

    $(document).on('drop', '.drop-zone', function(e) {
        if(e.originalEvent.dataTransfer){
            if(e.originalEvent.dataTransfer.files.length) {
                // Stop the propagation of the event
                e.preventDefault();
                e.stopPropagation();
                $(this).css('border', '2px dashed #bfbfbf');



                var img = $('<img/>', {
                    id: 'dynamic',
                    height:160
                });

                img.css('display','block');
                img.css('margin','auto');
                var file = e.originalEvent.dataTransfer.files[0];
                var reader = new FileReader();
                // Set preview image into the popover data-content

                reader.onload = function (e) {
                    $(".image-preview-input-title").text("Autre");
                    $(".image-preview-clear").show();
                    $(".image-preview-filename").val(file.name);
                    img.attr('src', e.target.result);
                    $('#imageData').val(e.target.result);
                    $('#imageFilename').val(file.name);
                    $(".drop-zone").css("background-image", "url("+e.target.result+")");

                }

                reader.readAsDataURL(file);
            }
        }
        else {
            $(this).css('border', '2px dashed #bfbfbf');
        }
        return false;
    });
});


