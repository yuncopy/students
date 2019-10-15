<?php
$file = '/tmp/swooletest.log';
for($i=0;$i<10;$i++){
    $content = $i;
    file_put_contents($file, '['.date('Y-m-d H:i:s').'] || '.$content.PHP_EOL, FILE_APPEND | LOCK_EX);
    sleep(2);
    echo $i;
}
