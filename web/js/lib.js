var isSendingForm = false;

$(document).ready(function () {

    //toastr default option
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-center",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    //selector body
    var $body = $('body');

    //hide PHP error message on dbclik (with ajax)
    $body.on('dblclick', '.error', function (e)
    {
        $(this).hide();
    });






    //click link modal
    $body.on("click", ".click-send-ajax-confirm", function (e)
    {
        var $title = $('#modal-title');
        var $message = $('#modal-body');
        var $btOk = $('#modal-confirm');
        var $btCancel = $('#modal-cancel');

        var $element = $(this);

        if( $element.data("modal-title") )
        {
            $title.html($element.data("modal-title"));
        }
        else
        {
            $title.html("Confirmation");
        }

        if( $element.data("modal-body") )
        {
            $message.html($element.data("modal-body"));
        }
        else
        {
            $message.html("Êtes-vous sûr de vouloir continuer ?");
        }

        if( $element.data("modal-confirm") )
        {
            $btOk.html($element.data("modal-confirm"));
            $btOk.data("url", $element.data("url"));
            $btOk.data("param", $element.data("param"));
        }
        else
        {
            $btOk.html("Ok");
            $btOk.data("url", "/");
            $btOk.data("param", "");
        }
        $btOk.off("click.confirm").on("click.confirm", function (e)
        {
            if (!isSendingForm)
            {
                var url = $(this).data("url");
                var data = $(this).data("param");
                sendAjaxRequest(url, data);
            }
            $('#modalConfirm').modal('toggle');
        });

        if( $element.data("modal-confirm") )
        {
            $btCancel.html( $element.data("modal-cancel"));
        }
        else
        {
            $btCancel.html("Annuler");
        }


        $('#modalConfirm').modal('toggle');
    });


    //select text value on click
    $("input.jsSelectOnClick").on('click', function() { $(this).select(); });

    //historyback button
    $(".jsBackButton").on('click', function() {  window.history.back(); });

    //submit auto-valid pour l'admin
    $(".jsBtAutoValid").on('click', function() {
        $("#autovalid").val("1");
        $("#formcandidature").submit();

    });

    //tel international plugin
    $("#phone").intlTelInput({
        utilsScript: "/js/plugins/intlTelInput/utils.js",
        preferredCountries : ["fr"]
    });
    $(".jsTestPhone").on('click', function() {
        var tel=$("#tel");
        var phone=$("#phone");
        if(phone.length>0){
            tel.val($("#phone").intlTelInput("getNumber"));
            tel.attr('type','text');
        }

    });

    //toggle offline/online
    $body.on('change','.jsSwitchAjax',function() {
        var $element = $(this);
        if (!isSendingForm)
        {
            var url = $element.data("url");
            var data = $element.data("param")+'&checked='+$element.prop('checked');
            sendAjaxRequest( url, data);
        }
    });

    $body.on('click','.jsLink',function(){
        window.location.href=$(this).data("url");
    });

    //refresh Captcha
    $body.on('click','.jsCaptchaRefresh',function(){
        var  date=new Date();
        $(".jsCaptcha").attr("src","/captcha/image.jpg?v="+date.getTime());
    });

    //click link
    $body.on("click", ".click-send-ajax", function (e)
    {
        e.preventDefault();
        var $element = $(this);
        if (!isSendingForm)
        {
            var url = $element.data("url");
            var data = $element.data("param");
            sendAjaxRequest( url, data);
        }
    });

    //submit form on select change
    $body.on("change",".submitOnChange",function(){
       $(this).closest("form").submit();
    });

    $body.on("click",".jsBtReset",function(){
        $(this).closest("form").find("select option:first").prop('selected', true);
        $(this).closest("form").find("input").val('');
        $(this).closest("form").submit();
    });


    //click form
    $body.on('submit', 'form[data-ajax="true"]', function (e) {
        e.preventDefault();
        var tel=$("#tel");
        var phone=$("#phone");
        if(phone.length>0){
            tel.val($("#phone").intlTelInput("getNumber"));
            tel.attr('type','text');
        }

        if (!isSendingForm) {
            var $form = $(this);
            var aData = $form.getFormDatas();
            sendAjaxRequest( $form.attr("action"), aData);
        }

    });

    //upload preview
    $('.uploadpic').on('change', function (e) {
        var files = $(this)[0].files;

        if (files.length > 0) {
            var file = files[0],$image_preview = $('#image_preview');
            $image_preview.find('.thumbnail').removeClass('hidden');
            $image_preview.find('img').attr('src', window.URL.createObjectURL(file));
            $image_preview.find('h4').html(file.name);
            $image_preview.find('.caption p:first').html(file.size + ' bytes');
        }
    });

    // Bouton "Annuler" pour vider le champ d'upload
    $('#image_preview').find('button[type="button"]').on('click', function (e) {
        e.preventDefault();

        $('#formcandidature').find('input[name="image"]').val('');
        $('#image_preview').find('.thumbnail').addClass('hidden');
    });

    //keep-awake
    setInterval(function () {
        if (!isSendingForm) {
            $.ajax('/home/keep-awake.html');
        }
    }, 1000 * 60 * 2);

    //CMS Markdown
    $body.on('click','.jsEditCmsBlock',function(){
       var $block=$("#"+$(this).data("block-id"));
       var ref=$(this).data("block-reference");

       $block.markdown({
            savable:true,
            language:'fr',
            onShow: function(e){},
            onPreview: function(e) {
                return e.getContent();
               /* var previewContent
                if (e.isDirty()) {
                    var originalContent = e.getContent();
                    previewContent = "Prepended text here..."
                        + "\n"
                        + originalContent
                        + "\n"
                        +"Apended text here..."
                } else {
                    previewContent = e.getContent();
                }

                return previewContent*/
            },
            onSave: function(e) {},
            onChange: function(e){},
            onFocus: function(e) {},
            onBlur: function(e) {}
        })

    });


});


