<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ações de Alunos</title>
    <!-- Incluindo Bootstrap via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">

<?php
// Classe Responsavel encapsula os dados de um responsável
class Responsavel {
    private $nomeCompleto;
    private $rg;
    private $cpf;
    private $telefone;

    public function __construct($nomeCompleto, $rg, $cpf, $telefone) {
        $this->nomeCompleto = $nomeCompleto;
        $this->rg = $rg;
        $this->cpf = $cpf;
        $this->telefone = $telefone;
    }

    public function getNomeCompleto() {
        return $this->nomeCompleto;
    }

    public function getRg() {
        return $this->rg;
    }

    public function getCpf() {
        return $this->cpf;
    }

    public function getTelefone() {
        return $this->telefone;
    }
}

// Classe Aluno encapsula os dados de um aluno e seus responsáveis
class Aluno {
    private $nomeCompleto;
    private $rg;
    private $cpf;
    private $matricula;
    private $serie;
    private $atividadesExtracurriculares;
    private $responsaveis = [];

    public function __construct($nomeCompleto, $rg, $cpf, $matricula, $serie, $atividadesExtracurriculares, $responsaveis = []) {
        $this->nomeCompleto = $nomeCompleto;
        $this->rg = $rg;
        $this->cpf = $cpf;
        $this->matricula = $matricula;
        $this->serie = $serie;
        $this->atividadesExtracurriculares = $atividadesExtracurriculares;
        $this->responsaveis = $responsaveis;  // Array de responsáveis
    }

    public function getNomeCompleto() {
        return $this->nomeCompleto;
    }

    public function getSerie() {
        return $this->serie;
    }

    public function setSerie($novaSerie) {
        $this->serie = $novaSerie;
    }

    public function getAtividadesExtracurriculares() {
        return $this->atividadesExtracurriculares ? "Sim" : "Não";
    }

    public function listarResponsaveis() {
        $output = '<div class="row" style="margin-top:25px;">';
        foreach ($this->responsaveis as $responsavel) {
            $output .= '<div class="col-md-6">';  // Coluna para cada responsável
            $output .= "<h6>Responsável: " . $responsavel->getNomeCompleto() . "</h6>";
            $output .= "<p>RG: " . $responsavel->getRg() . "<br>";
            $output .= "CPF: " . $responsavel->getCpf() . "<br>";
            $output .= "Telefone: " . $responsavel->getTelefone() . "</p>";
            $output .= '</div>';  // Fechando a coluna
        }
        $output .= '</div>';  // Fechando a linha
        return $output;
    }
}

// Classe abstrata que define a ação genérica com método abstrato
abstract class AcaoAluno {
    protected $aluno;

    public function __construct($aluno) {
        $this->aluno = $aluno;
    }

    abstract public function executarAcao();
}

// Classe para Matricula
class Matricula extends AcaoAluno {
    public function executarAcao() {
        return "
            <div class='container'>
                <div class='row'>
                    <div class='col'>
                        <strong>Aluno:</strong> " . $this->aluno->getNomeCompleto() . "<br>
                        <strong>Série:</strong> " . $this->aluno->getSerie() . "<br>
                        <strong>Atividades Extracurriculares:</strong> " . $this->aluno->getAtividadesExtracurriculares() . "
                    </div>
                </div>
                <div class='row mt-2'>
                    <div class='col'>
                        " . $this->aluno->listarResponsaveis() . "
                    </div>
                </div>
            </div>";
    }
}


// Classe para Rematricula (caso de reprovação)
class Rematricula extends AcaoAluno {
    private $novaSerie;

    public function __construct($aluno, $novaSerie) {
        parent::__construct($aluno);
        $this->novaSerie = $novaSerie;
    }

    public function executarAcao() {
        $this->aluno->setSerie($this->novaSerie);
        return "Aluno " . $this->aluno->getNomeCompleto() . " rematriculado na nova série: " . $this->aluno->getSerie() .
               ". Participa de atividades extracurriculares: " . $this->aluno->getAtividadesExtracurriculares() . "<br>" .
               $this->aluno->listarResponsaveis();
    }
}

// Classe para Formando
class Formando extends AcaoAluno {
    private $requisitosAtendidos;

    public function __construct($aluno, $requisitosAtendidos) {
        parent::__construct($aluno);
        $this->requisitosAtendidos = $requisitosAtendidos;
    }

    public function executarAcao() {
        if ($this->requisitosAtendidos) {
            return "Aluno " . $this->aluno->getNomeCompleto() . " concluiu a série " . $this->aluno->getSerie() .
                   " e está formado. Participa de atividades extracurriculares: " . $this->aluno->getAtividadesExtracurriculares() . "<br>" .
                   $this->aluno->listarResponsaveis();
        } else {
            return "Aluno " . $this->aluno->getNomeCompleto() . " não atendeu aos requisitos para se formar.";
        }
    }
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeCompleto = htmlspecialchars($_POST['nome_completo']);
    $rg = htmlspecialchars($_POST['rg']);
    $cpf = htmlspecialchars($_POST['cpf']);
    $matricula = htmlspecialchars($_POST['matricula']);
    $serie = htmlspecialchars($_POST['serie']);
    $atividadesExtracurriculares = isset($_POST['atividades_extracurriculares']) ? 'Sim' : 'Não';
    $requisitosAtendidos = isset($_POST['requisitos']) ? true : false;

