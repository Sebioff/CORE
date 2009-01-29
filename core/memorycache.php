<?php

/**
 * For caching values in the memory.
 * This cache is NOT neccessarily different for
 * each user, but could also be replaced by a global cache.
 * @author Sebastian
 */
class Core_MemoryCache {
  public function set($key, $value) {
    $_SESSION['CORE_cache'][$key] = $value;
  }
  
  public function get($key) {
    return isset($_SESSION['CORE_cache'][$key]) ? $_SESSION['CORE_cache'][$key] : false;
  }
  
  public function clear() {
    session_unset();
  }
}

?>