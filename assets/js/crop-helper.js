var $modal = $('#modal-crop');
var image = document.getElementById('image');
var cropper;

$("body").on("change", ".file-img.image-crop", function (e) {
    var width = $(this).data('width');
    var height = $(this).data('height');
    var isRound = $(this).data('round');
    if (isRound != null && isRound != '' && isRound != undefined) {
        $modal.addClass('crop-round');
    } else {
        $modal.removeClass("crop-round");
    }
    var parentForm = $(this).closest('.content-result-crop').attr('id');
    $modal.find('input[name="parent_form"]').val("#" + parentForm);
    $modal.find('input[name="width"]').val(width);
    $modal.find('input[name="height"]').val(height);
    var files = e.target.files;
    var done = function (url) {
        image.src = url;
        $modal.modal('show');
    };
    var reader;
    var file;
    var url;

    if (files && files.length > 0) {
        file = files[0];

        if (URL) {
            done(URL.createObjectURL(file));
        } else if (FileReader) {
            reader = new FileReader();
            reader.onload = function (e) {
                done(reader.result);
            };
            reader.readAsDataURL(file);
        }
    }
});

$modal.on('shown.bs.modal', function () {

    var width = $modal.find('input[name="width"]').val();
    var height = $modal.find('input[name="height"]').val();
    cropper = new Cropper(image, {
        aspectRatio: width != null && width != '' ? parseInt(width) / parseInt(height) : WIDTH_CROP / HEIGHT_CROP,
        viewMode: 0,
        preview: '.preview',
        cropBoxResizable: false,
        minCropBoxWidth: width != null && width != '' ? parseInt(width) : WIDTH_CROP,
        minCropBoxHeight: height != null && height != '' ? parseInt(height) : HEIGHT_CROP,
        data: {
            width: width != null && width != '' ? parseInt(width) : WIDTH_CROP,
            height: height != null && height != '' ? parseInt(height) : HEIGHT_CROP,
        }
    });

}).on('hide.bs.modal', function () {
    if (cropper != null && cropper != undefined) {
        cropper.destroy();
        cropper = null;
    }
    var parentForm = $modal.find('input[name="parent_form"]').val();
    $(parentForm).find("input.base_64_str").val("");
    $(parentForm).find('.btn-inputfile').find('span').html("Imagem");
    $(parentForm).find('.image-crop').val("");
});

$("#modal-crop #crop").click(function () {
    var width = $modal.find('input[name="width"]').val();
    var height = $modal.find('input[name="height"]').val();
    canvas = cropper.getCroppedCanvas({
        width: width != null && width != '' ? parseInt(width) : WIDTH_CROP,
        height: height != null && height != '' ? parseInt(height) : HEIGHT_CROP,
        imageSmoothingQuality: "medium",
        fillColor: '#fff'
    });

    canvas.toBlob(function (blob) {
        url = URL.createObjectURL(blob);
        var reader = new FileReader();
        reader.readAsDataURL(blob);
        reader.onloadend = function () {
            var base64data = reader.result;
            var parentForm = $modal.find('input[name="parent_form"]').val();
            var titleImg = $(parentForm).find('.btn-inputfile').find('span').html();
            $modal.modal('hide');
            $(parentForm).find("input.base_64_str").val(base64data);
            $(parentForm).find('.btn-inputfile').find('span').html(titleImg);
        }
    }, "image/jpeg", 0.8);
});

function rotate(val){
    val = parseInt(val);
    if (cropper != null && cropper != undefined){
        cropper.rotate(val);
    }
}