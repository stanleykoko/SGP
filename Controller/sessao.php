<?php

class Sessao {

    private static $perfis = array();
    private static $nivelCarregado = false;

    public static function iniciar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

      public static function iniciarSessao($utilizador) {
        self::iniciar();
        session_regenerate_id(true);
        $_SESSION['codigo']          = $utilizador->getCodigo();
        $_SESSION['nome']            = $utilizador->getNome();
        $_SESSION['apelido']         = $utilizador->getApelido();
        $_SESSION['username']        = $utilizador->getUserName();
        $_SESSION['nivelAcesso']     = $utilizador->getNivelAcesso();
        $_SESSION['ultimaAtividade'] = time();
        self::$nivelCarregado = true;
    }

    public static function terminarSessao() {
        self::iniciar();
        $_SESSION = array();
        if (ini_get('session.use_cookies')) {
            $cookie = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
        }
        session_destroy();
    }

    public static function estaLogado() {
        self::iniciar();
        if (!isset($_SESSION['codigo'])) {
            return false;
        }
        if (isset($_SESSION['ultimaAtividade']) && time() - $_SESSION['ultimaAtividade'] > 1800) {
            self::terminarSessao();
            return false;
        }
        $_SESSION['ultimaAtividade'] = time();
        return true;
    }

      public static function getUsername() {
        self::iniciar();
        return isset($_SESSION['username']) ? $_SESSION['username'] : null;
    }

    public static function getNomeCompleto() {
        self::iniciar();
        $nome = isset($_SESSION['nome']) ? $_SESSION['nome'] : "";
        $apelido = isset($_SESSION['apelido']) ? $_SESSION['apelido'] : "";
        return trim($nome . " " . $apelido);
    }

    public static function getNivelAcesso() {
        self::iniciar();
        if (!self::$nivelCarregado && isset($_SESSION['codigo'])) {
            self::$nivelCarregado = true;
            $conexao = mysqli_connect("localhost", "root", "", "sgpessoa");
            $stmt = mysqli_prepare($conexao, "select nivelAcesso from cadastro where CodigoUtilizador = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $_SESSION['codigo']);
                mysqli_stmt_execute($stmt);
                $resultado = mysqli_stmt_get_result($stmt);
                $utilizador = $resultado ? mysqli_fetch_assoc($resultado) : null;
                if ($utilizador) {
                    $_SESSION['nivelAcesso'] = $utilizador['nivelAcesso'];
                }
            }
        }
        return isset($_SESSION['nivelAcesso']) ? $_SESSION['nivelAcesso'] : null;
    }

     
    public static function podeFazer($acao) {
        $nivel = self::getNivelAcesso();
        // acao => coluna da tabela "perfil"
        $colunas = array(
            'ver' => 'ver',
            'criar' => 'criar',
            'editar' => 'editar',
            'remover' => 'remover',
            'utilizadores' => 'utilizadores',
            'cadastro' => 'utilizadores',
            'logs' => 'logs',
            'perfis' => 'perfis'
        );
        if (!isset($colunas[$acao]) || $nivel === null) {
            return false;
        }

        if (!array_key_exists($nivel, self::$perfis)) {
            $conexaoPerfil = mysqli_connect("localhost", "root", "", "sgpessoa");
            $stmt = mysqli_prepare($conexaoPerfil, "select * from perfil where nomePerfil = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $nivel);
                mysqli_stmt_execute($stmt);
                $resultado = mysqli_stmt_get_result($stmt);
                self::$perfis[$nivel] = $resultado ? mysqli_fetch_assoc($resultado) : false;
            } else {
                self::$perfis[$nivel] = false;
            }
        }

        if (self::$perfis[$nivel]) {
            return (bool) self::$perfis[$nivel][$colunas[$acao]];
        }

        $permissoesAntigas = array(
            'ADMINISTRADOR' => array('ver', 'criar', 'editar', 'remover', 'utilizadores', 'cadastro', 'logs', 'perfis'),
            'SUPEROPERADOR' => array('ver', 'criar', 'editar', 'remover'),
            'AUDITOR' => array('ver', 'logs'),
            'OPERADOR' => array('ver', 'criar')
        );
        return isset($permissoesAntigas[$nivel]) && in_array($acao, $permissoesAntigas[$nivel], true);
    }

 
    // Bloqueia a pagina: se nao estiver logado vai para o Login,
    // se nao tiver permissao vai para o Inicio
    public static function exigirPermissao($acao) {
        self::exigirLogin();
        if (!self::podeFazer($acao)) {
            // Não redireccionar para TelaPrincipal.php: essa página também
            // exige permissões e poderia criar um ciclo infinito.
            http_response_code(403);
            header('Content-Type: text/html; charset=UTF-8');
            echo '<!DOCTYPE html><html lang="pt"><head><meta charset="UTF-8"><title>Acesso negado</title></head>';
            echo '<body><h1>Acesso negado</h1><p>O seu perfil não tem permissão para executar esta operação.</p>';
            echo '<p><a href="TelaPrincipal.php">Voltar ao início</a> | <a href="Logout.php">Terminar sessão</a></p></body></html>';
            exit;
        }
    }

    // Escreve um link do menu. Sem permissao fica desativado (cinzento)
    public static function linkMenu($href, $texto, $acao, $ativo = false) {
        $classe = $ativo ? "on" : "";
        if (self::podeFazer($acao)) {
            echo '<a href="' . $href . '" class="' . $classe . '"><span class="icone"></span>' . $texto . '</a>';
        } else {
            echo '<a class="desativado" title="Sem permissao"><span class="icone"></span>' . $texto . '</a>';
        }
    }

    public static function exigirLogin() {
        if (!self::estaLogado()) {
            header("Location: Login.php");
            exit;
        }
    }
}

function verificarLogin() {
    Sessao::exigirLogin();
}
?>