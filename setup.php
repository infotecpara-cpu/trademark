<?php

use GlpiPlugin\Trademark\Config;

// Versão atualizada para refletir a compatibilidade
define('PLUGIN_TRADEMARK_VERSION', '2.0.3-glpi11');
define("PLUGIN_TRADEMARK_MIN_GLPI_VERSION", "11.0.0");
define("PLUGIN_TRADEMARK_MAX_GLPI_VERSION", "11.99.99");

/**
 * Init plugin
 */
function plugin_init_trademark() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['trademark'] = true;

   // No GLPI 11, o autoload deve ser carregado o quanto antes
   $autoload = __DIR__ . '/vendor/autoload.php';
   if (file_exists($autoload)) {
      require_once $autoload;
   }

   // Verifica se está instalado e ativo para registrar os hooks
   if (Plugin::isPluginActive('trademark')) {

      /* ---------- Registro de Classes ---------- */
      // No GLPI 11, o 'addtabon' deve referenciar a classe onde a aba aparecerá
      Plugin::registerClass(\GlpiPlugin\Trademark\Config::class, ['addtabon' => ['Config']]);

      /* ---------- Hooks de Interface ---------- */
      $PLUGIN_HOOKS['config_page']['trademark'] = 'front/config.form.php';

      // Hook para exibição na tela de login
      $PLUGIN_HOOKS['display_login']['trademark'] = 'plugin_trademark_display_login';

      /* ---------- Ativos (CSS e JS) ---------- */
      // Nota: No GLPI 11, prefira arquivos estáticos (.css/.js) em vez de .php se possível
      $PLUGIN_HOOKS['add_css']['trademark'][] = 'front/internal.css.php';
      $PLUGIN_HOOKS['add_javascript']['trademark'][] = 'front/internal.js.php';
   }
}

/**
 * Version info
 */
function plugin_version_trademark() {
   return [
      'name'           => __('Trademark', 'trademark'),
      'version'        => PLUGIN_TRADEMARK_VERSION,
      'author'         => 'Nextflow / Edgard',
      'homepage'       => 'https://nextflow.com.br/plugin-glpi/trademark',
      'license'        => 'GPL v2+',
      'requirements'   => [
         'glpi' => [
            'min' => PLUGIN_TRADEMARK_MIN_GLPI_VERSION,
            'max' => PLUGIN_TRADEMARK_MAX_GLPI_VERSION
         ]
      ]
   ];
}

/**
 * Prerequisites check
 */
function plugin_trademark_check_prerequisites() {
   if (version_compare(GLPI_VERSION, PLUGIN_TRADEMARK_MIN_GLPI_VERSION, '<')) {
      echo "Este plugin requer o GLPI >= " . PLUGIN_TRADEMARK_MIN_GLPI_VERSION;
      return false;
   }
   return true;
}

function plugin_trademark_check_config() {
   return true;
}
