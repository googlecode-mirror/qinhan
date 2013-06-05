<?php

//�ṩ��̻��� ���߼�ɨ��,ȷ���������һ���Դ���.
class project_function
{

    /**
     * @desc ͳ�ƺ�����������
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-12-16 20:55:31
     * @throws ע��:��DB�쳣����
     */
    function _function_author($token = array(), $module_name, $file)
    {
        $fixi = 0;
        $fix_array = array();
        $open = false;
        $doc = NULL;
        foreach ($token as $k => $v)
        {
            if (is_array($v))
            {
                $v[0] = token_name($v[0]);
                if (in_array($v[0], array(
                            'T_DOC_COMMENT'
                        )))
                {
                    $doc = $v;
                }

                if (in_array($v[0], array(
                            'T_FUNCTION'
                        )))
                {
                    $open = true;
                    $out = array();
                    if ($doc)
                    {
                        preg_match('#author\s+(.*)\s+#iUs', $doc[1], $out);
                        if (!$fix_array[$fixi]['author'])
                            $fix_array[$fixi]['author'] = $out[1];
                    }
                    $doc = NULL;
                }

                if ($open)
                {
                    $fix_array[$fixi]['txt'] .= $v[1];
                    if (!$fix_array[$fixi]['line'])
                        $fix_array[$fixi]['line'] = $v[2];
                    if (!$fix_array[$fixi]['name'] && $v[0] == 'T_STRING')
                    {
                        $fix_array[$fixi]['name'] = $v[1];
                    }
                }
            } else
            {
                if ($open)
                {

                    $fix_array[$fixi]['txt'] .= $v;
                    if ($v == '{')
                        $openi++;
                    if ($v == '}')
                    {
                        $openi--;
                        if (!$openi)
                        {
                            $open = false;
                            $openi = 0;
                            $fixi++;
                        }
                    }
                }
            }
        }

        foreach ($fix_array as $k => $v)
        {
            $txt = explode("\n", $v['txt']);
            $txtline = count($txt);
            $fix_array[$k]['txt'] = join("\n", array_slice($txt, 0, 4)) . "\n({$txtline}��)";
        }
        //
        foreach ($fix_array as $k => $v)
        {
            if (!$v['author'])
                $v['author'] = "δ֪";
            elseif (function_exists('mb_convert_encoding'))
            {
                $is_utf8 = preg_match('%^(?:
                [\x09\x0A\x0D\x20-\x7E]              # ASCII
                | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
                |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
                |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
                |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
                | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
                |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
                )*$%xs', $v['author']);
                if ($is_utf8)
                    $v['author'] = mb_convert_encoding($v['author'], 'GB2312', 'UTF8');
            }
            if (strpos($file, '/header_funtion.php') !== false || strpos($file, '/project') !== false)
                _status(1, $module_name . "(���븺����)", $v['author'] . "(��Ŀ)", basename($file) . '/' . $v['name'], var_export($v, true), VIP, 0, 'replace');
            else
                _status(1, $module_name . "(���븺����)", $v['author'], basename($file) . '/' . $v['name'], var_export($v, true), VIP, 0, 'replace');
        }
    }

    /**
     * @desc �߼�Ƕ�׵���ȼ��
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-12-02 15:04:16
     * @throws ע��:��DB�쳣����
     */
    function _if_deep()
    {
        
    }

