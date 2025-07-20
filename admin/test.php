<?php
echo "Admin test file is working!";
echo "<br>Current directory: " . __DIR__;
echo "<br>Files in admin directory:";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "<br>- " . $file;
    }
}
?> 