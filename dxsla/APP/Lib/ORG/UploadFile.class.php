<?php
class UploadFile extends Think
{//类定义开始

    // 上传文件的最大值
    public $maxSize = 3292200;

    // 是否支持多文件上传
    public $supportMulti = true;

    // 允许上传的文件后缀
    // 留空不作后缀检查
    public $allowExts = array();

    // 允许上传的文件类型
    // 留空不做检查
    public $allowTypes = array();

    // 使用对上传图片进行缩略图处理
    public $thumb   =  true;
    // 图库类包路径
    public $imageClassPath = '@.ORG.Image';
	// 是否固定宽高的裁剪：0为宽高最大限制，1为固定宽高，2为只有宽有限制
	public $cut;
    // 缩略图最大宽度
    public $thumbMaxWidth;
    // 缩略图最大高度
    public $thumbMaxHeight;
    // 缩略图前缀
    public $thumbPrefix   =  '';
	// 缩略图后缀
    public $thumbSuffix  =  '';
    // 缩略图保存路径
    public $thumbPath = '';
    // 缩略图文件名
    public $thumbFile		=	'';
    // 是否移除原图
    public $thumbRemoveOrigin = false;
    // 压缩图片文件上传
    public $zipImages = false;
    // 启用子目录保存文件
    public $autoSub   =  true;
    // 子目录创建方式 可以使用hash date
    public $subType   = 'date';
    public $dateFormat = 'ymd';
    public $hashLevel =  2; // hash的目录层次
    // 上传文件保存路径
    public $savePath = '';
	// 是否自动检查附件
    public $autoCheck = true;
    // 存在同名是否覆盖
    public $uploadReplace = false;

    // 上传文件命名规则
    // 例如可以是 time uniqid com_create_guid 等
    // 必须是一个无需任何参数的函数名 可以使用自定义函数
    public $saveRule = 'uniqid';

    // 上传文件Hash规则函数名
    // 例如可以是 md5_file sha1_file 等
    public $hashType = 'md5_file';

    // 错误信息
    private $error = '';

    // 上传成功的文件信息
    private $uploadFileInfo ;
	
	private $imageInfo;
	private $fileIndex = 0;

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function __construct()
    {

    }

