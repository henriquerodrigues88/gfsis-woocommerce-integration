# GFSIS WooCommerce Integration
<p>Integração do WooCommerce com o sistema GFSIS para registro de pedidos</p>
<p>Referência da API: https://gfsis.readme.io/reference/getting-started-with-your-api</p>
<p>Este plugin pode ser usado com os plugins <a href="https://br.wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/">Brazilian Market on WooCommerce</a> e <a href="https://br.wordpress.org/plugins/woo-checkout-field-editor-pro/">Checkout Field Editor</a>.</p>
<p>Recentemente desenvolvi um e-commerce utilizando WordPress e WooCommerce para a venda de certificados digitais para uma empresa do ramo, que também emite os certificados. Me deparei com um problema de integração do checkout para um sistema externo, o GFSIS. Este plugin personalizado foi desenvolvido para essa finalidade.</p>
<h2>No plugin Brazilian Market on WooCommerce</h2>
<ul>
<li>Navegue até WooCommerce - Campos do Checkout
<li>Na opção "Exibir Tipo de Pessoa", selecione Pessoa Física e Pessoa Jurídica.</li>
<li>Marque a opção "Tipo de Pessoa é obrigatório apenas no Brasil."</li>
<li>Marque a opção "Exibir Data de Nascimento".</li>
<li>Em validação, marque as opções "Verificar se o CPF é válido" e "Verificar se o CNPJ é válido".</li>
<li>Opcional: Marque "Habilitar Sugestões de E-mail" e/ou "Habilitar Máscara de Campos" em Opções de jQuery.</li>
<li>Salve suas alterações.</li>
</ul>
<h2>No plugin Checkout Field Editor</h2>
<ul>
<li>Navegue até WooCommerce - Checkout Form.</li>
<li>Crie um campo de nome billing_id do tipo hidden e deixe o valor padrão vazio.</li>
<li>Crie um campo de nome codigoibge do tipo select e não adicione opções.</li>
<li>Crie um campo de nome contactname do tipo hidden e insira o nome do vendedor cadastrado no GFSIS como valor padrão.</li>
<li>Crie um campo de nome pontoatendimento do tipo hidden e deixe o valor padrão vazio.</li>
<li>Crie um campo de nome indicacaocpf do tipo select e adicione os respectivos cpfs nas opções e seus labels, cadastrados no GFSIS. Você pode criar um campo indicacaocnpj, só vai precisar adicionar os respectivos cnpjs nas opções e seus labels e fazer as alterações de nome no arquivo gfsis-woocommerce-integration.php.</li>
<li>Crie um campo de nome tipovalidacao do tipo select e adicione a opção 1 para Presencial e 2 para Videoconferência. Se a validação é feita apenas por videconferência, por exemplo, adicione apenas a opção 2.</li>
<li>Opcional: Se há apenas uma opção, você pode ocultar o campo com CSS se não quiser mostra-lo ao cliente.</li>
<li>Crie um campo de nome formapagamento do tipo select e adicione as opções e labels de acordo com o que foi cadastrado no GFSIS. Por exemplo: 21 - Pix, 22 - Boleto, 23 - Cartão 1x, 24 - Cartão 2x, etc. Neste caso você precisará criar uma função personalizada para mostrar a opção do gateway de pagamento de acordo com a escolha do cliente no select.</li>
<li>Opcional: Reordene os campos para a ordem que você quer que aparecem no formulário de checkout, basta arrasta-los para cima ou para baixo.</li>
<li>Salve suas alterações.</li>
</ul>
<h2>No WordPress</h2>
<ul>
<li>O objetivo é criar um arquivo debug.log na pasta /wp-content para conferir se os campos estao sendo enviados, recebendo a resposta da API.</li>
<li>Utilize algum programa de FTP, como WinSCP, Filezilla ou o gerenciador de arquivos da sua hospedagem para acessar os arquivos.</li>
<li>Abra o arquivo wp-config.php.</li>
<li>Altere 'WP_DEBUG' para true.</li>
<li>Logo abaixo, copie e cole o seguinte conteudo e salve o arquivo:<br>
define( 'WP_DEBUG_LOG', true );<br>
define( 'WP_DEBUG_DISPLAY', false );<br>
@ini_set( 'log_errors', 1 );<br>
@ini_set( 'display_errors', 0 );
</li>
</ul>
<h2>No WooCommerce</h2>
<ul>
<li>Navege até WooCommerce - Produtos.</li>
<li>Insira em cada um dos seus produtos, no campo SKU, o ID referente ao produto do certificado que está cadastrado no GFSIS.</li>
<li>Opcional: Insira categorias e atrele aos produtos. Por exemplo: Equipamento, Certificado PF, Certificado PJ, etc. Obs: Eu precisei fazer isso por uma questão de regra de negócio, visto que o cliente que utiliza cpf não pode comprar um certificado pj e vice versa. Neste caso é necessário criar uma função personalizada para fazer essa verificação através das categorias.</li>
</ul>
<h2>Instalando o plugin</h2>
<ul>
<li>Navegue até Plugins - Adicionar plugin - Enviar plugin.</li>
<li>Caso você já tenha baixado, selecione o arquivo gfsis-woocommerce-integration-main.zip.</li>
<li>Instale e ative o plugin.</li>
</ul>
<h2>No plugin GFSIS WooCommerce Integration</h2>
<ul>
<li>Utilize algum programa de FTP, como WinSCP, Filezilla ou o gerenciador de arquivos da sua hospedagem para acessar os arquivos.</li>
<li>Navegue até as pastas wp-content - plugins - gfsis-woocommerce-integration.</li>
<li>Abra o arquivo gfsis-woocommerce-integration.php.</li>
<li>Na linha 146, substitua "123" pelo ID do seu ponto de atendimento cadastrado no GFSIS.</li>
<li>Na linha 201, em "yourdomain.gfsis.com.br", substitua "yourdomain" pelo seu nome de domínio.</li>
<li>Nas linhas 202 e 203, insira seu nome de usuário e senha, providos pelo GFSIS.</li>
<li>Na linha 251, em "yourdomain.gfsis.com.br", substitua "yourdomain" pelo seu nome de domínio.</li>
<li>Opcional: Na linha 259, ajuste o tempo limite para a requisição em segundos.</li>
<li>Salve suas alterações.</li>
</ul>
<h2>Extra</h2>
<ul>
<li>Comunicação com a API do Instituto Brasileiro de Geografia e Estatística - IBGE.</li>
<li>Quando uma UF é selecionada no campo estado, o campo codigoibge é populado com as opções para a escolha/pesquisa do cliente.</li>
</ul>
<h2>Funcionalidades</h2>
<ul>
<li>Mapeia os nomes dos campos do checkout para corresponderem aos nomes dos campos do GFSIS.</li>
<li>Salva campos personalizados no pedido do WooCommerce.</li>
<li>Limpa caracteres não numéricos nos campos cpf, cnpj e cep.</li>
<li>Converte data nascimento de d-m-y para Y-m-d.</li>
<li>Captura valores brutos para depuração e cria logs.</li>
<li>Cria o token, verifica data e hora de expiração e renova se expirado, armazenando em um transient ou utiliza o token armazenado em cache caso esteja válido.</li>
<li>Prepara e envia os dados dos objetos como JSON.</li>
<li>Obtém os detalhes do pedido.</li>
<li>Cria um código temporario para o GFSIS.</li>
<li>Captura o código retornado pela GFSIS atualizando o ID do pedido no WooCommerce para refletir o ID correto do pedido no GFSIS.</li>
<li>Obtém o SKU do produto principal do pedido passando como ID do produto no objeto certificado para o GFSIS.</li>
<li>Personaliza o número do pedido exibido na página de Pedido Recebido para o ID do pedido no GFSIS.</li>
</ul>
<h2>Requerimentos</h2>
<ul>
<li>Versão mínima do PHP: 7.4. Utilize a partir de 8.0 se nenhum dos seus plugins for obsoleto.</li>
<li>WordPress: 6.2 ou superior.</li>
<li>WooCommerce: 9.0 ou superior.</li>
</ul>
<h2>Feito com</h2>
<ul>
<li>PHP</li>
<li>Javascript</li>
</ul>
