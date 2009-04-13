#!/usr/local/bin/php
<?php
    /**
    * o------------------------------------------------------------------------------o
    * | This package is licensed under the Phpguru license. A quick summary is       |
    * | that for commercial use, there is a small one-time licensing fee to pay. For |
    * | registered charities and educational institutes there is a reduced license   |
    * | fee available. You can read more  at:                                        |
    * |                                                                              |
    * |                  http://www.phpguru.org/static/license.html                  |
    * o------------------------------------------------------------------------------o
    *
    * © Copyright 2008,2009 Richard Heyes
    */

    $dir = !empty($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '.';
    
    $dh = opendir($dir);
    
    while ($filename = readdir($dh)) {
        if ($filename == '.' OR $filename == '..') {
            continue;
        }
        
        if (filemtime($dir . DIRECTORY_SEPARATOR . $filename) < time()) {
            unlink($dir . DIRECTORY_SEPARATOR . $filename);
        }
    }
?>