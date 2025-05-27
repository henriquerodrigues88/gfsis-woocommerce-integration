<?php
/**
 * Plugin Name: GFSIS WooCommerce Integration
 * Description: Integração do WooCommerce com o sistema GFSIS para registro de pedidos.
 * Version: 2.0.5
 * Author: Henrique Rodrigues
 */

// Impede acesso direto
defined('ABSPATH') || exit;

/**
 * Combina o nome e o sobrenome do cliente em um único campo.
 *
 * @param WC_Order $order O objeto do pedido do WooCommerce.
 * @return string O nome completo do cliente.
 */
function get_full_name($order) {
    return $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
}

add_action('woocommerce_checkout_order_processed', 'send_order_to_gfsis', 10, 1);

// Salva campos personalizados no pedido
add_action('woocommerce_checkout_update_order_meta', 'save_custom_checkout_fields');
function save_custom_checkout_fields($order_id) {
    if (!empty($_POST['billing_cpf'])) {
        update_post_meta($order_id, 'billing_cpf', sanitize_text_field($_POST['billing_cpf']));
    }
    if (!empty($_POST['billing_cnpj'])) {
        update_post_meta($order_id, 'billing_cnpj', sanitize_text_field($_POST['billing_cnpj']));
    }
    if (!empty($_POST['billing_birthdate'])) {
        update_post_meta($order_id, 'billing_birthdate', sanitize_text_field($_POST['billing_birthdate']));
    }
    if (!empty($_POST['codigoibge'])) {
        error_log('GFSIS Debug: Salvando Código IBGE: ' . $_POST['codigoibge']);
        update_post_meta($order_id, '_codigoibge', sanitize_text_field($_POST['codigoibge']));
    }
    if (!empty($_POST['billing_number'])) {
        update_post_meta($order_id, 'billing_number', sanitize_text_field($_POST['billing_number']));
    }
    if (!empty($_POST['billing_neighborhood'])) {
        update_post_meta($order_id, 'billing_neighborhood', sanitize_text_field($_POST['billing_neighborhood']));
    }
    if (!empty($_POST['tipovalidacao'])) {
        error_log('GFSIS Debug: Salvando Tipo de Validação: ' . $_POST['tipovalidacao']);
        update_post_meta($order_id, '_tipovalidacao', sanitize_text_field($_POST['tipovalidacao']));
    }
    if (!empty($_POST['formapagamento'])) {
        error_log('GFSIS Debug: Salvando Forma de Pagamento: ' . $_POST['formapagamento']);
        update_post_meta($order_id, '_formapagamento', sanitize_text_field($_POST['formapagamento']));
    }
    if (!empty($_POST['contactname'])) {
        update_post_meta($order_id, 'contactname', sanitize_text_field($_POST['contactname']));
    }
    if (!empty($_POST['billing_id'])) {
        update_post_meta($order_id, 'billing_id', sanitize_text_field($_POST['billing_id']));
    }
    if (!empty($_POST['indicacaocpf'])) {
        update_post_meta($order_id, 'indicacaocpf', sanitize_text_field($_POST['indicacaocpf']));
    }
}

// Função para limpar caracteres não numéricos
function clean_numeric_value($value) {
    return preg_replace('/\D/', '', $value); // Remove tudo que não for número
}

