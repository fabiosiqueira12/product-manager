function submitData(event, myForm) {
    var urlAjax = myForm.dataset.url;
    event.preventDefault();
    var elements = myForm.elements;
    var data = {};
    for (i = 0; i < elements.length; i++) {
        if (elements[i].getAttribute('type') != 'submit' && elements[i].getAttribute('type') != 'button') {
            var nameAtribute = elements[i].getAttribute('name');
            var value = myForm.elements[i].value;
            data[nameAtribute] = value;
        }
    }
    sendData(BASE_URL + urlAjax, data, true);
}

function sendData(urlPost, data, withLoading = false) {
    CKupdate();
    var loading = document.getElementById("loader");
    if (withLoading) {
        loading.style.display = "block";
    }
    axios({
        method: 'post', // verbo http
        url: urlPost, // url
        data: data
    })
    .then(response => {
        loading.style.display = "none";
        var response = response.data;
        return returnResposta(response, null, urlPost, null);
    })
    .catch(error => {
        loading.style.display = "none";
        console.log(error);
    });
}

//Retorna dados que vier do controller
function getData(urlGet, withLoading = false) {
    var loading = document.getElementById("loader");
    if (withLoading) {
        loading.style.display = "block";
    }
    axios({
        method: 'post', // verbo http
        url: urlPost, // url
        data: data
    })
        .then(response => {
            loading.style.display = "none";
            return response.data;
        })
        .catch(error => {
            loading.style.display = "none";
            console.log(error);
        });
}

$(".form-search").on('submit', function (e) {
    e.preventDefault();
    var form = $(this);
    var url = $(form).data('url');
    $.ajax({
        type: "POST",
        url: BASE_URL + url,
        data: $(form).serializeArray(),
        beforeSend: function () {
            $("#loader").show();
        },
        success: function (response) {
            $(form).closest('body').find('.list-box').html(response);
        },
        error: function (response) {
            console.log(response);
        }
    }).always(function () {
        $("#loader").hide();
    });
});

$(".form-search-new").on('submit', function (e) {
    e.preventDefault();
    var form = $(this);
    var url = $(form).data('url');
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
});

$(".form-file").on('submit', function (e) {
    e.preventDefault();
    var form = $(this);
    CKupdate();
    var url = $(this).data('url');
    var actionForm = $(this).data('action');

    if (url === undefined || url === "" || url === null) {
        return Swal("Erro!", "Erro inesperado, tente novamente", "error");
    }
    $("#loader").show();
    axios({
        method: 'post', // verbo http
        url: BASE_URL + url, // url
        data: new FormData(this)
    })
        .then(response => {
            $("#loader").hide();
            var response = response.data;
            return returnResposta(response, form, url, actionForm);
        })
        .catch(error => {
            $("#loader").hide();
            console.log(error);
        });

});