function sendAjaxRequest( url, aData, bFadeLoading)
{
    var $body = $('body');

    toastr.clear();

    if(typeof bFadeLoading !== "boolean"){
        bFadeLoading=true;
    }
    if(bFadeLoading){ $("#loading").fadeIn(); }

    isSendingForm = true;

    var paramAjax = {
        "type": "post",
        "data": aData,
        xhr: function(){
            //upload Progress
            var xhr = $.ajaxSettings.xhr();
            if (xhr.upload) {
                xhr.upload.addEventListener('progress', function(event) {
                    progress_bar_id='#progress-wrp';
                    var percent = 0;
                    var position = event.loaded || event.position;
                    var total = event.total;
                    if (event.lengthComputable) {
                        percent = Math.ceil(position / total * 100);
                    }
                    //update progressbar
                    $(progress_bar_id +" .progress-bar").css("width", + percent +"%");
                    $(progress_bar_id + " .status").text(percent +"%");
                }, true);
            }
            return xhr;
        }
    };

    if( aData instanceof FormData )
    {
        paramAjax.contentType = false;
        paramAjax.processData = false;
    }

    $.ajax(url, paramAjax).done(function (response)
    {
        isSendingForm = false;

        var nTimeFade = 1;
        if (response.hasOwnProperty("durationFade"))
        {
            nTimeFade = response.durationFade;
        }

        var nTimeMessage = 3000;
        if (response.hasOwnProperty("durationMessage"))
        {
            nTimeMessage = response.durationMessage;
        }

        var nTimeRedirect = 1;
        if (response.hasOwnProperty("durationRedirect"))
        {
            nTimeRedirect = response.durationRedirect;
        }

        if(bFadeLoading){setTimeout(function () {
            $("#loading").fadeOut();
        }, nTimeFade);}

        if (response.hasOwnProperty("type"))
        {
            if (response.type == "message")
            {

                toastr.options.timeOut = nTimeMessage;
                if(response.message.type=="error"){
                    toastr.options.timeOut = 10000;
                }
                toastr[response.message.type](response.message.text, response.message.title);
            }

            if(response.type == "refresh-state-list" )
            {
                //reaffiche le bouton sur la fiche
                $('[data-id-candidature="'+response.id+'"]').removeClass("offline").removeClass("online").addClass(response.class);

                //reactive le bouton dans la liste
                var $button= $('button[data-id-button-candidature="'+response.id+'"]');
                if($button.length>0){
                    if(response.class=="online"){
                        $button.removeClass("jsLink");
                        $button.prop("disabled",true);
                    }else{
                        $button.addClass("jsLink");
                        $button.prop("disabled",false);
                    }
                }


            }

            if(response.type == "refresh-delete-list" )
            {
                $('[data-id-candidature="'+response.id+'"]').remove();
            }
        }

        if (response.hasOwnProperty("required"))
        {
            if (response.required.length)
            {
                $.each(response.required, function (i)
                {
                    var $field = $('[name="' + response.required[i].field + '"]');
                    $field.closest(".form-group").addClass("has-error");
                    $field.off("click.required").on("click.required", function ()
                    {
                        $(this).off("click.required");
                        $(this).closest(".form-group").removeClass("has-error");
                    })
                });
            }
        }

        if (response.hasOwnProperty("redirect")) {
            if (response.redirect) {
                isSendingForm = true;
                setTimeout(function () {
                    document.location.href = response.redirect;
                }, nTimeRedirect);
            }
        }

        if(!response.hasOwnProperty("type")){

            var $updatedPage = jQuery(response);
            $(".updatableContent[data-updateIndex]").each(function (i, content)
            {

                var $currentContent = $(content);
                var updateIndex =  $currentContent.attr("data-updateIndex") ;


                var $updatedContent = $updatedPage.find(".updatableContent[data-updateIndex='"+ updateIndex +"']");

                if( $updatedContent.length>0 )
                {

                    $currentContent.replaceWith($updatedContent);
                    $('[data-toggle]').bootstrapToggle();


                }

            });
        }



    }).fail( function(response)
    {
        isSendingForm = false;
        var div = $("<div>").html(JSON.stringify(response));
        div.addClass("error");
        $body.append(div);
        if(bFadeLoading){setTimeout(function () {
            $("#loading").fadeOut();
        }, 200)}
    });

}



