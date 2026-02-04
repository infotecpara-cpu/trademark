<?php
include ("../../../inc/includes.php");

// Verificação de segurança CSRF (Obrigatório no GLPI 11)
Session::checkRight("config", UPDATE);

if (isset($_POST["update"])) {
   GlpiPlugin\Trademark\Config::configUpdate($_POST);
   Html::back();
}

// Se o arquivo for chamado via aba, o redirecionamento é automático,
// mas se for acesso direto, você deve chamar o formulário:
Html::header(Config::getTypeName(1), $_SERVER['PHP_SELF'], "config", "trademark");
// ... lógica de exibição se necessário
Html::footer();
