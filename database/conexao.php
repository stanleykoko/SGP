<?php
/**
 * Configuração da ligação MySQL do SGP.
 * Ambiente XAMPP local predefinido: utilizador root sem palavra-passe.
 * Se alterares as credenciais, actualiza também os controladores que usam mysqli_connect.
 */
$servidor = 'localhost';
$utilizadorBD = 'root';
$senhaBD = '';
$nomeBD = 'sgpessoa';

function ligarBD() {
    global $servidor, $utilizadorBD, $senhaBD, $nomeBD;
    $ligacao = mysqli_connect($servidor, $utilizadorBD, $senhaBD, $nomeBD);
    if (!$ligacao) {
        error_log('Falha na ligação à base de dados SGP: ' . mysqli_connect_error());
        http_response_code(500);
        exit('Não foi possível ligar à base de dados sgpessoa. Confirme se o MySQL está activo e se a base de dados foi importada.');
    }
    mysqli_set_charset($ligacao, 'utf8mb4');
    return $ligacao;
}
?>
