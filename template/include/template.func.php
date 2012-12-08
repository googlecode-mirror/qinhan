<?php
if(!defined('IN_SITE')) {
    exit('Access Denied!');
}

function checktplrefresh($tplfile, $timecompare) {
    global $tplrefresh;

    if($tplrefresh == 1 && @filemtime($tplfile) != $timecompare) {
        parse_template($tplfile);
    }
	return TRUE;
}

function template($file) {
	global $tpldir, $objdir;

	$objfile = $objdir.$file.'.tpl.php';

    if(!file_exists($objfile)) {
        $tplfile = $tpldir.$file.'.htm';
        parse_template($tplfile);
    }
    return $objfile;
}

function parse_template($tplfile) {
    global $tpldir, $objdir;

	$file = basename($tplfile, '.htm');
	$objfile = $objdir.$file.'.tpl.php';

    if(!$fp = @fopen($tplfile, 'r')) {
		exit("Current template file '$tplfile' not found or have no access!");
	}
	$template = fread($fp, max(filesize($tplfile), 1));
	fclose($fp);

    $template = preg_replace("/[\n\r\t]*\{subtemplate\s+([a-z0-9_:]+)\}[\n\r\t]*/is", "<?php include template('\\1'); ?>", $template);

	$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
	$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
    $template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?php echo \\1; ?>", $template);

    $template = preg_replace("/[\n\r\t]*\{eval\s+(.+?)\}[\n\r\t]*/is", "<?php \\1; ?>", $template);
    $template = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/is", "<?php echo \\1; ?>", $template);
	$template = preg_replace("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/is", "\\1<?php } elseif(\\2) { ?>\\3", $template);
	$template = preg_replace("/([\n\r\t]*)\{else\}([\n\r\t]*)/is", "\\1<?php } else { ?>\\2", $template);

	for($i = 0; $i < 5; $i++) {
        $template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r]*(.+?)[\n\r]*\{\/loop\}[\n\r\t]*/is", "<?php if(is_array(\\1)) { foreach(\\1 as \\2) { ?>\\3<?php } } ?>", $template);
        $template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{\/loop\}[\n\r\t]*/is", "<?php if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>\\4<?php } } ?>", $template);
		$template = preg_replace("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r]*)(.+?)([\n\r]*)\{\/if\}([\n\r\t]*)/is", "\\1<?php if(\\2) { ?>\\3\\4\\5<?php } ?>\\6", $template);
	}

    $template = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1; ?>", $template);
	$template = preg_replace("/ \?\>[\n\r]*\<\?php /s", " ", $template);

	$template = preg_replace("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/e", "transamp('\\0')", $template);
	$template = preg_replace("/\<script[^\>]*?src=\"(.+?)\"(.*?)\>\s*\<\/script\>/ise", "stripscriptamp('\\1', '\\2')", $template);

    $template = "<?php if(!defined('IN_SITE')) exit('Access Denied!'); checktplrefresh('$tplfile', ".time()."); ?>\n$template";

    if(!$fp = @fopen($objfile, 'wb')) {
		exit("Directory '$objdir' not found or have no access!");
	}
	flock($fp, LOCK_EX);
    fwrite($fp, $template);
    flock($fp, LOCK_UN);
	fclose($fp);
}

function transamp($str) {
	$str = str_replace('&', '&amp;', $str);
	$str = str_replace('&amp;amp;', '&amp;', $str);
	$str = str_replace('\"', '"', $str);
	return $str;
}

function stripscriptamp($s, $extra) {
	$extra = str_replace('\\"', '"', $extra);
	$s = str_replace('&amp;', '&', $s);
	return "<script src=\"$s\" type=\"text/javascript\"$extra></script>";
}
?>
