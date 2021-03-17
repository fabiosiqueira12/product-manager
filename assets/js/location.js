var Local = (function () {
    var $cep, $lat, $lng, $city, $state;

    var init = function () {
        $cep = $('input[name=cep]');
        $lat = $('input[name=latitude]');
        $lng = $('input[name=longitude]');
        $logradouro = $('input[name=logradouro]');
        $bairro = $('input[name=bairro]');
        $city = $('input[name=cidade]');
        $state = $('input[name=estado]');

        $cep.blur(function () {
            var cep = $(this).val().replace(/\D/g, '');
            
            if (/^[0-9]{8}$/.test(cep)) {
                getGeolationByCep(cep,$(this));
            }
        });
    }

    var getLocationByCep = function (cep) {
        return $.getJSON('https://viacep.com.br/ws/' + cep + '/json/')
            .then(function (response) { 
                return {
                    city: response.localidade,
                    state: response.uf,
                    logradouro: response.logradouro,
                    bairro : response.bairro
                } 
            })
    }

    var getLatAndLngByAddress = function (address) {
        return $.get('https://maps.googleapis.com/maps/api/geocode/json?address=' + address + '&key=' + window.KEY_GOOGLEMAPS)
            .then(function (response) {
                var location =  response.results[0].geometry.location;
                return {
                    lat: location.lat,
                    lng: location.lng
                }
            });
    }

    var getGeolationByCep = function (cep,el) {
        getLocationByCep(cep)
            .then(function (location) {
                var parent = $(el).closest('.endereco-group');
                $(parent).find('input[name="cidade"]').val(location.city);
                $(parent).find('input[name="estado"]').val(location.state);
                $(parent).find('input[name="logradouro"]').val(location.logradouro);
                $(parent).find('input[name="bairro"]').val(location.bairro);        
                /* var address = location.logradouro + ', ' + location.bairro + ', ' + location.city + "-" + location.state; 
                getLatAndLngByAddress(address)
                    .then(function (geo) {
                        $lat.val(geo.lat);
                        $lng.val(geo.lng);  
                    }); */
            });
    }

    return {
        init: init
    }
})();

$(document).ready(Local.init);