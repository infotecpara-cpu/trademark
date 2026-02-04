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
   Html::redirect($url, 302);
   die;
}

$name = 'internal';
$css = "";

// Busca configurações via Namespace
$picture = TrademarkConfig::getConfig("{$name}_picture", '');
$width   = TrademarkConfig::getConfig("{$name}_picture_width", '100px');
$height  = TrademarkConfig::getConfig("{$name}_picture_height", '55px');

if ($picture) {
   $url = TrademarkToolbox::getPictureUrl($picture);

   /* Seletores atualizados para o layout Tabler do GLPI 11 */
   $css .= "
   .navbar-brand-autodark,
   .glpi-logo {
      background-image: url('$url') !important;
      background-size: contain !important;
      background-repeat: no-repeat !important;
      background-position: center !important;
      width: $width !important;
      height: $height !important;
      display: inline-block;
   }
   /* Oculta o SVG original do GLPI 11 que fica dentro da div */
   .navbar-brand-autodark svg,
   .glpi-logo svg {
      display: none !important;
   }
   ";
}

$css_type   = TrademarkConfig::getConfig("{$name}_css_type", 'css');
$css_custom = TrademarkConfig::getConfig("{$name}_css_custom", '');
$css_custom = html_entity_decode($css_custom);

// Nota: Removido suporte a SCSS para simplificar,
// a menos que você tenha a biblioteca instalada no seu src/
if ($css_custom) {
   $css .= $css_custom;
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