    /**
     * @desc ������������
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-12-02 11:12:17
     * @throws ע��:��DB�쳣����
     */
    function _function_count($token = array(), $module_name, $file)
    {
        $fixi = 0;
        $fix_array = array();
        $open = false;
        $openi = 0;
        $last_line = 0;
        foreach ($token as $k => $v)
        {
            if (is_array($v))
            {
                $v[0] = token_name($v[0]);
                if (in_array($v[0], array(
                            'T_FUNCTION'
                        )))
                {
                    $open = true;
                }
                if ($v[0] == 'T_CURLY_OPEN')
                    $openi++;
                if ($open)
                {
                    if (!$fix_array[$fixi]['line'])
                        $fix_array[$fixi]['line'] = $v[2];
                    if (!$fix_array[$fixi]['name'] && $v[0] == 'T_STRING')
                    {
                        $fix_array[$fixi]['name'] = $v[1];
                    }
                }
                $last_line = $v[2];
            } else
            {
                if ($open)
                {

                    if ($v == '{')
                        $openi++;
                    if ($v == '}')
                    {
                        $openi--;
                        if (!$openi)
                        {
                            $open = false;
                            $openi = 0;
                            $fixi++;
                        }
                    }
                }
            }
        }
        $tmp_fix_array = array();
        foreach ($fix_array as $k => $v)
            $tmp_fix_array[] = "{$v['name']}@{$v['line']}��";
        //�ļ�����
        $num = intval(count($fix_array) / 100);
        _status(count($fix_array), $module_name . "(�����ֲ�)", ($num * 100) . '-' . ($num * 100 + 100) . "��", $file, join("\n", $tmp_fix_array), VIP, 0, 'replace');
        //�ļ�����
        $num = intval($last_line / 1000);
        _status($last_line, $module_name . "(��������)", ($num * 1000) . '-' . ($num * 1000 + 1000) . "��", $file, join("\n", $tmp_fix_array), VIP, 0, 'replace');
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-11-27 00:01:42
     * @throws ע��:��DB�쳣����
     */
    function _disable_function($token = array(), $module_name, $file)
    {
        $block_array = explode(',', 'file_get_contents,curl_init,curl_multi_init,fopen,file_put_contents,
            file,fopen,base64_encode,unlink,error_reporting,display_errors
            system,exec,shell_exec,passthru,proc_open,proc_close,
            proc_get_status,checkdnsrr,getmxrr,getservbyname,getservbyport,
            syslog,popen,show_source,highlight_file,dl,socket_listen,socket_create,socket_bind,
            socket_accept, socket_connect, stream_socket_server, stream_socket_accept,stream_socket_client,
            ftp_connect,ftp_login,ftp_pasv,ftp_get,sys_getloadavg,disk_total_space,
            disk_free_space,posix_ctermid,posix_get_last_error,posix_getcwd, posix_getegid,posix_geteuid,
            posix_getgid, posix_getgrgid,posix_getgrnam,posix_getgroups,posix_getlogin,posix_getpgid,
            posix_getpgrp,posix_getpid, posix_getppid,posix_getpwnam,posix_getpwuid, posix_getrlimit,
             posix_getsid,posix_getuid,posix_isatty, posix_kill,posix_mkfifo,posix_setegid,posix_seteuid,
             posix_setgid, posix_setpgid,posix_setsid,posix_setuid,posix_strerror,posix_times,
             posix_ttyname,posix_uname');
        array_walk($block_array, create_function('&$v,$k', '$v=trim($v);'));
        $fixi = 0;
        $fix_array = array();
        $open = false;
        $openi = 0;
        foreach ($token as $k => $v)
        {
            if (is_array($v))
            {
                $v[0] = token_name($v[0]);
                if (in_array($v[0], array(
                            'T_FUNCTION'
                        )))
                {
                    $open = true;
                }
                if ($v[0] == 'T_CURLY_OPEN')
                    $openi++;
                if ($open)
                {
                    $fix_array[$fixi]['txt'] .= $v[1];
                    if (!$fix_array[$fixi]['line'])
                        $fix_array[$fixi]['line'] = $v[2];
                    if (!$fix_array[$fixi]['name'] && $v[0] == 'T_STRING')
                    {
                        $fix_array[$fixi]['name'] = $v[1];
                    }
                    //�Ƿ������ж�
                    if ($v[0] == 'T_STRING' && in_array($v[1], $block_array))
                    {
                        $fix_array[$fixi]['disable_function'][] = $v;
                    }
                }
            } else
            {
                if ($open)
                {

                    $fix_array[$fixi]['txt'] .= $v;
                    if ($v == '{')
                        $openi++;
                    if ($v == '}')
                    {
                        $openi--;
                        if (!$openi)
                        {
                            $open = false;
                            $openi = 0;
                            $fixi++;
                        }
                    }
                }
            }
        }

        foreach ($fix_array as $k => $v)
        {
            $txt = explode("\n", $v['txt']);
            $txtline = count($txt);
            $v['txt'] = join("\n", array_slice($txt, 0, 4)) . "\n(������{$txtline}��)";
            if (!empty($v['disable_function']))
            {
                foreach ($v['disable_function'] as $kk => $vv)
                {
                    if (strpos($file, '/header_funtion.php') !== false || strpos($file, '/project') !== false)
                        _status(1, $module_name . "(��ȫBUG)", "��Σ����[��Ŀ]", "{$vv[1]}@{$v['name']}@(line:{$v['line']})@{$file}", var_export($v, true), VIP);
                    else
                        _status(1, $module_name . "(��ȫBUG)", "��Σ����", "{$vv[1]}@{$v['name']}@(line:{$v['line']})@{$file}", var_export($v, true), VIP);
                }
            }
        }
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-11-25 21:55:33
     * @throws ע��:��DB�쳣����
     */
    function _sign($token = array(), $module_name, $file)
    {
        $fixi = 0;
        $fix_array = array();
        $open = false;
        $openi = 0;
        foreach ($token as $k => $v)
        {
            if (is_array($v))
            {
                $v[0] = token_name($v[0]);
                if (in_array($v[0], array(
                            'T_FUNCTION'
                        )))
                {
                    $open = true;
                }
                if ($v[0] == 'T_CURLY_OPEN')
                    $openi++;
                if ($open)
                {
                    $fix_array[$fixi]['txt'] .= $v[1];
                    if (!$fix_array[$fixi]['line'])
                        $fix_array[$fixi]['line'] = $v[2];
                    if (!$fix_array[$fixi]['name'] && $v[0] == 'T_STRING')
                    {
                        $fix_array[$fixi]['name'] = $v[1];
                    }
                    //�����ж�
                    if ($v[0] == 'T_STRING' && $v[1] == 'SIGN')
                        $fix_array[$fixi]['sign'] = 1;
                }
            } else
            {
                if ($open)
                {

                    $fix_array[$fixi]['txt'] .= $v;
                    if ($v == '{')
                        $openi++;
                    if ($v == '}')
                    {
                        $openi--;
                        if (!$openi)
                        {
                            $open = false;
                            $openi = 0;
                            $fixi++;
                        }
                    }
                }
            }
        }

        foreach ($fix_array as $k => $v)
        {
            $v['txt'] = strtolower($v['txt']);
            //���ڹؼ�sql���.������ǩ����֤!
            if (strpos($v['txt'], 'insert ') !== false || strpos($v['txt'], 'update ') !== false || strpos($v['txt'], 'delete ') !== false)
            {
                if (!$v['sign'])
                {
                    $txt = explode("\n", $v['txt']);
                    $txtline = count($txt);
                    $v['txt'] = join("\n", array_slice($txt, 0, 4)) . "\n(������{$txtline}��)";
                    if (strpos($file, '/header_funtion.php') !== false || strpos($file, '/project') !== false)
                        _status(1, $module_name . "(��ȫBUG)", "CSRF����[��Ŀ]", "{$v['name']}@(line:{$v['line']})@{$file}", var_export($v, true), VIP);
                    else
                        _status(1, $module_name . "(��ȫBUG)", "CSRF����", "{$v['name']}@(line:{$v['line']})@{$file}", var_export($v, true), VIP);
                }
            }
        }
    }

    /**
     * @desc WHAT?
     * @author ����̩ mailto:resia@dev.ppstream.com
     * @since  2012-11-25 00:00:41
     * @throws ע��:��DB�쳣����
     */
    function _xss($token = array(), $module_name, $file)
    {
        $fixi = 0;
        $fix_array = array();
        $open = false;
        foreach ($token as $k => $v)
        {
            if (is_array($v))
            {
                $v[0] = token_name($v[0]);
                if (in_array($v[0], array(
                            'T_ECHO',
                            'T_PRINT',
                            'T_OPEN_TAG_WITH_ECHO',
                            'T_EXIT'
                        )))
                {
                    $open = true;
                }
                if ($open)
                {
                    if ($v[0] == 'T_VARIABLE')
                        $fix_array[$fixi]['must'] = 1;
                    $fix_array[$fixi]['txt'] .= $v[1];
                    if (!$fix_array[$fixi]['line'])
                        $fix_array[$fixi]['line'] = $v[2];
                }
                //
                if ($open && $v[0] == 'T_CLOSE_TAG')
                {
                    $open = false;
                    $fixi++;
                }
            } else
            {
                if ($open)
                {
                    $fix_array[$fixi]['txt'] .= $v;
                    if ($v == ';')
                    {
                        $open = false;
                        $fixi++;
                    }
                }
            }
        }
        foreach ($fix_array as $k => $v)
        {
            if (!$v['must'])
            {
                unset($fix_array[$k]);
            } else
            {
                $v['txt'] = strtolower($v['txt']);
                foreach (array(
            'strip_tags',
            'htmlspecialchars',
            'json_encode',
            'floatval',
            'intval',
            'round',
            'urlencode',
            'rawurlencode',
            'http_build_query',
            'md5',
            'date',
            'count'
                ) as $kk => $vv)
                {
                    if (strpos($v['txt'], $vv . "(") !== false)
                    {
                        unset($fix_array[$k]);
                        break;
                    }
                }
            }
        }
        foreach ($fix_array as $k => $v)
        {
            if (strpos($file, '/header_funtion.php') !== false || strpos($file, '/project') !== false)
                _status(1, $module_name . "(��ȫBUG)", "XSSע��[��Ŀ]", $file, var_export($v, true), VIP);
            else
                _status(1, $module_name . "(��ȫBUG)", "XSSע��", $file, var_export($v, true), VIP);
        }
    }

}
