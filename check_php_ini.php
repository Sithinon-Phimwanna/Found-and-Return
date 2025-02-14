<?php
$php_ini = php_ini_loaded_file();
if ($php_ini) {
    echo "ขนาดของ php.ini: " . filesize($php_ini) . " bytes";
    echo ini_get('upload_max_filesize');
} else {
    echo "ไม่พบไฟล์ php.ini";
}
?>