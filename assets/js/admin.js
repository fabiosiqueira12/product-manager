var admin = {

    init: function () {

        this.maskInputs();

        $(".btn-inputfile").change(function (e) {
            if (e.target.files.length > 0) {
                console.log(e.target.files);
                if (e.target.files.length > 1) {
                    var fileName = e.target.files.length + " arquivos";
                } else {
                    var fileName = e.target.files[0].name;
                }
                $(this).find('span').text(fileName);
            } else {
                $(this).find('span').text($(this).find('span').data('title'));
            }
        });

        $(".block-text").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 67, 86]) !== -1 ||
                // Allow: Ctrl+A, Command+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        //ADD ATTR FORM DATA TO FORMS
        $(".form-file").each(function () {
            $(this).attr("enctype", "multipart/form-data");
            $(this).attr('onsubmit', 'admin.sendData(this,event);');
        });

        $(".form-search-admin").each(function () {
            $(this).attr("enctype", "multipart/form-data");
            $(this).attr('onsubmit', 'admin.search(this,event);');
        });

        $(".btn-new").on("click", function () {
            var target = $(this).data('target');
            if (target == null || target == undefined || target == "") {
                return Swal("Erro!", "O modal não foi encontrado", "error");
            }
            $(target).find("[name='id']").val("");
            $(target).find('[type="text"],[type="tel"],[type="number"],[type="date"],[type="datetime"],select').val("");
            $(target).modal("show");
        });

    },

    maskInputs: function () {

        $('.mask-cpf').mask('000.000.000-00', { reverse: true });
        $(".mask-cnpj").mask("00.000.000/0000-00");
        $(".mask-ncm").mask("0000.00.00");
        $('.mask-cep').mask('00000-000');
        $('.mask-percent').mask('00,00');
        $('.mask-peso').mask("#0.000", { reverse: true });
        var SPMaskBehavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        },
            spOptions = {
                onKeyPress: function (val, e, field, options) {
                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                }
            };

        $('.mask-telefone').mask(SPMaskBehavior, spOptions);
        mask2($(".mask-valor").prop('placeholder', '0,00'), '[###.]###,##');
        $('.money').mask('000.000.000.000,00', { reverse: true });
    },

    numberToReal: function (value) {
        var value = value.toFixed(2).split('.');
        value[0] = value[0].split(/(?=(?:...)*$)/).join('.');
        return value.join(',');
    },

    editClick: function (element) {

        if ($(element).data('link') != undefined) {
            return window.location.href = BASE_URL + $(element).data('link');
        }

        admin.maskInputs();

        var modalTarget = $(element).data('target');
        if (modalTarget == null || modalTarget == undefined || modalTarget == "") {
            return Swal("Erro!", "o target não foi encontrado", "error");
        }

        var data = $(element).data('editar');
        if (data == null || data == undefined || data == "") {
            return Swal("Erro!", "os dados não foram encontrados", "error");
        }

        $.each(data, function (key, val) {

            if (typeof val == 'object') {
                if (val instanceof Array) {
                    var options = $(modalTarget).find('select[name=' + key + '\\[\\]]').find("option");
                    for (let index = 0; index < val.length; index++) {
                        $.each(options, function (k, v) {
                            if ($(v).attr('value') == val[index]) {
                                $(v).attr('selected', true);
                                return false;
                            }
                        });
                    }
                }
                if (key != 'agencia') {
                    $.each(val, function (k, v) {
                        $(modalTarget).find('input[name=' + k + ']').val(v);
                        $(modalTarget).find('textarea[name=' + k + ']').val(v);
                        $(modalTarget).find('select[name=' + k + ']').val(v);
                    });
                }
            } else if (key == 'texto' || key == 'description' || key == 'texto_completo') {
                $(modalTarget).find('textarea[name=' + key + ']').val(val);
                $(modalTarget).find('input[name=' + key + ']').val(val);
                CKupdate();
            } else if (key == 'date_publish' || key == 'date_insert' || key == 'date_update' || key == "data_abertura" || key == 'date_birth') {
                var date = val.split(" ");
                $(modalTarget).find('input[name=' + key + ']').val(date[0]);
            } else {
                $(modalTarget).find('input[name=' + key + ']').val(val);
                $(modalTarget).find('textarea[name=' + key + ']').val(val);
                $(modalTarget).find('select[name=' + key + ']').val(val);
            }
        });

        $(modalTarget).modal('toggle');

    },

    removeClick: function (element) {
        var textAlert = $(element).data('textalert');
        var ref = $(element).data('excluir');
        if (ref === undefined || ref === null || ref === "") {
            return Swal("Erro!", 'Erro inesperado, a referência para remover não foi encontrada', "error");
        }
        var link = $(element).attr('data-link');
        if (link === undefined || link === null || link === "") {
            return Swal("Erro!", 'Erro inesperado, o link para remover não foi encontrado', "error");
        }
        Swal({
            title: 'Atenção!',
            text: textAlert != undefined && textAlert != '' ? textAlert : "Você tem certeza que quer remover esse registro?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: "POST",
                    url: BASE_URL + link,
                    data: {
                        ref: ref
                    },
                    beforeSend: function () {
                        $("#loader").show();
                    },
                    success: function (response) {
                        return admin.returnResult(response, null, null, null);
                    },
                    error: function (response) {
                        console.log(response);
                        return Swal('Erro!', response.responseJSON.message, 'error');
                    }
                }).always(function () {
                    $("#loader").hide();
                });
            }
        });
    },

    search: function (form, event) {

        event.preventDefault();
        var url = $(form).data('url');
        if (url == null || url == undefined || url == ""){
            return Swal("Erro!","A url de busca não foi encontrada","error");
        }
        $.ajax({
            type: "POST",
            url: BASE_URL + url,
            data: $(form).serializeArray(),
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (response) {
                if (typeof response == 'object') {
                    if (response.result == 0) {
                        return Swal('Atenção!', response.message, 'warning');
                    }
                    return Swal('Sucesso!', response.message, 'success');
                }
                $(form).closest('body').find('.content-box-paginate').html(response);
            },
            error: function (response) {
                console.log(response);
                return Swal('Erro!', response.responseJSON.message, 'error');
            }
        }).always(function () {
            $("#loader").hide();
        });

    },

    sendData: function (form, event) {
        event.preventDefault();
        var url = $(form).data('url');
        var actionForm = $(form).data('action');
        if (url === undefined || url === "" || url === null) {
            return Swal("Erro!", "Erro inesperado, tente novamente", "error");
        }
        $.ajax({
            type: "POST",
            url: BASE_URL + url,
            data: new FormData(form),
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (response) {
                return admin.returnResult(response, form, url, actionForm);
            },
            error: function (response) {
                console.log(response);
                return Swal('Erro!', response.responseJSON.message, 'error');
            }
        }).always(function () {
            $("#loader").hide();
        });
    },

    /**
     * Retorna a resposta do AJAX
     * @param {*} response 
     * @param {*} form 
     * @param {*} url 
     * @param {*} actionForm 
     */
    returnResult: function (response, form = null, url = null, actionForm = null) {

        if (IsJsonString(response) == false) {
            return Swal("Erro!", "erro interno do servidor", "error");
        }

        var response = JSON.parse(response);
        if (response.result == 0) {
            return Swal(
                'Erro!',
                response.message || 'Não foi possível concluir!',
                'error'
            );
        }

        if (actionForm != undefined && actionForm != '' || actionForm != null) {
            window.location.href = BASE_URL + actionForm;
            return false;
        }

        switch (response.action) {
            case 'reload':
                window.location.reload();
                break;
            case 'search':
                Swal(
                    'Sucesso!',
                    response.message,
                    'success'
                );
                $('.modal').modal('hide');
                $('body').find('.form-search-admin').trigger('submit');
                break;
            case 'goto':
                Swal({
                    title: 'Sucesso!',
                    text: response.message,
                    type: 'success',
                    showCancelButton: false,
                    allowOutsideClick: false,
                    confirmButtonColor: '#3085d6'
                }).then((resultSwal) => {
                    if (resultSwal.value) {
                        window.location.href = BASE_URL + response.link;
                    }
                });
                break;
            case 'list':
                Swal(
                    'Sucesso!',
                    response.message,
                    'success'
                );
                $('.modal').modal('hide');
                $('body').find(response.element).addClass('loading');
                var listUrl = BASE_URL + $('body').find(response.element).data('list');
                $('body').find(response.element).load(listUrl,
                    function () {
                        $('body').closest('body').find(response.element).removeClass('loading');
                    });
                break;
            case 'new':
                $(".modal").modal('hide');
                Swal({
                    title: 'Sucesso!',
                    text: response.message,
                    type: 'success',
                    showCancelButton: false,
                    allowOutsideClick: false,
                    confirmButtonColor: '#3085d6'
                }).then((resultSwal) => {
                    if (resultSwal.value) {
                        window.location.reload();
                    }
                });
                break;
            case 'remove':
                Swal({
                    title: 'Sucesso!',
                    text: response.message,
                    type: 'success',
                    showCancelButton: false,
                    allowOutsideClick: false,
                    confirmButtonColor: '#3085d6'
                }).then((resultSwal) => {
                    if (resultSwal.value) {
                        window.location.reload();
                    }
                });
                break;
            default:
                Swal(
                    'Sucesso!',
                    response.message,
                    'success'
                );
                break;
        }

    }
};

function CKupdate() {
    if (typeof CKEDITOR != "undefined") {
        for (instance in CKEDITOR.instances)
            CKEDITOR.instances[instance].updateElement();
    }
}

function IsJsonString(str) {
    try {
        var obj = JSON.parse(str);

        if (obj && typeof obj === "object") {
            return true;
        }
        return false;

    } catch (e) {
        return false;
    }
}

$(document).ready(function () {
    admin.init();
});