function send_order_to_gfsis($order_id) {
    // Obtem os detalhes do pedido
    $order = wc_get_order($order_id);

    // Função para converter data de d-m-y para Y-m-d
    function convert_date_format($date, $from_format = 'd-m-y', $to_format = 'Y-m-d') {
        $date_obj = DateTime::createFromFormat($from_format, $date);
        return $date_obj ? $date_obj->format($to_format) : '';
    }

    // Captura valores brutos para depuração
    $raw_cpf = get_post_meta($order_id, 'billing_cpf', true);
    $raw_cnpj = get_post_meta($order_id, 'billing_cnpj', true);
    $raw_birthdate = get_post_meta($order_id, 'billing_birthdate', true);
    $raw_codigo_ibge = get_post_meta($order_id, '_codigoibge', true);
    $raw_numero = get_post_meta($order_id, 'billing_number', true);
    $raw_bairro = get_post_meta($order_id, 'billing_neighborhood', true);
    $raw_tipo_validacao = get_post_meta($order_id, '_tipovalidacao', true);
    $raw_forma_pagamento = get_post_meta($order_id, '_formapagamento', true);

    // Captura valores brutos para o contato
    $raw_contact_name = get_post_meta($order_id, 'contactname', true);

    // Captura valores brutos para a indicação
    $raw_indicacao_cpf = get_post_meta($order_id, 'indicacaocpf', true);

    // Log dos valores brutos
    error_log('GFSIS Debug: CPF: ' . $raw_cpf);
    error_log('GFSIS Debug: CNPJ: ' . $raw_cnpj);
    error_log('GFSIS Debug: Data de Nascimento: ' . $raw_birthdate);
    error_log('GFSIS Debug: Código IBGE: ' . $raw_codigo_ibge);
    error_log('GFSIS Debug: Número: ' . $raw_numero);
    error_log('GFSIS Debug: Bairro: ' . $raw_bairro);
    error_log('GFSIS Debug: Tipo de Validação: ' . $raw_tipo_validacao);
    error_log('GFSIS Debug: Forma de Pagamento: ' . $raw_forma_pagamento);

    // Log dos valores do contato para depuração
    error_log('GFSIS Debug: Contato Nome: ' . $raw_contact_name);

    // Log dos valores de indicação para depuração
    error_log('GFSIS Debug: Indicação CPF: ' . $raw_indicacao_cpf);

    // Prepara dados do contato
    $contact_data = [
        'nome' => $raw_contact_name ?: '',
    ];

    // Prepara dados do cliente
    $customer_data = [
        'nome' => get_full_name($order),
        'cpf' => clean_numeric_value($raw_cpf),
        'cnpj' => clean_numeric_value($raw_cnpj),
        'email' => $order->get_billing_email(),
        'dataNascimento' => convert_date_format($raw_birthdate, 'd/m/Y', 'Y-m-d'),
        'telefone' => $order->get_billing_phone(),
        'codigoIbge' => $raw_codigo_ibge ?: '',
        'logradouro' => $order->get_billing_address_1(),
        'numero' => $raw_numero,
        'complemento' => $order->get_billing_address_2() ?: '',
        'bairro' => $raw_bairro ?: '',
        'uf' => $order->get_billing_state(),
        'municipio' => $order->get_billing_city(),
        'cep' => clean_numeric_value($order->get_billing_postcode()), // Remove o "-" do CEP
        'contato' => $contact_data, // Adiciona o objeto contato ao cliente
    ];

    // Log do cliente para depuração
    error_log('GFSIS Debug: Cliente: ' . json_encode($customer_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Recupera o valor de billing_id salvo no meta do pedido
    $order_billing_id = get_post_meta($order_id, 'billing_id', true);

    // Prepara dados do pedido
    $order_data = [
        'id' => 388580 + time() % 10000, // Gera um ID temporário para o payload inicial
        'data' => $order->get_date_created()->date('Y-m-d'),
        'pontoAtendimento' => get_post_meta($order_id, '_pontoatendimento', true) ?: 123, // Substitua 123 pelo valor correto
        'formaPagamento' => $raw_forma_pagamento ?: '',
        'tipoValidacao' => $raw_tipo_validacao ?: '',
    ];

    // Log do pedido para depuração
    error_log('GFSIS Debug: Pedido: ' . json_encode($order_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Prepara dados do certificado
    $certificado_data = [
        'id' => get_product_sku_from_order($order),
        'valor' => $order->get_total(),
    ];

    // Log do certificado para depuração
    error_log('GFSIS Debug: Certificado: ' . json_encode($certificado_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Prepara dados da indicação
    $indicacao_data = [
        'cpf' => sanitize_text_field($_POST['indicacaocpf'] ?? ''),
    ];

    // Log da indicação para depuração
    error_log('GFSIS Debug: Indicação: ' . json_encode($indicacao_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Combina todos os dados no payload
    $payload = [
        'pedido' => $order_data,
        'cliente' => $customer_data, // Adicionado objeto contato ao cliente
        'indicacao' => $indicacao_data,
        'certificado' => $certificado_data,
    ];

    // Inicializa a variável $token como null
    $token = null;

    // Verifica se já existe um token armazenado e se ele ainda é válido
    $token_data = get_transient('gfis_api_token_data');
    if ($token_data) {
        $token = $token_data['token'];
        $expiration = $token_data['expiration'];

        // Verifica se o token expirou
        if (strtotime($expiration) > time()) {
            error_log('GFSIS: Token válido recuperado do cache.');
        } else {
            error_log('GFSIS: Token expirado. Iniciando nova autenticação...');
            $token = null; // Força a renovação do token
        }
    } else {
        error_log('GFSIS: Nenhum token encontrado. Iniciando autenticação...');
    }

    // Se não houver um token válido, autentica novamente
    if (!$token) {
        $auth_url = 'https://yourdomain.gfsis.com.br/gestaofacil/rest/auth';
        $username = 'username';
        $password = 'password';
        $auth_header = 'Basic ' . base64_encode("$username:$password");

        $auth_response = wp_remote_post($auth_url, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $auth_header,
            ],
            'redirection' => 0,
        ]);

        if (is_wp_error($auth_response)) {
            error_log('GFIS Authentication Error: ' . $auth_response->get_error_message());
            return;
        }

        $auth_status_code = wp_remote_retrieve_response_code($auth_response);
        error_log('GFSIS Authentication HTTP Status: ' . $auth_status_code);

        $auth_body_raw = wp_remote_retrieve_body($auth_response);
        error_log('GFSIS Authentication Response: ' . $auth_body_raw);

        if ($auth_status_code !== 201) {
            error_log('GFSIS Authentication Failed: Credenciais inválidas ou problema na requisição.');
            return;
        }

        $auth_body = json_decode($auth_body_raw, true);
        if (empty($auth_body['accessToken']) || empty($auth_body['expirationDate'])) {
            error_log('GFSIS Authentication Failed: Token ou data de expiração não recebidos.');
            return;
        }

        $token = $auth_body['accessToken'];
        $expiration = $auth_body['expirationDate'];
        error_log('GFSIS: Token recebido com sucesso. Expira em: ' . $expiration);

        // Armazena o token e a data de expiração em um transient
        set_transient('gfis_api_token_data', [
            'token' => $token,
            'expiration' => $expiration,
        ], strtotime($expiration) - time());
    }

    // Log do payload antes de enviar
    error_log('GFSIS Payload: ' . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Envia os dados do pedido
    $order_url = 'https://yourdomain.gfsis.com.br/gestaofacil/rest/CriaPedidoVendaLTS';
    $order_response = wp_remote_post($order_url, [
        'body' => json_encode($payload),
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ],
        'redirection' => 0,
        'timeout' => 15, // Tempo limite para a requisição
    ]);

    if (is_wp_error($order_response)) {
        error_log('GFSIS Order Submission Error: ' . $order_response->get_error_message());
        return;
    }

    $order_status_code = wp_remote_retrieve_response_code($order_response); // Registra o status HTTP
    error_log('GFSIS Order Submission HTTP Status: ' . $order_status_code);

    // Log da resposta da API
    $order_body = wp_remote_retrieve_body($order_response);
    error_log('GFSIS Order Submission Response: ' . $order_body);

    // Após enviar o pedido, captura o código retornado pela GFSIS e atualiza o ID no WooCommerce
    if ($order_status_code === 201 && !empty($order_body)) {
        $response_data = json_decode($order_body, true);
        if (isset($response_data['codigo'])) {
            $gfsis_codigo = $response_data['codigo'];
            update_post_meta($order_id, 'gfsis_codigo', $gfsis_codigo);
            error_log('GFSIS: Código do pedido atualizado no WooCommerce: ' . $gfsis_codigo);

            // Atualiza o ID do pedido no WooCommerce para refletir o código da GFSIS
            $order_data['id'] = $gfsis_codigo;
        }
    }

    // Após enviar o pedido, captura o código retornado pela GFSIS e atualiza o campo billing_id
    if ($order_status_code === 201 && !empty($order_body)) {
        $response_data = json_decode($order_body, true);
        if (isset($response_data['codigo'])) {
            $gfsis_codigo = $response_data['codigo'];
            update_post_meta($order_id, 'billing_id', $gfsis_codigo); // Atualiza o campo billing_id
            error_log('GFSIS: Código do pedido atualizado no campo billing_id: ' . $gfsis_codigo);
        }
    }

    error_log('GFSIS: Pedido enviado com sucesso.');
}

/**
 * Obtém o SKU do produto principal do pedido.
 *
 * @param WC_Order $order O objeto do pedido do WooCommerce.
 * @return string|null O SKU do produto, ou null se não houver produtos.
 */
function get_product_sku_from_order($order) {
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        return $product ? $product->get_sku() : null;
    }
    return null;
}

// Carrega os códigos IBGE dinamicamente
add_action( 'wp_enqueue_scripts', 'gfis_enqueue_scripts' );
function gfis_enqueue_scripts() {
    if ( is_checkout() ) {
        wp_enqueue_script( 'gfis-ibge-script', plugins_url( '/js/gfis-ibge.js', __FILE__ ), array('jquery'), null, true );
        wp_localize_script( 'gfis-ibge-script', 'gfis_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }
}

add_action( 'wp_ajax_gfis_get_ibge_codes', 'gfis_get_ibge_codes' );
add_action( 'wp_ajax_nopriv_gfis_get_ibge_codes', 'gfis_get_ibge_codes' );
function gfis_get_ibge_codes() {
    $state = sanitize_text_field( $_POST['state'] );
    $ibge_codes = gfis_fetch_ibge_codes( $state );
    wp_send_json_success( $ibge_codes );
}

function gfis_fetch_ibge_codes( $state ) {
    $state_codes = array(
        'AC' => 12, 'AL' => 27, 'AP' => 16, 'AM' => 13, 'BA' => 29, 'CE' => 23, 'DF' => 53, 'ES' => 32, 'GO' => 52,
        'MA' => 21, 'MT' => 51, 'MS' => 50, 'MG' => 31, 'PA' => 15, 'PB' => 25, 'PR' => 41, 'PE' => 26, 'PI' => 22,
        'RJ' => 33, 'RN' => 24, 'RS' => 43, 'RO' => 11, 'RR' => 14, 'SC' => 42, 'SP' => 35, 'SE' => 28, 'TO' => 17
    );

    if ( ! isset( $state_codes[$state] ) ) {
        return array();
    }

    $state_code = $state_codes[$state];
    $api_url = "https://servicodados.ibge.gov.br/api/v1/localidades/estados/{$state_code}/municipios";
    
    $response = wp_remote_get( $api_url );
    if ( is_wp_error( $response ) ) {
        return array();
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return array();
    }

    $ibge_codes = array();
    foreach ( $data as $municipio ) {
        if (isset($municipio['id'], $municipio['nome'])) {
            $ibge_codes[$municipio['id']] = $municipio['nome'];
        }
    }

    return $ibge_codes;
}

add_action( 'wp_footer', 'gfis_custom_js' );
function gfis_custom_js() {
    if ( is_checkout() ) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            function fetchIbgeCodes(state) {
                var data = {
                    action: 'gfis_get_ibge_codes',
                    state: state
                };

                $.post(gfis_ajax_object.ajax_url, data, function(response) {
                    if (response.success) {
                        var $ibgeField = $('select#codigoibge');
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
                });
            }

            $('#billing_state').change(function() {
                var state = $(this).val();
                fetchIbgeCodes(state);
            });

            var initialState = $('#billing_state').val();
            if (initialState) {
                fetchIbgeCodes(initialState);
            }
        });
        </script>
        <?php
    }
}

// Personaliza o número do pedido exibido na página de Pedido Recebido
add_filter('woocommerce_order_number', 'customize_order_number', 10, 2);
function customize_order_number($order_number, $order) {
    // Recupera o billing_id salvo no meta do pedido
    $billing_id = get_post_meta($order->get_id(), 'billing_id', true);

    // Se o billing_id existir, usa ele como número do pedido
    if (!empty($billing_id)) {
        return $billing_id;
    }

    // Caso contrário, retorna o número padrão do WooCommerce
    return $order_number;
}