    /**
     +----------------------------------------------------------
     * 上传所有文件
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $savePath  上传文件保存路径
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function upload($savePath ='')
    {
        $savePath= $this->checkSavePath($savePath);
		if(!$savePath) return false;
		
        $fileInfo = array();
        $isUpload   = false;

        // 获取上传的文件信息
        // 对$_FILES数组信息处理
        $files	 =	 $this->dealFiles($_FILES);
        foreach($files as $key => $file) {
            //过滤无效的上传
            if(!empty($file['name'])) {
                //登记上传文件的扩展信息
                $file['key']        =  $key;
                $file['extension']  = $this->getExt($file['name']);
                $file['savepath']   = $savePath;
                $file['savename']   = $this->getSaveName($file);
				$this->fileIndex = $key;

                // 自动检查附件
                if($this->autoCheck) {
                    if(!$this->check($file))
                        return false;
                }

                //保存上传文件
                if(!$this->save($file)) return false;

                //上传成功后保存文件信息，供其他地方调用
                unset($file['tmp_name'],$file['error']);
                $fileInfo[] = $file;
                $isUpload   = true;
            }
        }
        if($isUpload) {
            $this->uploadFileInfo = $fileInfo;
            return true;
        }else {
            $this->error  =  '没有选择上传文件';
            return false;
        }
    }
		
    /**
     +----------------------------------------------------------
     * 获取网络图片
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
	public function copyFile($url) {
        $savePath= $this->checkSavePath();
		if(!$savePath) return false;
		
		$file['savepath'] = $savePath;
		$file['extension'] = $this->getExt($url);
		$file['extension'] = $file['extension'] ? $file['extension'] : 'jpg';
		$file['tmp_name'] = $savePath . '/' . microtime(1) . '.' . $file['extension'];

		$timeout = 5;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$thumb = curl_exec($ch);
		curl_close($ch);
		
		if(strlen($thumb) > $this->maxSize) {
            $this->error = '文件大小超过限制';
            return false;
		}

		//判断网络图片类型是否安全：http://topic.csdn.net/u/20110301/11/8e2153e2-5903-47a5-ae55-5612cf6084c1.html
		$mime = array('image/gif'=>'gif', 'image/x-png'=>'png', 'image/jpeg'=>'jpg');
		preg_match('/(image\/(?:.*?))\s+/i', $thumb, $arr);
		$key = strtolower(trim($arr[1]));
		//dump($arr);
		if(!array_key_exists($key, $mime)) {
            $this->error = '文件类型不允许';
            return false;
		}

		$fp = @fopen($file['tmp_name'], "a");
        @fwrite($fp, $thumb);
        fclose($fp);

		$file['savename'] = $this->getSaveName($file);
		//dump($file);
		rename($file['tmp_name'], $savePath . $file['savename']);
		$this->doThumb($file, $savePath . $file['savename']);
		$this->uploadFileInfo[0] = $file;
		return true;
	}
	
    /**
     +----------------------------------------------------------
     * 上传单个上传字段中的文件 支持多附件
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $file  上传文件信息
     * @param string $savePath  上传文件保存路径
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function uploadOne($file,$savePath=''){
        $savePath= $this->checkSavePath($savePath);
		if(!$savePath) return false;
		
        //过滤无效的上传
        if(!empty($file['name'])) {
            $fileArray = array();
            if(is_array($file['name'])) {
               $keys = array_keys($file);
               $count	 =	 count($file['name']);
               for ($i=0; $i<$count; $i++) {
                   foreach ($keys as $key)
                       $fileArray[$i][$key] = $file[$key][$i];
               }
            }else{
                $fileArray[] =  $file;
            }
            $info =  array();
            foreach ($fileArray as $key=>$file){
                //登记上传文件的扩展信息
                $file['extension']  = $this->getExt($file['name']);
                $file['savepath']   = $savePath;
                $file['savename']   = $this->getSaveName($file);
                // 自动检查附件
                if($this->autoCheck) {
                    if(!$this->check($file))
                        return false;
                }
                //保存上传文件
                if(!$this->save($file)) return false;

                unset($file['tmp_name'],$file['error']);
                $info[] = $file;
            }
            // 返回上传的文件信息
            return $info;
        }else {
            $this->error  =  '没有选择上传文件';
            return false;
        }
    }	

    /**
     +----------------------------------------------------------
     * 上传一个文件
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $name 数据
     * @param string $value  数据表名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    private function save($file)
    {
        $filename = $file['savepath'].$file['savename'];
        if(!$this->uploadReplace && is_file($filename)) {
            // 不覆盖同名文件
            $this->error	=	'文件已经存在！'.$filename;
            return false;
        }
        if(!move_uploaded_file($file['tmp_name'], auto_charset($filename,'utf-8','gbk'))) {
            $this->error = '文件上传保存错误！';
            return false;
        }
		$this->doThumb($file, $filename);
		unlink($file['tmp_name']);
		unlink($filename);
        return true;
    }

	private function doThumb($file, $filename) {
		if($this->thumb && in_array($file['extension'],array('gif','jpg','jpeg','bmp','png'))) {
			//是图像文件生成缩略图
			$thumbWidth		=	explode(',',$this->thumbMaxWidth);
			$thumbHeight		=	explode(',',$this->thumbMaxHeight);
			$thumbPrefix		=	explode(',',$this->thumbPrefix);
			$thumbSuffix = explode(',',$this->thumbSuffix);
			$thumbFile			=	explode(',',$this->thumbFile);
			$cut			=	explode(',',$this->cut);
			// 生成图像缩略图
			import($this->imageClassPath);
			$realFilename  =  $this->autoSub ? basename($file['savename']) : $file['savename'];
			$thumbPath    =  $this->thumbPath ? $this->thumbPath : substr($filename, 0, -strlen($realFilename));

			for($i=0,$len=count($thumbWidth); $i<$len; $i++) {
				$thumbname	=	$thumbPath.$thumbPrefix[$i].$realFilename.$thumbSuffix[$i].'.jpg';
				Image::thumb($filename,$thumbname,'',$thumbWidth[$i],$thumbHeight[$i],true, $cut[$i]);
			}
			if($this->thumbRemoveOrigin) {
				// 生成缩略图之后删除原图
				unlink($filename);
			} else {
				if($file['extension'] != 'jpg') {
					$imageFun = $createFun = 'ImageCreateFrom' . $file['extension'];
					$image = $createFun($filename);
					imagejpeg($image, $filename);
					//unlink($filename);
				}
			}
		}
	}

    /**
     +----------------------------------------------------------
     * 转换上传文件数组变量为正确的方式
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param array $files  上传的文件变量
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    private function dealFiles($files) {
       $fileArray = array();
       $n = 0;
       foreach ($files as $file){
           if(is_array($file['name'])) {
               $keys = array_keys($file);
               $count	 =	 count($file['name']);
               for ($i=0; $i<$count; $i++) {
                   foreach ($keys as $key)
                       $fileArray[$n][$key] = $file[$key][$i];
                   $n++;
               }
           }else{
               $fileArray[$n] = $file;
               $n++;
           }
       }
       return $fileArray;
    }

    /**
     +----------------------------------------------------------
     * 获取错误代码信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $errorNo  错误号码
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function error($errorNo)
    {
         switch($errorNo) {
            case 1:
                $this->error = '上传文件大于2M，如果是图片请换张尺寸稍小的图片吧';  //上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值
                break;
            case 2:
                $this->error = '上传文件大于2M，如果是图片请换张尺寸稍小的图片吧';  //上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值
                break;
            case 3:
                $this->error = '文件只有部分被上传';
                break;
            case 4:
                $this->error = '没有文件被上传';
                break;
            case 6:
                $this->error = '找不到临时文件夹';
                break;
            case 7:
                $this->error = '文件写入失败';
                break;
            default:
                $this->error = '未知上传错误！';
        }
        return ;
    }

    /**
     +----------------------------------------------------------
     * 根据上传文件命名规则取得保存文件名
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param string $filename 数据
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    private function getSaveName($filename)
    {
		$addition = ".".$filename['extension'];
		if(in_array($filename['extension'],array('gif','jpg','jpeg','bmp','png'))) {
			$imageInfo = $this->getImage($filename['tmp_name']);
			$addition = '_' . $imageInfo[0]	. 'x' . $imageInfo[1] . '.jpg';
		}
		$filename['hash'] = '';
		/*if(function_exists($this->hashType)) {
			$fun =  $this->hashType;
			$filename['hash'] .= $fun(auto_charset($filename['tmp_name'],'utf-8','gbk'));
		}*/
		$filename['hash'] .= base_convert(rand(0, 1296), 10, 36);
		if(function_exists($this->saveRule)) {
			$rule = $this->saveRule;
			$filename['hash'] .= $rule();
		}