(function($)
{

    $.fn.setValue = function (value) {
        var $e = $(this);
        $e.val(value).attr("value", value);
        return $e;
    };

    $.fn.getValue = function () {
        var $e = $(this);
        return $e.val();
    };

    $.fn.form__parse2String = function(){
        var sParam="";
        var form = $(this);
        form.find("input").each(function(index){

            if(
                ($(this).attr('type')=="checkbox" && $(this).is(':checked') )
                ||
                ($(this).attr('type')!="checkbox" && $(this).attr('type')!="radio")
                ||
                ($(this).attr('type')=="radio" && $(this).is(':checked') )
            ){
                sParam+="&"+$(this).attr('name')+'='+encodeURIComponent($(this).val());
            }
        });

        form.find("textarea").each(function(index){
            sParam+="&"+$(this).attr('name')+'='+encodeURIComponent($(this).val());

        });
        form.find("select").each(function(index){
            sParam+="&"+$(this).attr('name')+'='+encodeURIComponent($(this).val());

        });
        return sParam;
    };

    $.fn.parseForm = function()
    {
        return $(this).form__parse2String();
    };

    $.fn.parseFormObject = function()
    {
        return $(this).form__parse2Object();
    };

    $.fn.form__parse2Object = function(){
        var sParam="",
            form = $(this),
            oDatas = {};

        form.find("input").each(function(index)
        {
            if( ($(this).attr('type')=="checkbox" && $(this).is(':checked') )
                ||
                ($(this).attr('type')!="checkbox" && $(this).attr('type')!="radio")
                ||
                ($(this).attr('type')=="radio" && $(this).is(':checked') ) )
            {
                var t = $(this);
                oDatas[ t.attr('name') ] = t.val();
            }
        });

        form.find("textarea, select").each(function(index)
        {
            var t = $(this);
            oDatas[ t.attr('name') ] = t.val();
        });

        return oDatas;
    };

    $.fn.getFormDatas = function()
    {
        return $(this).form__getDatasInObject();
    };

    $.fn.serializeObject = function()
    {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    $.fn.form__getDatasInObject = function()
    {
        var $form = $(this);
        var aDatas = {};
        var formdata = (window.FormData) ? new FormData($form[0]) : null;
        var data = (formdata !== null) ? formdata : $form.serialize();
        return data;
    };



})(jQuery);







