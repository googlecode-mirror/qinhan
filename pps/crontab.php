<?php

if (!$_SERVER['HTTP_HOST'] || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || strpos($_SERVER['REMOTE_ADDR'], '10.') === 0) {
    
} else {
    if (is_file("index.php"))
        die(header("location: /index.php"));
    else
        die("No input file specified." . date('r'));
}
//������������ݱ��:
$dir = './crontab';
exec("chmod 0755 {$dir}/* ");
if ($dh = opendir($dir)) {
    while (($file = readdir($dh)) !== false) {
        if ($file == '.' || $file == '..')
            continue;
        //ȫ·��
        $nfile = $dir . '/' . $file;
        if (!is_dir($nfile) && strpos($file, '.sh') !== false) {
            $data = file_get_contents($nfile);
            $data = str_replace("\r", NULL, $data);
            print_r("�޸��ļ�:{$nfile}");
            print_r("<br>\n");
            $fp = fopen($nfile, 'w');
            if ($fp) {
                fwrite($fp, $data);
                fclose($fp);
                chmod($nfile, 0755);
            }
        }
    }
    closedir($dh);
}