    // Dados dos responsáveis
    $responsaveis = [];
    $responsaveis[] = new Responsavel(
        htmlspecialchars($_POST['nome_responsavel1']),
        htmlspecialchars($_POST['rg_responsavel1']),
        htmlspecialchars($_POST['cpf_responsavel1']),
        htmlspecialchars($_POST['telefone_responsavel1'])
    );

    if (!empty($_POST['nome_responsavel2'])) {
        $responsaveis[] = new Responsavel(
            htmlspecialchars($_POST['nome_responsavel2']),
            htmlspecialchars($_POST['rg_responsavel2']),
            htmlspecialchars($_POST['cpf_responsavel2']),
            htmlspecialchars($_POST['telefone_responsavel2'])
        );
    }

    // Cria um novo aluno com base nos dados enviados
    $aluno = new Aluno($nomeCompleto, $rg, $cpf, $matricula, $serie, $atividadesExtracurriculares, $responsaveis);

    // Variáveis de ação baseadas na seleção
    $acoes = [];
    $acaoEscolhida = htmlspecialchars($_POST['acao']);

    // Dependendo da ação selecionada, cria o objeto correspondente
    switch ($acaoEscolhida) {
        case 'matricula':
            $acoes[] = new Matricula($aluno);
            break;
        case 'rematricula':
            $acoes[] = new Rematricula($aluno, "Série Avançada");
            break;
        case 'formando':
            $acoes[] = new Formando($aluno, $requisitosAtendidos);
            break;
    }

    // Processa as ações e exibe os resultados
    if (!empty($acoes)) {
        echo "<div class='row mt-4'>"; // Iniciando a linha do grid
        foreach ($acoes as $acao) {
            echo "<div class='col-md-6'>"; // Cada ação será colocada dentro de uma coluna
            echo "<div class='list-group-item'>" . $acao->executarAcao() . "</div>"; // Conteúdo de cada ação
            echo "</div>"; // Fechando a coluna
        }
        echo "</div>"; // Fechando a linha
    }
    
}
?>

<!-- Formulário para inserção de dados -->
<h4 class="text-center py-4 mt-5 mb-5">Dados do Aluno</h4>
<form method="post" class="mt-4" style="margin-bottom:50px;">
    <!-- Dados do aluno -->
    <div class="mb-3">
        <label for="nome_completo" class="form-label">Nome Completo</label>
        <input type="text" class="form-control" name="nome_completo" required>
    </div>

    <div class="mb-3">
        <label for="rg" class="form-label">RG</label>
        <input type="text" class="form-control" name="rg" required>
    </div>

    <div class="mb-3">
        <label for="cpf" class="form-label">CPF</label>
        <input type="text" class="form-control" name="cpf" required>
    </div>

    <div class="mb-3">
        <label for="matricula" class="form-label">Matrícula</label>
        <input type="text" class="form-control" name="matricula" required>
    </div>

    <div class="mb-3">
        <label for="serie" class="form-label">Série</label>
        <input type="text" class="form-control" name="serie" required>
    </div>

    <!-- Dados dos responsáveis -->
    <h4 class="text-center py-4 mt-5 mb-5">Dados dos Responsáveis</h4>
    <h6 class="text-center">Responsável 1</h6>
    <div class="mb-3">
        <label for="nome_responsavel1" class="form-label">Nome do Responsável 1</label>
        <input type="text" class="form-control" name="nome_responsavel1" required>
    </div>
    <div class="mb-3">
        <label for="rg_responsavel1" class="form-label">RG do Responsável 1</label>
        <input type="text" class="form-control" name="rg_responsavel1" required>
    </div>
    <div class="mb-3">
        <label for="cpf_responsavel1" class="form-label">CPF do Responsável 1</label>
        <input type="text" class="form-control" name="cpf_responsavel1" required>
    </div>
    <div class="mb-3">
        <label for="telefone_responsavel1" class="form-label">Telefone do Responsável 1</label>
        <input type="text" class="form-control" name="telefone_responsavel1" required>
    </div>

    <h6 class="text-center">Responsável 2 (opcional)</h6>
    <div class="mb-3">
        <label for="nome_responsavel2" class="form-label">Nome do Responsável 2</label>
        <input type="text" class="form-control" name="nome_responsavel2">
    </div>
    <div class="mb-3">
        <label for="rg_responsavel2" class="form-label">RG do Responsável 2</label>
        <input type="text" class="form-control" name="rg_responsavel2">
    </div>
    <div class="mb-3">
        <label for="cpf_responsavel2" class="form-label">CPF do Responsável 2</label>
        <input type="text" class="form-control" name="cpf_responsavel2">
    </div>
    <div class="mb-3">
        <label for="telefone_responsavel2" class="form-label">Telefone do Responsável 2</label>
        <input type="text" class="form-control" name="telefone_responsavel2">
    </div>

    <!-- Ação e Atividades Extracurriculares -->
    <div class="mb-3">
        <label for="acao" class="form-label">Ação</label>
        <select class="form-select" name="acao" required>
            <option value="">Selecione uma Ação</option>
            <option value="matricula">Matrícula</option>
            <option value="rematricula">Rematricula</option>
            <option value="formando">Formando</option>
        </select>
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" name="atividades_extracurriculares">
        <label for="atividades_extracurriculares" class="form-check-label">Atividades Extracurriculares</label>
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" name="requisitos" value="1">
        <label for="requisitos" class="form-check-label">Requisitos Atendidos</label>
    </div>

    <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-primary">Enviar</button>
    </div>
</form>

</div>

<!-- Incluindo JS do Bootstrap via CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