		$saveName = $filename['hash'] . $addition;
        if($this->autoSub) {
            // 使用子目录保存文件
            $filename['savename'] = $saveName;
            $saveName = $this->getSubName($filename).'/'.substr($saveName, $this->hashLevel);
        }
        return $saveName;
    }

    /**
     +----------------------------------------------------------
     * 获取子目录的名称
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param array $file  上传的文件信息
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    private function getSubName($file)
    {
		$dir   =  '';
        switch($this->subType) {
            case 'date':
                $dir   =  '/' . date($this->dateFormat,time());
                break;
            case 'hash':
				break;
            default:
                break;
        }
		$name = $file['hash'];
		for($i=0; $i<$this->hashLevel; $i++) {
			$dir   .=  '/' . $name{$i};
		}
        if(!is_dir($file['savepath'].$dir)) {
            mk_dir($file['savepath'].$dir);
        }
        return $dir;
    }

    /**
     +----------------------------------------------------------
     * 检查上传的文件
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param array $file 文件信息
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function check($file) {
		//文件上传失败 捕获错误代码
        if($file['error']!== 0) {            
            $this->error($file['error']);
            return false;
        }
        //检查是否合法上传
        if(!$this->checkUpload($file['tmp_name'])) {
            $this->error = '非法上传文件！';
            return false;
        }
        //检查文件大小
        if(!$this->checkSize($file['size'])) {
            $this->error = '上传文件大小不符！';
            return false;
        }
        //检查文件Mime类型
        if(!$this->checkType($file['type'])) {
            $this->error = '上传文件MIME类型不允许！';
            return false;
        }
        //检查文件类型
        if(!$this->checkExt($file['extension'])) {
            $this->error ='上传文件类型不允许';
            return false;
        }
        // 如果是图像文件 严格检查文件格式
        if(in_array($file['extension'], array('gif','jpg','jpeg','png'))) {
			$imageInfo = $this->getImage();
			if($imageInfo === false || $this->getImgExt($file['tmp_name']) != $file['extension']) {
            	$this->error = '非法图像文件';
            	return false;
			} elseif($imageInfo[0] < 120 || $imageInfo[1] < 120) {
            	$this->error = '图片的宽高要大于120像素';
            	return false;
			}
        }
        return true;
    }

    /**
     +----------------------------------------------------------
     * 检查上传的文件类型是否合法
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param string $type 数据
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function checkType($type)
    {
        if(!empty($this->allowTypes))
            return in_array(strtolower($type),$this->allowTypes);
        return true;
    }


    /**
     +----------------------------------------------------------
     * 检查上传的文件后缀是否合法
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param string $ext 后缀名
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function checkExt($ext)
    {
		//dump($this->allowExts);
        if(!empty($this->allowExts))
            return in_array(strtolower($ext),$this->allowExts,true);
        return true;
    }

    /**
     +----------------------------------------------------------
     * 检查文件大小是否合法
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param integer $size 数据
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function checkSize($size)
    {
        return !($size > $this->maxSize) || (-1 == $this->maxSize);
    }

    /**
     +----------------------------------------------------------
     * 检查文件是否非法提交
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param string $filename 文件名
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function checkUpload($filename)
    {
        return is_uploaded_file($filename);
    }

    /**
     +----------------------------------------------------------
     * 取得上传文件的后缀
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param string $filename 文件名
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    private function getExt($filename)
    {
        $pathinfo = pathinfo($filename);
		return strtolower($pathinfo['extension']);
    }
	
	// 截取字节严格判断格式
	private function getImgExt($filename) {
		$file     = fopen($filename, "rb");
		$bin      = fread($file, 2);
		fclose($file);
		$strInfo  = @unpack("c2chars", $bin);
		$typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
		$fileType = '';
		switch($typeCode) {
			case 7790:
				$fileType = 'exe';
				break;
			case 7784:
				$fileType = 'midi';
				break;	
			case 8297:
				$fileType = 'rar';
				break;
			case 255216:
				$fileType = 'jpg';
				break;
			case 7173:
				$fileType = 'gif';
				break;
			case 6677:
				$fileType = 'bmp';
				break;
			case 13780:
				$fileType = 'png';
				break;
			default:
				$fileType = 'unknown'.$typeCode;
		}
		if($strInfo['chars1'] == '-1' && $strInfo['chars2'] == '-40') {
			return 'jpg';
		}
		if($strInfo['chars1'] == '-119' && $strInfo['chars2'] == '80') {
			return 'png';
		}
		return $fileType;
	}	

	private function getImage($tmp_file = NULL) {
		if($tmp_file) {
			$this->imageInfo = getimagesize($tmp_file);
		}
		return $this->imageInfo;
	}
	
	public function getFileIndex() {
		return $this->fileIndex;
	}
	
	private function checkSavePath($savePath = '') {
        //如果不指定保存文件名，则由系统默认
        if(empty($savePath))
            $savePath = $this->savePath;
        // 检查上传目录
        if(!is_dir($savePath)) {
            // 检查目录是否编码后的
            if(is_dir(base64_decode($savePath))) {
                $savePath	=	base64_decode($savePath);
            }else{
                // 尝试创建目录
                if(!mkdir($savePath)){
                    $this->error  =  '上传目录'.$savePath.'不存在';
                    return false;
                }
            }
        }else {
            if(!is_writeable($savePath)) {
                $this->error  =  '上传目录'.$savePath.'不可写';
                return false;
            }
        }
		return $savePath;
	}	
    /**
     +----------------------------------------------------------
     * 取得上传文件的信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array

     +----------------------------------------------------------
     */
    public function getUploadFileInfo()
    {
        return $this->uploadFileInfo;
    }

    /**
     +----------------------------------------------------------
     * 取得最后一次错误信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function getErrorMsg()
    {
        return $this->error;
    }

}//类定义结束

function auto_charset($fContents, $from='gbk', $to='utf-8') {
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key)
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else {
        return $fContents;
    }
}
if (!function_exists('imagecreatefrombmp')) { function imagecreatefrombmp($filename) {
	// version 1.00
	if (!($fh = fopen($filename, 'rb'))) {
		trigger_error('imagecreatefrombmp: Can not open ' . $filename, E_USER_WARNING);
		return false;
	}
	// read file header
	$meta = unpack('vtype/Vfilesize/Vreserved/Voffset', fread($fh, 14));
	// check for bitmap
	if ($meta['type'] != 19778) {
		trigger_error('imagecreatefrombmp: ' . $filename . ' is not a bitmap!', E_USER_WARNING);
		return false;
	}
	// read image header
	$meta += unpack('Vheadersize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vcolors/Vimportant', fread($fh, 40));
	// read additional 16bit header
	if ($meta['bits'] == 16) {
		$meta += unpack('VrMask/VgMask/VbMask', fread($fh, 12));
	}
	// set bytes and padding
	$meta['bytes'] = $meta['bits'] / 8;
	$meta['decal'] = 4 - (4 * (($meta['width'] * $meta['bytes'] / 4)- floor($meta['width'] * $meta['bytes'] / 4)));
	if ($meta['decal'] == 4) {
		$meta['decal'] = 0;
	}
	// obtain imagesize
	if ($meta['imagesize'] < 1) {
		$meta['imagesize'] = $meta['filesize'] - $meta['offset'];
		// in rare cases filesize is equal to offset so we need to read physical size
		if ($meta['imagesize'] < 1) {
			$meta['imagesize'] = @filesize($filename) - $meta['offset'];
			if ($meta['imagesize'] < 1) {
				trigger_error('imagecreatefrombmp: Can not obtain filesize of ' . $filename . '!', E_USER_WARNING);
				return false;
			}
		}
	}
	// calculate colors
	$meta['colors'] = !$meta['colors'] ? pow(2, $meta['bits']) : $meta['colors'];
	// read color palette
	$palette = array();
	if ($meta['bits'] < 16) {
		$palette = unpack('l' . $meta['colors'], fread($fh, $meta['colors'] * 4));
		// in rare cases the color value is signed
		if ($palette[1] < 0) {
			foreach ($palette as $i => $color) {
				$palette[$i] = $color + 16777216;
			}
		}
	}
	// create gd image
	$im = imagecreatetruecolor($meta['width'], $meta['height']);
	$data = fread($fh, $meta['imagesize']);
	$p = 0;
	$vide = chr(0);
	$y = $meta['height'] - 1;
	$error = 'imagecreatefrombmp: ' . $filename . ' has not enough data!';
	// loop through the image data beginning with the lower left corner
	while ($y >= 0) {
		$x = 0;
		while ($x < $meta['width']) {
			switch ($meta['bits']) {
				case 32:
				case 24:
					if (!($part = substr($data, $p, 3))) {
						trigger_error($error, E_USER_WARNING);
						return $im;
					}
					$color = unpack('V', $part . $vide);
					break;
				case 16:
					if (!($part = substr($data, $p, 2))) {
						trigger_error($error, E_USER_WARNING);
						return $im;
					}
					$color = unpack('v', $part);
					$color[1] = (($color[1] & 0xf800) >> 8) * 65536 + (($color[1] & 0x07e0) >> 3) * 256 + (($color[1] & 0x001f) << 3);
					break;
				case 8:
					$color = unpack('n', $vide . substr($data, $p, 1));
					$color[1] = $palette[ $color[1] + 1 ];
					break;
				case 4:
					$color = unpack('n', $vide . substr($data, floor($p), 1));
					$color[1] = ($p * 2) % 2 == 0 ? $color[1] >> 4 : $color[1] & 0x0F;
					$color[1] = $palette[ $color[1] + 1 ];
					break;
				case 1:
					$color = unpack('n', $vide . substr($data, floor($p), 1));
					switch (($p * 8) % 8) {
						case 0:
							$color[1] = $color[1] >> 7;
							break;
						case 1:
							$color[1] = ($color[1] & 0x40) >> 6;
							break;
						case 2:
							$color[1] = ($color[1] & 0x20) >> 5;
							break;
						case 3:
							$color[1] = ($color[1] & 0x10) >> 4;
							break;
						case 4:
							$color[1] = ($color[1] & 0x8) >> 3;
							break;
						case 5:
							$color[1] = ($color[1] & 0x4) >> 2;
							break;
						case 6:
							$color[1] = ($color[1] & 0x2) >> 1;
							break;
						case 7:
							$color[1] = ($color[1] & 0x1);
							break;
					}
					$color[1] = $palette[ $color[1] + 1 ];
					break;
				default:
					trigger_error('imagecreatefrombmp: ' . $filename . ' has ' . $meta['bits'] . ' bits and this is not supported!', E_USER_WARNING);
					return false;
			}
			imagesetpixel($im, $x, $y, $color[1]);
			$x++;
			$p += $meta['bytes'];
		}
		$y--;
		$p += $meta['decal'];
	}
	fclose($fh);
	return $im;
}}
?>