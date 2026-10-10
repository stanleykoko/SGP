
<?php
include_once(__DIR__ . "/../Model/CadastroUser.php");
include_once(__DIR__ . "/controllerLog.php");

class Sessao
{
    private $logado = false;
    public $codigo;
    public $email;
    public $nome;
    public $apelido;
    public $username;
    public $nivelAcesso;

    function __construct()
    {
        session_start();
        $this->verificar_login();
    }

    private function verificar_login()
    {
        if (isset($_SESSION['codigo']) && isset($_SESSION['email'])) {
            $this->codigo = $_SESSION['codigo'];
            $this->email = $_SESSION['email'];
            $this->nome = $_SESSION['nome'];
            $this->apelido = $_SESSION['apelido'];
            $this->username = $_SESSION['username'];
            $this->nivelAcesso = $_SESSION['nivelAcesso'];
            $this->logado = true;
        } else {
            unset($this->codigo);
            unset($this->email);
            unset($this->nome);
            unset($this->apelido);
            unset($this->username);
            unset($this->nivelAcesso);
            $this->logado = false;
        }
    }

    public function esta_logado()
    {
        return $this->logado;
    }

    public function login($utilizador)
    {
        if ($utilizador) {
            session_regenerate_id(true);
            $_SESSION['codigo'] = $utilizador['CodigoUtilizador'];
            $this->codigo = $_SESSION['codigo'];
            $_SESSION['email'] = $utilizador['email'];
            $this->email = $_SESSION['email'];
            $_SESSION['nome'] = $utilizador['nome'];
            $this->nome = $_SESSION['nome'];
            $_SESSION['apelido'] = $utilizador['apelido'];
            $this->apelido = $_SESSION['apelido'];
            $_SESSION['username'] = $utilizador['username'];
            $this->username = $_SESSION['username'];
            $_SESSION['nivelAcesso'] = $utilizador['nivelAcesso'];
            $this->nivelAcesso = $_SESSION['nivelAcesso'];
            $this->logado = true;
        }
    }

    public function logout()
    {
        unset($_SESSION['codigo']);
        unset($this->codigo);
        unset($_SESSION['email']);
        unset($this->email);
        unset($_SESSION['nome']);
        unset($this->nome);
        unset($_SESSION['apelido']);
        unset($this->apelido);
        unset($_SESSION['username']);
        unset($this->username);
        unset($_SESSION['nivelAcesso']);
        unset($this->nivelAcesso);
        $this->logado = false;
    }
}

$sessao = new Sessao();

$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

if (!$conexao) {
    die("Erro na ligação à base de dados: " . mysqli_connect_error());
}

function login($identificador, $senha)
{
    global $conexao, $sessao;

    $sql = "SELECT * FROM cadastro WHERE username='$identificador' OR email='$identificador'";
    $result = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($result) > 0) {
        $utilizador = mysqli_fetch_assoc($result);

        if (password_verify($senha, $utilizador['senha'])) {
            $sessao->login($utilizador);
            registarAccao("Log in");
            header("Location: ../View/TelaPrincipal.php");
            exit();
        } else {
            echo "Senha incorrecta";
        }
    } else {
        echo "Username ou email não encontrado";
    }
}

function verificarLogin()
{
    global $sessao;

    if (!$sessao->esta_logado()) {
        header("Location: ../View/Login.php");
        exit();
    }
}
?>
