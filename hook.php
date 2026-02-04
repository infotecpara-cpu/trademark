<?php

use GlpiPlugin\Trademark\Config as TrademarkConfig;

function plugin_trademark_display_login() {
   // No GLPI 11, usamos o Namespace para buscar as configurações
   $loginPicture = TrademarkConfig::getConfig('login_picture');
   $maxWidth     = TrademarkConfig::getConfig('login_picture_max_width', '240');
   $maxHeight    = TrademarkConfig::getConfig('login_picture_max_height', '130');
   $pageTitle    = TrademarkConfig::getConfig('page_title');
   $favicon      = TrademarkConfig::getConfig('favicon_picture');

   // Carregamento do CSS customizado do plugin
   // O GLPI 11 já carrega o CSS via hook de forma mais eficiente,
   // mas mantemos o link dinâmico caso você use processamento PHP no CSS.
   $timestamp = time(); // Idealmente buscar do banco como você fazia
   echo Html::css("/plugins/trademark/front/internal.css.php?_=$timestamp");

   ?>
   <?php if ($loginPicture) : ?>
      <?php
         // Certifique-se que a classe Toolbox foi migrada para Namespace ou use \
         $pictureUrl = \PluginTrademarkToolbox::getPictureUrl($loginPicture);
      ?>
      <style>
         /* Ajuste para o GLPI 11 (Layout Tabler) */
         .page-anonymous .navbar-brand-autodark,
         .page-anonymous .glpi-logo {
            background-image: url("<?php echo $pictureUrl ?>") !important;
            background-size: contain !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            width: 100% !important;
            height: <?php echo $maxHeight ?>px !important;
            max-width: <?php echo $maxWidth ?>px !important;
            content: "" !important; /* Remove o SVG original */
         }
         /* Esconde o texto "GLPI" se ele aparecer ao lado da logo */
         .page-anonymous .glpi-logo + span { display: none; }
      </style>
   <?php endif; ?>

   <script type="text/javascript">
   $(function() {
      // Ajuste de Placeholders para os inputs do Tabler
      $('#login_name').attr('placeholder', <?php echo json_encode(__('Login')) ?>);
      $('#login_password').attr('placeholder', <?php echo json_encode(__('Password')) ?>);

      <?php if ($favicon) : ?>
         var faviconUrl = <?php echo json_encode(\PluginTrademarkToolbox::getPictureUrl($favicon)) ?>;
         $('link[rel*="icon"]').attr('href', faviconUrl);
      <?php endif; ?>

      <?php if ($pageTitle) : ?>
         // No GLPI 11 o título costuma vir formatado, fazemos o replace
         document.title = document.title.replace('GLPI', <?php echo json_encode($pageTitle) ?>);
      <?php endif; ?>

      <?php
      $footerDisplay = TrademarkConfig::getConfig('page_footer_display', 'original');
      $footerText    = TrademarkConfig::getConfig('page_footer_text', '');

      if ($footerDisplay === 'hide') : ?>
         $('.footer').hide();
      <?php endif; ?>

      <?php if ($footerDisplay === 'custom' && !empty($footerText)) :
         $cleanFooter = \Glpi\RichText\RichText::getEnhancedHtml($footerText);
      ?>
         $('.footer .container-xl').html(<?php echo json_encode($cleanFooter) ?>);
      <?php endif; ?>
   });
   </script>
   <?php
}
