<?php

// Função para validar se produto corresponde com CPF ou CNPJ
add_action('woocommerce_checkout_process', 'validate_person_type_and_product_category');

function validate_person_type_and_product_category() {
    // Obtém o tipo de pessoa selecionado
    $person_type = isset($_POST['billing_persontype']) ? sanitize_text_field($_POST['billing_persontype']) : '';

    // Obtém os itens do carrinho
    $cart_items = WC()->cart->get_cart();

    // Inicializa variáveis para controle
    $has_certificado_pf = false;
    $has_certificado_pj = false;

    // Verifica os produtos no carrinho
    foreach ($cart_items as $cart_item) {
        $product_id = $cart_item['product_id'];
        $terms = get_the_terms($product_id, 'product_cat');

        if ($terms) {
            foreach ($terms as $term) {
                if ($term->slug === 'certificado-pf') {
                    $has_certificado_pf = true;
                }
                if ($term->slug === 'certificado-pj') {
                    $has_certificado_pj = true;
                }
            }
        }
    }

    // Valida as condições
    if ($person_type === '1' && $has_certificado_pj) { // Pessoa Física
        wc_add_notice(__('Você não pode adquirir <strong>certificado PJ como pessoa física.</strong> Escolha <strong>certificado PF</strong> ou utilize um <strong>CNPJ.</strong>', 'woocommerce'), 'error');
    }

    if ($person_type === '2' && $has_certificado_pf) { // Pessoa Jurídica
        wc_add_notice(__('Você não pode adquirir <strong>certificado PF como pessoa jurídica.</strong> Escolha <strong>certificado PJ</strong> ou utilize um <strong>CPF.</strong>', 'woocommerce'), 'error');
    }
}
// E.O.F

// Função de alteração do texto "add to cart" na página do produto
    add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single' ); 
    function woocommerce_add_to_cart_button_text_single() {
    return __( 'Comprar', 'woocommerce' ); 
}
// E.O.F

// Função de alteração do texto "add to cart" nas páginas de arquivos dos produtos
    add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_add_to_cart_button_text_archives' );  
    function woocommerce_add_to_cart_button_text_archives() {
    return __( 'Comprar', 'woocommerce' );
}
// E.O.F

// Função de validação condicional para os campos CPF e CNPJ no checkout do WooCommerce
function validar_campos_condicionais_checkout() {
    // Verifica se o campo billing_persontype foi enviado
    if ( isset( $_POST['billing_persontype'] ) ) {
        $persontype = sanitize_text_field( $_POST['billing_persontype'] );

        // Se o cliente escolheu CPF, verifica se o campo billing_cpf foi preenchido
        if ( 'cpf' === $persontype ) {
            if ( empty( $_POST['billing_cpf'] ) ) {
                wc_add_notice( __( 'CPF é um campo obrigatório', 'woocommerce' ), 'error' );
            }
        }
        // Se o cliente escolheu CNPJ, verifica se o campo billing_cnpj foi preenchido
        elseif ( 'cnpj' === $persontype ) {
            if ( empty( $_POST['billing_cnpj'] ) ) {
                wc_add_notice( __( 'CNPJ é um campo obrigatório', 'woocommerce' ), 'error' );
            }
        }
    }
}
add_action( 'woocommerce_checkout_process', 'validar_campos_condicionais_checkout' );
// E.O.F

// Sincroniza a escolha do Select2 de forma de pagamento com os radios dos métodos Safe2Pay
add_action('wp_footer', 'safe2pay_sync_select2_to_radios');
function safe2pay_sync_select2_to_radios() {
    if ( ! is_checkout() ) return;
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // marca o radio e dispara clique no label pra abrir o <li>
        function selecionarMetodoPagamento(idRadio) {
            var input = document.getElementById(idRadio);
            if (!input) return;
            // encontra o label e dispara o clique
            var label = input.closest('label');
            if (label) label.click();
            // reforça o checked + change
            input.checked = true;
            input.dispatchEvent(new Event('change'));
        }

        // mostra ou esconde parcelas
        function atualizarParcelas(mostrar, valor) {
            var parcelas = document.getElementById("safe2pay-card-installments");
            if (!parcelas) return;
            parcelas.style.display = mostrar ? "block" : "none";
            if (mostrar && valor) parcelas.value = valor;
            parcelas.dispatchEvent(new Event('change'));
        }

        // função principal chamada ao mudar o select2
        function onFormaChange() {
            var select = document.getElementById("formapagamento");
            if (!select) return;
            var val = select.value;

            // antes de tudo, esconde parcelas
            atualizarParcelas(false);

            switch (val) {
                case "21": // Pix
                    selecionarMetodoPagamento("safe2pay-payment-method-pix");
                    break;

                case "22": // Boleto
                    selecionarMetodoPagamento("safe2pay-payment-method-bank-slip");
                    break;

                case "23": // CC 1x
                    selecionarMetodoPagamento("safe2pay-payment-method-credit-card");
                    atualizarParcelas(true, "1");
                    break;

                case "24": // CC 2x
                    selecionarMetodoPagamento("safe2pay-payment-method-credit-card");
                    atualizarParcelas(true, "2");
                    break;
            }
        }

        // hook no change do <select> e no evento do Select2
        var selectForma = document.getElementById("formapagamento");
        if (selectForma) {
            selectForma.addEventListener("change", onFormaChange);
            if (window.jQuery && jQuery().select2) {
                jQuery("#formapagamento").on("select2:select", onFormaChange);
            }
            // roda uma vez na inicialização
            onFormaChange();
        }
    });
    </script>
    <style>
    /* esconde o select de parcelas até ser necessário */
    #safe2pay-card-installments {
        display: none;
    }
    </style>
    <?php
}
// E.O.F

