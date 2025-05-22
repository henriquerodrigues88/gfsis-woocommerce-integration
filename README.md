# GFSIS Woocommerce Integration<br>
<p>Integração do WooCommerce com o sistema GFSIS para registro de pedidos</p>
<p>API Reference: https://gfsis.readme.io/reference/getting-started-with-your-api</p>
<p>Este plugin pode ser usado com os plugins Checkout Field Editor e Brazilian Market on WooCommerce.</p>
<h2>No plugin Checkout Field Editor</h2>
<ul>
<li>Crie um campo de nome billing_id do tipo hidden e deixe o default value vazio</li>
<li>Crie um campo de nome codigoibge do tipo select. Não adicione opções</li>
<li>Crie um campo de nome contactname do tipo e adicione o nome do vendedor cadastrado no GFSIS como default value</li>
<li>Crie um campo de nome pontoatendimento do tipo hidden e deixe o default value vazio</li>
<li>Crie um campo de nome indicacaocpf do tipo select e adicione o respectivos cpf nas options e seus labels cadastrados no GFSIS. Você pode usar cnpj, só vai precisar fazer as devidas alterações no arquivo gfsis-woocommerce-integration.php</li>
<li>Crie um campo de nome tipovalidacao do tipo select e adicione a option 1 para Presencial e 2 para Videoconferência. Se sua empresa utiliza apenas videconferência, por exemplo, você pode adicionar apenas a opção 2 e opcionalmente ocultar o campo com CSS</li>
<li>Crie um campo de nome formapagamento do tipo select e adicione as options e labels de acordo com o que foi cadastrado no financeiro do GFSIS. Por exemplo: 21 - Pix, 22 - Boleto, 23 - Cartão 1x, 24 - Cartão 2x, etc.</li>
<li>Opcional: Reordene os campos para como você quer que aparecem no checkout</li>
</ul>
<h2>No plugin Brazilian Market on WooCommerce</h2>
<ul>
<li>Na opção "Exibir Tipo de Pessoa", selecione Pessoa Física e Pessoa Jurídica</li>
<li>Marque a opção "Tipo de Pessoa é obrigatório apenas no Brasil?"</li>
<li>Marque a opção "Exibir Data de Nascimento"</li>
<li>Em validação, marque as opções "Verificar se o CPF é válido" e "Verificar se o CNPJ é válido."</li>
<li>Opcionalmente, você também pode marcar as opções "Habilitar Sugestões de E-mail" e/ou "Habilitar Máscara de Campos" em Opções de jQuery.</li>
</ul>
<h2>No WordPress</h2>
<ul>
<li>O objetivo é criar um arquivo debug.log na pasta /wp-content para conferir se os campos estao sendo enviados</li>
<li>Abra o arquivo wp-config.php</li>
<li>Altere 'WP_DEBUG' para true</li>
<li>Logo abaixo, cole o seguinte conteudo e salve o arquivo:<br>define( 'WP_DEBUG_LOG', true );<br>
define( 'WP_DEBUG_DISPLAY', false );<br>
@ini_set( 'log_errors', 1 );<br>
@ini_set( 'display_errors', 0 );
</li>
<li>No painel de administração do WordPress, navegue até plugins e clique em enviar</li>
<li>Caso você já tenha baixado, selecione o arquivo gfsis-woocommerce-integration.zip</li>
<li>Instale e ative o plugin</li>
<h2>No Woocommerce</h2>
<li>Navege até Produtos</li>
<li>Certifique-se de que todos os seus produtos contenham no campo SKU o ID referente ao produto que está cadastrado no GFSIS</li>
<li>Opcional: Insira categorias e atrele aos produtos. Por exemplo: Equipamento, Certificado PF, Certificado PJ, etc</li>
</ul>
<h2>No plugin GFSIS Woocommerce Integration</h2>
<ul>
<li>Abra o arquivo gfsis-woocommerce-integration.php</li>
<li>Na linha 146, substitua "123" pelo ID correto do seu ponto de atendimento cadastrado no GFSIS</li>
<li>Na linha 201, em "yourdomain.gfsis.com.br", substitua "yourdomain" pelo seu domínio</li>
<li>Nas linhas 202 e 203, insira seu nome de usuário e senha, providos pelo GFSIS</li>
<li>Na linha 251, em "yourdomain.gfsis.com.br", substitua "yourdomain" pelo seu domínio</li>
</ul>
<h2>Extras</h2>
<ul>
<li>Comunicação com a API do IBGE</li>
<li>Quando um estado é selecionado, o campo codigoibge é populado com as opções para a escolha do cliente</li>
</ul>
<h2>Funcionalidades</h2>
<ul>
<li>Salva campos personalizados no pedido</li>
<li>Mapeia os campos do Woocommerce para corresponderem aos campos do GFSIS</li>
<li>Limpa caracteres não numéricos</li>
<li>Obtém os detalhes do pedido</li>
<li>Converte data de d-m-y para Y-m-d</li>
<li>Captura valores brutos para depuração</li>
<li>Prepara e envia os dados dos objetos como JSON</li>
<li>Cria o token, verifica sua expiração e renova automaticamente se necessário armazenando em um transient</li>
<li>Captura o código retornado pela GFSIS atualizando o ID do pedido no WooCommerce para refletir o código do GFSIS</li>
<li>Obtém o SKU do produto principal do pedido passando como ID do certificado para o GFSIS</li>
<li>Personaliza o número do pedido exibido na página de Pedido Recebido</li>
</ul>
<h2>Requerimentos</h2>
<ul>
<li>Versão mínima do PHP: 7.4</li>
<li>WordPress a partir da versão 6.2</li>
<li>Woocommerce a partir da versão 9.0</li>
</ul>
