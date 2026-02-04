<?php

namespace GlpiPlugin\Trademark;

class Toolbox {
   static function getPictureUrl($filename) {
      if (empty($filename)) return '';
      return "/plugins/trademark/pics/" . $filename;
   }

   static function getTimestamp() {
      return time();
   }
}
