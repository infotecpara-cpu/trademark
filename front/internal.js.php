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

header('Content-Type: application/javascript');

$is_cacheable = !isset($_GET['debug']) && !isset($_GET['nocache']);
if ($is_cacheable) {
   $max_age = WEEK_TIMESTAMP;
   header_remove('Pragma');
   header('Cache-Control: public');
   header('Cache-Control: max-age=' . $max_age);
   header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + $max_age));
}

?>

$(function () {
    // Garante que o CSS do plugin seja carregado por último para sobrescrever o padrão
    $('link[href*="trademark/front/internal.css.php"]').appendTo($('head'));

    <?php
    $favicon = TrademarkConfig::getConfig('favicon_picture');
    if ($favicon) :
        $faviconUrl = TrademarkToolbox::getPictureUrl($favicon);
    ?>
        // Atualiza o Favicon no GLPI 11
        var $icon = $('link[rel*=icon]');
        $icon.attr('type', null);
        $icon.attr('href', <?php echo json_encode($faviconUrl) ?>);
    <?php endif; ?>

    <?php
    $pageTitle = TrademarkConfig::getConfig('page_title');
    if ($pageTitle) :
    ?>
        // Atualiza o título substituindo a marca padrão
        var oldTitle = document.title;
        document.title = oldTitle.replace('GLPI', <?php echo json_encode($pageTitle) ?>);
    <?php endif; ?>

    <?php
    $footerDisplay = TrademarkConfig::getConfig('page_footer_display', 'original');
    $footerText = TrademarkConfig::getConfig('page_footer_text', '');

    if ($footerDisplay === 'hide') :
    ?>
        // No GLPI 11, o "Sobre" geralmente fica no menu de usuário ou rodapé
        $('.footer .copyright, a[data-bs-target="#about_modal"]').hide();
    <?php endif; ?>

    <?php
    if ($footerDisplay === 'custom' && !empty($footerText)) :
        $footerTextHtml = \Glpi\RichText\RichText::getEnhancedHtml($footerText);
    ?>
        // Aplica o rodapé customizado nos containers do Tabler
        $('.footer .container-xl,
