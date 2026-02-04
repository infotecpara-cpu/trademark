<?php

namespace GlpiPlugin\Trademark;

use CommonDBTM;
use CommonGLPI;
use Config as GlpiConfig;
use Dropdown;
use Html;
use Session;
use Toolbox;
use Plugin;

class Config extends CommonDBTM {

   private static $_cache = null;

   static function getTypeName($nb = 0) {
      return __('Trademark Configuration', 'trademark');
   }

   static function getConfig($name, $defaultValue = null) {
      if (self::$_cache === null) {
         // No GLPI 11, valores de configuração de plugins são buscados na tabela glpi_configs
         self::$_cache = GlpiConfig::getConfigurationValues('trademark');
      }

      if (isset(self::$_cache[$name]) && self::$_cache[$name] !== '') {
         return self::$_cache[$name];
      }
      return $defaultValue;
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
      // No GLPI 11, comparamos com o nome da classe global de configuração
      if ($item instanceof GlpiConfig) {
         return [1 => __('Trademark', 'trademark')];
      }
      return '';
   }

   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
      if ($item instanceof GlpiConfig) {
         $config = new self();
         $config->showFormDisplay();
      }
      return true;
   }

   /**
    * Renderiza o formulário de configuração com o estilo do GLPI 11 (Tabler)
    */
   function showFormDisplay() {
      $fields = GlpiConfig::getConfigurationValues('trademark');

      // Mescla com valores padrão caso estejam vazios
      $this->getEmpty();
      $fields = array_merge($this->fields, $fields);

      echo "<form action='" . Toolbox::getItemTypeFormURL(self::class) . "' method='post' enctype='multipart/form-data'>";
      echo "<div class='card'>";
      echo "<div class='card-header'><h4 class='card-title'>" . __('Trademark Visual Settings', 'trademark') . "</h4></div>";
      echo "<div class='card-body'>";

      echo "<table class='table table-striped card-table'>";

      // Exemplo: Título da Página
      echo "<tr><td>" . __('Page Title', 'trademark') . "</td>";
      echo "<td><input type='text' class='form-control' name='page_title' value='".Html::entities_deep($fields['page_title'])."'></td></tr>";

      // Exemplo: Imagem de Login
      echo "<tr><td>" . __('Login Logo', 'trademark') . "</td>";
      echo "<td>";
      Html::file(['name' => '_login_picture', 'display' => true]);
      if (!empty($fields['login_picture'])) {
         echo "<div class='mt-2'><img src='".Html::entities_deep($fields['login_picture'])."' style='max-height: 50px;'></div>";
         echo "<label class='form-check mt-1'><input type='checkbox' class='form-check-input' name='_blank_login_picture'> " . __('Remove', 'trademark') . "</label>";
      }
      echo "</td></tr>";

      echo "</table>";
      echo "</div>"; // card-body

      echo "<div class='card-footer text-end'>";
      echo "<input type='submit' name='update' value='".__s('Save')."' class='btn btn-primary'>";
      echo "</div>";

      echo "</div>"; // card
      Html::closeForm();
   }

   /**
    * Processa o upload de imagens e atualização de configs
    */
   static function configUpdate($input) {
      $old = GlpiConfig::getConfigurationValues('trademark');

      // Aqui você deve usar o seu PluginTrademarkToolbox::savePicture antigo
      // ou migrar para a nova lógica de Documentos/Imagens do GLPI 11.
      // Exemplo simplificado para manter a compatibilidade com seu código:
      $input = self::checkPicture('login_picture', $input, $old, 240, 130);
      $input = self::checkPicture('internal_picture', $input, $old, 100, 55);

      // Salva na tabela glpi_configs sob o contexto 'trademark'
      foreach ($input as $key => $value) {
         if (!str_starts_with($key, '_')) {
            GlpiConfig::setConfigurationValues('trademark', [$key => $value]);
         }
      }

      Session::addMessageAfterRedirect(__('Configuration updated', 'trademark'), true, INFO);
   }

   function getEmpty() {
      $this->fields = [
         'page_title'               => '',
         'login_picture'            => '',
         'internal_picture'         => '',
         'login_picture_max_width'  => '240px',
         'login_picture_max_height' => '130px',
         'internal_picture_width'   => '100px',
         'internal_picture_height'  => '55px',
      ];
   }
}