//file type validation
$(".file-img").change(function () {
    var element = $(this);
    var file = this.files[0];
    if (file != undefined) {
        var imagefile = file.type;
        console.log(imagefile);
        var match = ["image/jpeg", "image/png", "image/jpg", "image/gif", "image/x-icon", "image/svg+xml", "image/vnd.microsoft.icon"];
        if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2]) || (imagefile == match[3]) || (imagefile == match[4]) || (imagefile == match[5])) || (imagefile == match[6])) {
            Swal(
                'Atenção!',
                'Selecione um arquivo válido (JPEG/JPG/PNG/SVG/ICO).',
                'warning'
            );
            $(element).val('');
            return false;
        } else {
            if ($(this).hasClass("change-img")) {
                var imgRef = $(this).data('ref');
                var reader = new FileReader();
                reader.onload = function (e) {
                    $(imgRef).attr('src', e.target.result);
                    $(".btn-crop-image").attr('data-src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        }
    } else {
        if ($(this).hasClass("change-img")) {
            var imgRef = $(this).data('ref');
            var reader = new FileReader();
            reader.onload = function (e) {
                $(imgRef).attr('src', e.target.result);
            }
            if (file != undefined) {
                reader.readAsDataURL(file);
            } else {
                $(imgRef).attr('src', BASE_URL + '/assets/img/default.jpg');
            }
        }
    }
});

function editClick(element) {
    if ($(element).data('link') != undefined) {
        window.location.href = BASE_URL + $(element).data('link');
    } else {
        $('.mask-cpf').mask('000.000.000-00', {
            reverse: true
        });
        $('.mask-telefone').mask('(00) 00000-0000');
        $(".mask-cnpj").mask("00.000.000/0000-00");
        $('.mask-cep').mask('00000-000');
        mask2($(".mask-valor").prop('placeholder', '0,00'), '[###.]###,##');
        var modalTarget = $(element).data('target');
        var data = $(element).data('editar');
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
    }
}

function questionClick(element) {

    var textAlert = $(element).data('textalert');
    var titleAlert = $(element).data('titlealert');
    Swal({
        title: titleAlert != undefined && titleAlert != '' ? titleAlert : 'Atenção!',
        text: textAlert != undefined && textAlert != '' ? textAlert : "Você tem certeza que quer ativar esse registro?",
        type: 'warning',
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não'
    }).then((result) => {

        var ref = $(element).data('ref');
        if (ref === undefined || ref === null || ref === "") {
            alert('Erro inesperado, recarregue a página');
        }
        var link = $(element).attr('data-link');
        var iduser = $(element).attr('data-iduser');

        var status = undefined;

        if (result.value) {
            status = 1;
        } else {
            if (result.dismiss == "cancel") {
                status = 2;
            }
        }

        if (status != undefined) {
            $.ajax({
                type: "POST",
                url: BASE_URL + link,
                data: {
                    ref: ref,
                    status: status,
                    id_user: iduser
                },
                dataType: 'json',
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function (response) {
                    return returnResposta(response, null, null, null);
                },
                error: function (response) {
                    console.log(response);
                }
            }).always(function () {
                $("#loader").hide();
            });
        }

    });
}

function removeClick(element) {
    var textAlert = $(element).data('textalert');
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
            var ref = $(element).data('excluir');
            if (ref === undefined || ref === null || ref === "") {
                alert('Erro inesperado, recarregue a página');
            }
            var link = $(element).attr('data-link');
            $.ajax({
                type: "POST",
                url: BASE_URL + link,
                data: {
                    ref: ref
                },
                dataType: 'json',
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function (response) {
                    return returnResposta(response, null, null, null);
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
}

function activeClick(element) {
    var link = $(element).data('link');
    var ref = $(element).data('ref');
    $.ajax({
        type: "POST",
        url: BASE_URL + link,
        data: { ref: ref },
        dataType: 'json',
        beforeSend: function () {
            $("#loader").show();
        },
        success: function (response) {
            return returnResposta(response, null, null, null);
        },
        error: function (response) {
            console.log(response);
        }
    }).always(function () {
        $("#loader").hide();
    });
}

function CKupdate() {
    if (typeof CKEDITOR != "undefined") {
        for (instance in CKEDITOR.instances)
            CKEDITOR.instances[instance].updateElement();
    }
}

$(".nave-pagination .page-link").on('click', function (e) {
    var button = $(this);
    var nav = $(this).closest('.nave-pagination');
    var url = $(nav).data('url');
    var page = $(this).data('page');
    var forpage = $(nav).data('forpage');
    var form = $(this).closest('body').find('.form-search');
    var data = $(form).serializeArray();
    data.push({
        name: 'page',
        value: page
    });
    data.push({
        name: 'forpage',
        value: forpage
    });
    $(nav).attr('data-actual', page);
    $(nav).find('.page-link').each(function () {
        $(this).parent().removeClass('active');
    });
    $(button).parent().addClass('active');
    $.ajax({
        type: "POST",
        url: BASE_URL + url,
        data: data,
        beforeSend: function () {
            $("#loader").show();
        },
        success: function (response) {
            $(button).closest('body').find('.list-box').html(response);
        },
        error: function (response) {
            console.log(response);
            return Swal('Erro!', response.responseJSON.message, 'error');
        }
    }).always(function () {
        $("#loader").hide();
    });
});

function make_paginate(element) {
    var button = $(element);
    if ($(button).parent().hasClass('active')) {
        return false;
    }
    var nav = $(element).closest('.nave-pagination-new');
    var url = $(nav).data('url');
    var page = $(element).data('page');
    var forpage = $(nav).data('forpage');
    var form = $(element).closest('body').find('.form-search-new');
    $(form).find('input[name="page"]').val(page);
    var data = $(form).serializeArray();
    data.push({
        name: 'page',
        value: page
    });
    data.push({
        name: 'forpage',
        value: forpage
    });
    $(nav).attr('data-actual', page);
    $(nav).find('.page-link').each(function () {
        $(element).parent().removeClass('active');
    });
    $(button).parent().addClass('active');
    $.ajax({
        type: "POST",
        url: BASE_URL + url,
        data: data,
        beforeSend: function () {
            $("#loader").show();
        },
        success: function (response) {
            $(button).closest('body').find('.content-box-paginate').html(response);
        },
        error: function (response) {
            console.log(response);
            return Swal('Erro!', response.responseJSON.message, 'error');
        }
    }).always(function () {
        $("#loader").hide();
    });
}


function getCidadesByUF(element) {
    var val = $(element).val();
    if (val != "") {
        $.ajax({
            type: "POST",
            url: BASE_URL + "/estados/getCidadesUf",
            data: { uf: val },
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (response) {
                var res = JSON.parse(response);
                if (res.result = 0) {
                    return Swal('Erro!', response.message, 'error');
                } else {
                    var list = res.list;
                    var htmlList = $("body").find('.select-cidades').find('option');
                    $.each(htmlList, function (i, v) {
                        if ($(v).val() != '') {
                            $(v).remove();
                        }
                    });
                    for (let i = 0; i < list.length; i++) {
                        $("body").find(".select-cidades").append(
                            '<option value="' + list[i].title + '">' + list[i].title + '</option>'
                        );
                    }
                }
            },
            error: function (response) {
                console.log(response);
            }
        }).always(function () {
            $("#loader").hide();
        });
    } else {
        var htmlList = $("body").find('.select-cidades').find('option');
        $.each(htmlList, function (i, v) {
            if ($(v).val() != '') {
                $(v).remove();
            }
        });
    }

}

function getCidades(element) {
    var val = $(element).val();
    if (val != "") {
        $.ajax({
            type: "POST",
            url: BASE_URL + "/estados/getCidades",
            data: { id_estado: val },
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (response) {
                var res = JSON.parse(response);
                if (res.result = 0) {
                    return Swal('Erro!', response.message, 'error');
                } else {
                    var list = res.list;
                    var htmlList = $("body").find('.select-cidades').find('option');
                    $.each(htmlList, function (i, v) {
                        if ($(v).val() != '') {
                            $(v).remove();
                        }
                    });
                    for (let i = 0; i < list.length; i++) {
                        $("body").find(".select-cidades").append(
                            '<option value="' + list[i].id + '">' + list[i].title + '</option>'
                        );
                    }
                }
            },
            error: function (response) {
                console.log(response);
            }
        }).always(function () {
            $("#loader").hide();
        });
    } else {
        var htmlList = $("body").find('.select-cidades').find('option');
        $.each(htmlList, function (i, v) {
            if ($(v).val() != '') {
                $(v).remove();
            }
        });
    }

}

function copyClick(element) {
    if ($(element).data('link') != undefined) {
        window.location.href = BASE_URL + $(element).data('link');
    } else {
        $('.mask-cpf').mask('000.000.000-00', {
            reverse: true
        });
        $('.mask-telefone').mask('(00) 00000-0000');
        $(".mask-cnpj").mask("00.000.000/0000-00");
        $('.mask-cep').mask('00000-000');
        mask2($(".mask-valor").prop('placeholder', '0,00'), '[###.]###,##');
        var modalTarget = $(element).data('target');
        var data = $(element).data('editar');
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
            } else if (key == 'texto' || key == 'description') {
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
        $(modalTarget).find('input[name="id"]').val('');
        $(modalTarget).find('input[name="token"]').val('');
        $(modalTarget).modal('toggle');
    }
}

function closeSwal() {
    Swal.close();
}

/**
 * Retorna a resposta do AJAX
 * @param {*} response 
 * @param {*} form 
 * @param {*} url 
 * @param {*} actionForm 
 */
function returnResposta(response, form = null, url = null, actionForm = null) {

    console.log(response);

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
        case 'list_modal':
            $(form).find('.content-list').load(BASE_URL + response.link_list,
                function () { });
            break;
        case 'search':
            Swal(
                'Sucesso!',
                response.message,
                'success'
            );
            $('.modal').modal('hide');
            $('body').find('.form-search-new').trigger('submit');
            break;
        case 'files':
            Swal({
                title: 'Sucesso!',
                text: response.message,
                type: 'success',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#3085d6'
            }).then((resultSwal) => {
                if (resultSwal.value) {
                    $(form).modal('hide');
                    var ref = $('body').find('.box-files').data('ref');
                    var link = $('body').find('.box-files').data('link');
                    $.ajax({
                        type: "POST",
                        url: BASE_URL + link,
                        data: { ref: ref },
                        beforeSend: function () {
                            $("#loader").show();
                        },
                        success: function (response) {
                            $('body').find('.box-files').html(response);
                        },
                        error: function (response) {
                            console.log(response);
                        }
                    }).always(function () {
                        $("#loader").hide();
                    });
                }
            });
            break;
        case 'images':
            Swal({
                title: 'Sucesso!',
                text: response.message,
                type: 'success',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#3085d6'
            }).then((resultSwal) => {
                if (resultSwal.value) {
                    $(".content-crop").removeClass('active');
                    $("body").removeClass('active-crop');
                    $(form).modal('hide');
                    var ref = $('body').find('.box-imagens').data('ref');
                    var link = $('body').find('.box-imagens').data('link');
                    $.ajax({
                        type: "POST",
                        url: BASE_URL + link,
                        data: { ref: ref },
                        beforeSend: function () {
                            $("#loader").show();
                        },
                        success: function (response) {
                            $('body').find('.box-imagens').html(response);
                        },
                        error: function (response) {
                            console.log(response);
                        }
                    }).always(function () {
                        $("#loader").hide();
                    });
                }
            });
            break;
        case 'variacoes':
            Swal({
                title: 'Sucesso!',
                text: response.message,
                type: 'success',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#3085d6'
            }).then((resultSwal) => {
                $(form).modal('hide');
                var ref = $('body').find('.box-variacoes').data('ref');
                var link = $('body').find('.box-variacoes').data('link');
                $.ajax({
                    type: "POST",
                    url: BASE_URL + link,
                    data: { ref: ref },
                    beforeSend: function () {
                        $("#loader").show();
                    },
                    success: function (response) {
                        $('body').find('.box-variacoes').html(response);
                    },
                    error: function (response) {
                        console.log(response);
                    }
                }).always(function () {
                    $("#loader").hide();
                });
            });
            break;
        case 'modal_post':
            Swal({
                title: 'Sucesso!',
                text: response.message,
                type: 'success',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#3085d6'
            }).then((resultSwal) => {
                if (resultSwal.value) {
                    var ref = $(form).closest('body').find('.content-list').data('ref');
                    var link = $(form).closest('body').find('.content-list').data('link');
                    var modal = $(form).closest('.modal');
                    $.ajax({
                        type: "POST",
                        url: BASE_URL + link,
                        data: { ref: ref },
                        beforeSend: function () {
                            $("#loader").show();
                        },
                        success: function (response) {
                            $(modal).find('.content-list').html(response);
                            var btns = $(modal).find(".content-list").find('tr').find('.btn');
                            for (var i = 0; i < btns.length; i++) {
                                var link = $(btns[i]).data('link');
                                $(btns[i]).attr('data-link', link + '/' + ref);
                            }
                        },
                        error: function (response) {
                            console.log(response);
                        }
                    }).always(function () {
                        $(modal).modal('show');
                        $("#loader").hide();
                    });
                }
            });

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
            $('.modal').modal('hide');
            $('body').find('.list-box').addClass('loading');
            var listUrl = BASE_URL + $('body').find('.list-box').data('list');
            $('body').find('.list-box').load(listUrl,
                function () {
                    $('body').find('.list-box').removeClass('loading');
                });
            break;
        case 'list_box':
            $('.modal').modal('hide');
            var element = '.content-box-paginate';
            $('body').find(element).addClass('loading');
            var listUrl = BASE_URL + $('body').find(element).data('list');
            $('body').find(element).load(listUrl,
                function () {
                    $('body').find(element).removeClass('loading');
                });
            break;
        case 'preview':
            var item = '.list-preview';
            var listUrl = BASE_URL + $('body').find(item).data('list');
            $('body').find(item).load(listUrl,
                function () {

                });
            break;
        case 'list_new':
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
        case 'modal':
            Swal({
                title: 'Sucesso!',
                text: response.message,
                type: 'success',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#3085d6'
            }).then((resultSwal) => {
                if (resultSwal.value) {
                    $(".modal").modal('hide');
                }
            });
            break;
        case 'new':
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
        case 'message':
            Swal(
                'Sucesso!',
                response.message,
                'success'
            );
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