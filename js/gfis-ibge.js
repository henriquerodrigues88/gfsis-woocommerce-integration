jQuery(document).ready(function($) {
    function fetchIbgeCodes(state, callback) {
        var data = {
            action: 'gfis_get_ibge_codes',
            state: state
        };

        $.post(gfis_ajax_object.ajax_url, data, function(response) {
            if (response.success) {
                var $ibgeField = $('select#codigoibge');
                if ($ibgeField.length) {
                    $ibgeField.empty();
                    $ibgeField.append($('<option>', {
                        value: '',
                        text: 'Selecione uma opção'
                    }));
                    $.each(response.data, function(code, city) {
                        $ibgeField.append($('<option>', {
                            value: code,
                            text: city
                        }));
                    });
                    $ibgeField.closest('.form-row').show();
                }
                if (typeof callback === 'function') {
                    callback();
                }
            }
        });
    }

    $('#billing_state').change(function() {
        var state = $(this).val();
        if (state) {
            fetchIbgeCodes(state);
        }
    });

    var initialState = $('#billing_state').val();
    if (initialState) {
        fetchIbgeCodes(initialState);
    }

    // Adiciona evento para garantir que os valores sejam enviados
    $('form.checkout').on('submit', function() {
        var codigoIbge = $('#codigoibge').val();
        var tipoValidacao = $('#tipovalidacao').val();
        var formaPagamento = $('#formapagamento').val();
        console.log('Enviando Código IBGE:', codigoIbge);
        console.log('Enviando Tipo de Validação:', tipoValidacao);
        console.log('Enviando Forma de Pagamento:', formaPagamento);
    });
});