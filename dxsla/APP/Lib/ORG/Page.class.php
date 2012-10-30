<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

class Page extends Think {
    // 起始行数
    public $firstRow	;
    // 列表每页显示行数
    public $listRows	;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页栏每页显示的页数
    protected $rollPage   ;

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     +----------------------------------------------------------
     */
    public function __construct($totalRows,$listRows,$parameter='') {
        $this->totalRows = $totalRows;
		$p = $parameter ? $parameter : 'p';
        $this->parameter = $p;
        $this->rollPage = C('PAGE_ROLLPAGE');
        $this->listRows = !empty($listRows)?$listRows:C('PAGE_LISTROWS');
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage  = !empty($_GET[$p]) ? max(intval($_GET[$p]), 1):1;
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    /**
     +----------------------------------------------------------
     * 分页显示输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */

	//xing393939修改2012-01-02
    public function show($hide = false) {
        if($this->totalRows < 2) return '';
        $p = $this->parameter;
		$url = $_SERVER['REQUEST_URI'];
		$pos = strpos($url, '/index.php?s=');
		if($pos === 0) {
			$url = substr($url, 13);
		}
        $url .= (strpos($url, '?') ? '' : "?");
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'], $params);
            unset($params[$p]);			
            $url = $parse['path'] . '?';
			$url .= $params ? http_build_query($params) . '&' : '';
        }
		if($pos === 0) {
			$url = '/index.php?s=' . $url;
		}
        $p .= '=';

        $str = '';
        $ruler = 10;
        $offset = 2;
		if($ruler > $this->totalPages) {
			$from = 1;
			$to = $this->totalPages;
		} else {
			$from = $this->nowPage - $offset;
			$to = $from + $ruler - 1;
			if($from < 1) {
				$to = $this->nowPage + 1 - $from;
				$from = 1;
				if($to - $from < $ruler) {
					$to = $ruler;
				}
			} else if($to > $this->totalPages) {
				$from = $this->totalPages - $ruler + 1;
				$to = $this->totalPages;
			}
		}
		if($this->nowPage != 1) {
            $str .= "<a href=\"".$url.$p.($this->nowPage-1)."\">上一页</a>";
		} else {
            $str .= "&lt;&lt;上一页 ";
        }
        if(!$hide) {
            //$str .= ;
            $str .= $from > 1 ? "<a href=\"".$url.$p."1\">1</a>" . ($from > 2 ? ' .. ' : '') : '';
            for($i = $from; $i <= $to; $i ++) {
                $str .= $i == $this->nowPage ? '<span class="current">'.$this->nowPage.'</span>' : "<a href=\"".$url.$p.$i."\">$i</a>";
            }
            $str .= $to < $this->totalPages ? '..' : '';
        }
        if($this->totalPages != $this->nowPage) {
            if($this->nowPage > 3 && isset($GLOBALS['i']['group_type']) && $GLOBALS['i']['group_type'] < 2) {
				$this->nowPage --;
			}
			$str .= "<a href=\"".$url.$p.($this->nowPage+1)."\">下一页</a>";
        } else {
            $str .= " 下一页&gt;&gt;";
        }

        return $str;
    }

}
?>