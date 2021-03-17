var alertCustom = {
    init : function (){
        $("body").append('<div class="toast-list"></div>');
    },
    /**
    * @param {string} linkSuccess - link send when user click on button success.
    * @param {string} linkCancel - link send when user click on button cancel.
    * @param {string} message - text you can say in alert.
    * @param {string} textSuccess - change text of the button confirm.
    * @param {string} textCancel - change text of the button confirm.
    */
    confirmAlert: function(linkSuccess,linkCancel,message,textSuccess = 'Confirmar',textCancel = 'Rejeitar'){
        $('.toast-list').append(
            '<div class="toast-custom confirm">'+
                '<button class="close" type="button" onclick="alertCustom.closeToast(this)"><i class="fa fa-times"></i></button>'+
                '<p>' + message + '</p>'+
                '<div class="actions">'+
                    '<a href="' +  linkSuccess +'" class="confirm-link">'+ textSuccess +'</a>'+
                    '<a href="' + linkCancel + '" class="cancel-link">'+ textCancel +'</a>'+
                '</div>' +   
            '</div>'
        );
        if (count > 4) {
            $('.toast-list .toast-custom').first().fadeOut(function(){
                $(this).remove();
            });
        }
        $(".toast-list .toast-custom").last().fadeIn();
    },
    /**
    * @param {string} linkSuccess - link send when user click on button success.
    * @param {string} message - text you can say in alert.
    * @param {string} textSuccess - change text of the button confirm.
    */
    defaultAlert : function(linkSuccess,message,textSuccess = 'Visualizar'){
        if (linkSuccess != undefined && linkSuccess != ''){
            $('.toast-list').append(
                '<div class="toast-custom default">'+
                    '<button class="close" type="button" onclick="alertCustom.closeToast(this)"><i class="fa fa-times"></i></button>'+
                    '<p>' + message + '</p>'+
                    '<div class="actions">'+
                        '<a href="' +  linkSuccess +'" class="confirm-link">'+ textSuccess +'</a>'+
                    '</div>' +   
                '</div>'
            );
        }else{
            $('.toast-list').append(
                '<div class="toast-custom default">'+
                    '<button class="close" type="button" onclick="alertCustom.closeToast(this)"><i class="fa fa-times"></i></button>'+
                    '<p>' + message + '</p>'+
                    '<div class="actions">'+
                        '<a href="javascript:void(0)" onclick="alertCustom.closeToast(this)" class="confirm-link">'+ textSuccess +'</a>'+
                    '</div>' +   
                '</div>'
            );
        }
        
        var count = $('.toast-list .toast-custom').length;
        if (count > 4) {
            $('.toast-list .toast-custom').first().fadeOut(function(){
                $(this).remove();
            });
        }
        $(".toast-list .toast-custom").last().fadeIn();
    },
    closeToast : function(element)
    {
        $(element).closest('.toast-custom').fadeOut(function(){
            $(this).remove();
        });
    }
}

alertCustom.init();