<?php

namespace GlpiPlugin\Trademark;

use CommonDBTM;
use CommonGLPI;
use Config as GlpiConfig;
use Dropdown;
use Html;
use Session;
use Toolbox;

class Config extends CommonDBTM {

   private static $_cache = null;
   private static $_i = 1;

   static function getConfig($name, $defaultValue = null) {

      if (self::$_cache === null) {
         $config = new self();
         $config->getEmpty();
         $config->fields = array_merge(
            $config->fields,
            Config::getConfigurationValues('trademark')
         );

         self::$_cache = $config->fields;
      }

      if (isset(self::$_cache[$name]) && self::$_cache[$name] !== '') {
         return self::$_cache[$name];
      }
      return $defaultValue;
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
      switch (get_class($item)) {
         case Config::class:
            return [1 => t_trademark('Trademark')];
         default:
            return '';
      }
   }

   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
      switch (get_class($item)) {
         case Config::class:
            $config = new self();
            $config->showFormDisplay();
            break;
      }
      return true;
   }

   protected static function checkPicture($name, $input, $old, $width = 0, $height = 0, $max_size = 500) {

      $blank = "_blank_$name";
      $new = "_$name";

      if (isset($input[$blank]) && $input[$blank]) {
         unset($input[$blank]);
         if (!empty($old[$name])) {
            PluginTrademarkToolbox::deletePicture($old[$name]);
         }
         $input[$name] = '';
      } else if (!empty($input[$new][0])) {

         $picName = array_shift($input[$new]);
         $picPath = GLPI_TMP_DIR . '/' . $picName;
         $picResizedPath = GLPI_TMP_DIR . '/resized_' . $picName;

         if ($width || $height) {
            if (PluginTrademarkToolbox::resizePicture(
               $picPath,
               $picResizedPath,
               $width,
               $height,
               0, 0, 0, 0,
               $max_size
            )) {
               $picPath = $picResizedPath;
            }
         }

         if ($dest = PluginTrademarkToolbox::savePicture($picPath)) {
            $input[$name] = $dest;
         } else {
            Session::addMessageAfterRedirect(
               __('Unable to save picture file.'),
               true,
               ERROR
            );
         }

         if (!empty($old[$name])) {
            PluginTrademarkToolbox::deletePicture($old[$name]);
         }
      }

      unset(
         $input["_$name"],
         $input["_prefix_$name"],
         $input["_prefix_new_$name"],
         $input["_tag_$name"],
         $input["_tag_new_$name"],
         $input["_uploader_$name"],
         $input["new_$name"],
         $input[$blank],
         $input[$new]
      );

      return $input;
   }

   static function configUpdate($input) {

      $old = GlpiConfig::getConfigurationValues('trademark');
      unset($input['_no_history']);

      $input = self::checkPicture('favicon_picture', $input, $old, 192, 192, 192);
      $input = self::checkPicture('login_picture', $input, $old, 145, 80, 300);
      $input = self::checkPicture('internal_picture', $input, $old, 100, 55, 300);
      $input = self::checkPicture('login_background_picture', $input, $old);

      $input['timestamp'] = time();
      PluginTrademarkToolbox::setTimestamp($input['timestamp']);

      Session::addMessageAfterRedirect(
         __('Item successfully updated'),
         false,
         INFO
      );

      return $input;
   }

   function getEmpty() {

      $defaultCss = PluginTrademarkScss::hasScssSuport()
         ? 'scss'
         : 'css';

      $this->fields = [
         'favicon_picture' => '',
         'page_title' => '',
         'page_footer_display' => 'original',
         'page_footer_text' => '',
         'login_picture' => '',
         'login_picture_max_width' => '240px',
         'login_picture_max_height' => '130px',
         'login_css_custom' => '',
         'login_css_type' => $defaultCss,
         'login_theme' => '',
         'internal_picture' => '',
         'internal_picture_width' => '100px',
         'internal_picture_height' => '55px',
         'internal_css_custom' => '',
         'internal_css_type' => $defaultCss,
      ];
   }

   // ⬇️ O restante do arquivo (HTML, CSS, JS, formulários)
   // pode continuar exatamente como está no seu código atual
}