// Função de injeção do JS do popup no rodapé (carrinho)
add_action( 'wp_footer', 'certifika_cart_popup_refined', 999 );
function certifika_cart_popup_refined() {
    if ( is_admin() ) {
        return;
    }
    $popup_id = 1234;
    ?>
    <script>
    (function($){
      function tryPopup() {
        if (
          window.elementorProFrontend &&
          elementorProFrontend.modules &&
          elementorProFrontend.modules.popup &&
          typeof elementorProFrontend.modules.popup.showPopup === 'function'
        ) {
          elementorProFrontend.modules.popup.showPopup({ id: <?php echo $popup_id; ?> });
        } else {
          setTimeout(tryPopup, 300);
        }
      }

      var path = window.location.pathname.replace(/\/$/,'');
      if ( path === '/carrinho' ) {
        tryPopup();
      }

      // Apenas AJAX add_to_cart
      $(document.body).on('added_to_cart', function(){
        tryPopup();
      });

    })(jQuery);
    </script>
    <?php
}
// E.O.F

// Função para autocompletar Empresa quando CNPJ é preenchido
add_action( 'wp_enqueue_scripts', 'ca_enqueue_cnpj_autocomplete' );
function ca_enqueue_cnpj_autocomplete() {
    if ( ! is_checkout() ) {
        return;
    }

    wp_enqueue_script(
    	'ca-cnpj-autocomplete',
    	get_stylesheet_directory_uri() . '/js/cnpj-autocomplete.js',
    	array('jquery'),
    	filemtime( get_stylesheet_directory() . '/js/cnpj-autocomplete.js' ),
    	true
    );

    wp_localize_script( 'ca-cnpj-autocomplete', 'caCNPJSettings', array(
        'fieldCNPJ'    => 'input[name="billing_cnpj"]',
        'fieldEmpresa' => 'input[name="billing_company"]',
        'apiUrl'       => 'https://brasilapi.com.br/api/cnpj/v1/'
    ) );
}
// E.O.F

// Função para limpar debug.log diariamente
// Agenda o evento
if ( ! wp_next_scheduled( 'limpar_debug_log_diariamente' ) ) {
    wp_schedule_event( strtotime( '00:00:00' ), 'daily', 'limpar_debug_log_diariamente' );
}

// Define que será feito diariamente
add_action( 'limpar_debug_log_diariamente', 'limpar_debug_log_conteudo' );

// Apaga o conteúdo do arquivo debug.log
function limpar_debug_log_conteudo() {
    $log_path = WP_CONTENT_DIR . '/debug.log';
    
    if ( file_exists( $log_path ) ) {
        file_put_contents( $log_path, '' ); // Limpa o conteúdo mantendo o arquivo
    }
}
// E.O.F

// Adiciona GTAG na página do carrinho e onclick no botão Finalizar compra
add_action('wp_footer', 'gtag_checkout_click_event');

function gtag_checkout_click_event() {
    if (is_cart()) {
        ?>
        <script>
          console.log("🟢 Script de rastreamento do botão 'Finalizar compra' carregado.");

          function gtagSendEventCheckout(url) {
            console.log("📦 Evento de clique no botão 'Finalizar compra' detectado!");

            // Verifica se o gtag existe
            if (typeof gtag !== 'function') {
              console.warn("⚠️ gtag não está disponível. Redirecionando diretamente...");
              window.location = url;
              return;
            }

            // Envia o evento com callback
            gtag('event', 'ads_conversion_Fale_conosco_1', {
              'event_callback': function () {
                console.log("✅ Evento enviado, redirecionando para: " + url);
                window.location = url;
              },
              'event_timeout': 2000
            });

            // Fallback de redirecionamento após 2 segundos
            setTimeout(function () {
              console.warn("⏱️ Timeout atingido. Redirecionando para: " + url);
              window.location = url;
            }, 2000);
          }

          document.addEventListener('DOMContentLoaded', function () {
            var botaoFinalizar = document.querySelector('.checkout-button');

            if (botaoFinalizar) {
              var url = botaoFinalizar.getAttribute('href');
              botaoFinalizar.addEventListener('click', function (e) {
                e.preventDefault(); // Impede a navegação imediata
                gtagSendEventCheckout(url); // Envia o evento e redireciona depois
              });
            } else {
              console.warn("🚫 Botão 'Finalizar compra' não encontrado.");
            }
          });
        </script>
        <?php
    }
}
