$(document).ready(function () {
    //Verify if isset name of select input to send in form or to validation
    if ($('.dropdown-simple-select').data('input') != undefined && $('.dropdown-simple-select').data('input') != '') {

        //CREATE SELECT INPUT
        $('.dropdown-simple-select').append(
            '<select multiple name="' + $('.dropdown-simple-select').data('input') + '" required="' + $('.dropdown-simple-select').data('required') + '">' +
            '<option value=""></option>' +
            '</select>'
        );

        //CREATE OPTIONS BASE DROPDOWN
        $(".dropdown-simple-select .dropdown-menu .dropdown-item").each(function () {
            if ($(this).hasClass('active')) {
                $(".dropdown-simple-select select").append(returnOptionDropdown($(this), true));
            } else {
                $(".dropdown-simple-select select").append(returnOptionDropdown($(this)));
            }
        });

        //Remove bootstrap propagaton when click inside close the dropdown
        $('.dropdown-simple-select .dropdown-menu').on('click', function (e) {
            e.stopPropagation();
        });

        defineTextButton();

        //Click event on dropdown
        $(".dropdown-simple-select .dropdown-menu .dropdown-item").click(function () {
            var btn = $(this);
            $(btn).toggleClass('active');
            $(btn).parent().parent().find('select option').each(function () {
                if ($(btn).data('value') == $(this).attr('value')) {
                    if ($(btn).hasClass('active')) {
                        $(this).prop('selected', true);
                    } else {
                        $(this).removeAttr('selected');
                    }
                    return false;
                }
            });
            var list = returnActives(btn);
            if (list.length == 0){
                var text = $(btn).parent().parent().data('text');
                $(btn).parent().parent().find('.btn').text(text);
            }else{
                $(btn).parent().parent().find('.btn').text(list.join(','));
            }
        });

    } else {
        console.error('You need add a data Attribute data-input with name of select input to HTML5 Validation and/or send in response form');
    }

    //Return option HTML with datas attributes defines in link .dropdown-item
    function returnOptionDropdown(element, selected = false) {
        if (selected) {
            return '<option selected value="' + $(element).data('value') + '" >' + $(element).data('name') + '</option>';
        }
        return '<option value="' + $(element).data('value') + '" >' + $(element).data('name') + '</option>';
    }

    //Return count actives options
    function returnActives(element){
        var listNames = [];
        $(element).parent().find('.active').each(function () {
            listNames.push($(this).data('name'));
        });
        return listNames;
    }


    //Define text if set data-atrr data-text you can change default text top value empty here
    function defineTextButton(){
        //CHECK if define a default text to show
        if ($('.dropdown-simple-select').data('text') != '' && $('.dropdown-simple-select').data('text') != undefined) {
            $('.dropdown-simple-select').find('.btn').text($('.dropdown-simple-select').data('text'));
        } else {
            $('.dropdown-simple-select').find('.btn').text('Select');
        }
    }

});