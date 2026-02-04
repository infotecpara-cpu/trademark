<?php

use GlpiPlugin\Trademark\Config as TrademarkConfig;
use GlpiPlugin\Trademark\Toolbox as TrademarkToolbox;

$_GET["donotcheckversion"]   = true;
$dont_check_maintenance_mode = true;

include('../../../inc/includes.php');

// Redirecionamento para controle de cache
if (!isset($_GET['_'])) {
   $timestamp = TrademarkToolbox::getTimestamp();

   header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
   header("Cache-Control: post-check=0, pre-check=0", false);
   header("Pragma: no-cache");

   $file = basename(__FILE__);
   $url = "$file?_=$timestamp";
   if (isset($_GET['v'])) {
      $url .= '&v=' . $_GET['v'];
   }
   if (isset($_GET['theme'])) {
      $url .= '&theme=' . $_GET['theme'];
   }
   Html::redirect($url, 302);
   die;
}

// Lógica de CSS Customizado
$css = "";

// Fundo da Tela de Login
$background = TrademarkConfig::getConfig('login_background_picture');
if ($background) {
    $bgUrl = TrademarkToolbox::getPictureUrl($background);
    $css .= "
    body.page-anonymous {
        background-image: url('$bgUrl') !important;
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
    }
    /* Ajuste de opacidade do card para destacar o fundo se desejado */
    .page-anonymous .card-md {
        background-color: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
    }
    ";
}

// CSS Customizado (Antigo logic de SCSS simplificada para CSS)
$customCss = TrademarkConfig::getConfig('login_css_custom', '');
if (!empty($customCss)) {
    $css .= html_entity_decode($customCss);
}

header('Content-Type: text/css');

$is_cacheable = !isset($_GET['debug']) && !isset($_GET['nocache']);
if ($is_cacheable) {
   $max_age = WEEK_TIMESTAMP;
   header_remove('Pragma');
   header('Cache-Control: public');
   header('Cache-Control: max-age=' . $max_age);
   header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + $max_age));
}

echo $css;
