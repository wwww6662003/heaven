<?php
/**
 * @package HeavenMVC
 * @version 1.0 Imagelib.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */

if(!defined('IN_HEAVEN')){
	exit;
}
class Lib_Imagelib extends Core_Base {
	//定义变量.
	private static $instance; //用于构建类的singleton模式参数

	public $image_url;	//原图片路径,该图片在验证码时指背景图片,在水印图片时指水印图片.
	public $font_name;	//字体名称.
	public $font_size;	//字体大小.	
	protected $image;	//实例化对象名称.
	protected $width;	//图象宽度.
	protected $height;	//图象高度.
	protected $type;	//图片的格式,如:JPG,GIF,PNG等	
	protected $font_x;	//文字的横坐标.
	protected $font_y;	//纵坐标.	
	protected $font_color;	//字体颜色.
	protected $text_content; //验证码内容.
	public $session_name;	//生成验证码SESSION的名称，用于类外数据验证
	protected $image_width;	 //水印原图片的宽度
	protected $image_height; //水印原图片的高度	
	protected $width_new;	//生成缩略图的实际宽度
	protected $height_new;	//生成缩略图的实际高度	
	protected $water_image;		//水印图片的实例化对象	
	protected $water_x;		//生成水印区域的横坐标
	protected $water_y;		//生成水印区域的纵坐标
	protected $alpha;	//水印图片水印区域的透明度
	protected $water_width;	//水印图片的宽度
	protected $water_height;	//水印图片的高度
	
	/**
     * 构造函数,用于初始化运行环境.
     * @access public
     * @return mixed
     */
	public function __construct(){		
				
		$this->font_size = 16;		
		$this->font_name = HEAVEN_PATH.'/Public/Fonts/aispec.ttf';
				
		return true;
	}
	
	//初始化运行环境,实例化image对象.
	protected function parse_vdcode_init(){		
		if(!$this->image){			
			//分析验证码的背景图片.
			$this->image_url = empty($this->image_url) ? HEAVEN_PATH.'/Public/Images/vdcode_bg.jpg' : $this->image_url;			
			$this->session_name = empty($this->session_name) ? 'heaven_image_vdcode' : $this->session_name;
			$this->parse_image_info($this->image_url);											
		}		
		return true;
	}
	
	//初始化运行环境,获取图片格式并实例化.
	protected function parse_image_info($url){		
		if(!$this->image){			
			list($this->image_width, $this->image_height, $type) = getimagesize($url);
			switch ($type){					
				case 1:
					$this->image = imagecreatefromgif($url);
					$this->type = 'gif';
					break;			
				case 2:
					$this->image = imagecreatefromjpeg($url);
					$this->type = 'jpg';
					break;				
				case 3:
					$this->image = imagecreatefrompng($url);
					$this->type = 'png';
					break;				
				case 4:
					$this->image = imagecreatefromwbmp($url);
					$this->type = 'bmp';
					break;
			}		
		}				
		return true;
	}
	
	//设置字体名称.
	public function set_font_name($name, $size=false){
		if ($name){
			$this->font_name = $name;
		}		
		if ($size){
			$this->font_size = intval($size);
		}
		return $this;
	}
	
	//设置字体大小.
	public function set_font_size($size){
		if ($size){
			$this->font_size = intval($size);
		}		
		return $this;
	}

	//设置背景图片或水印图片的URL.
	public function set_image_url($url){
		if ($url){
			$this->image_url = $url;
		}		
		return $this;
	}

	//设置水印图片水印的坐标位置.
	public function set_watermark_position($x, $y){
		if ($x){
			$this->water_x = $x;
		}
		if($y){
			$this->water_y = $y;
		}
		return $this;
	}
	
	//设置生成图片的大小.
	public function set_image_size($width, $height){
		if ($width){
			$this->width = $width;
		}
		if ($height){
			$this->height = $height;
		}		
		return $this;
	}

	//设置水印图片水印区域的透明度.
	public function set_watermark_alpha($param){
		if ($param){
			$this->alpha = intval($param);
		}		
		return $this;
	}

	//设置验证码的session_name.
	public function set_session_name($name){
		if ($name){
			$this->session_name = $name;
		}
		return $this;
	}
	
	//设置验证码内容.
	public function set_text_content($content){
		if ($content){
			$this->text_content = $content;
		}
		return $this;
	}
	
	//调整文字水印区域的位置
	protected function handle_watermark_font_place(){		
		if(empty($this->font_x)||empty($this->font_y)){	
			if(empty($this->text_content)){
				Core_Action::halt('You do not set the watermark text on image!');
			}
			$bbox = imagettfbbox($this->font_size, 0, $this->font_name, $this->text_content);			
			$font_w = $bbox[2] - $bbox[0] + 5; //文字margin_right为5px,特此加5			
			$font_h = abs($bbox[7] - $bbox[1]);			
			$this->font_x = ($this->image_width > $font_w) ? $this->image_width - $font_w : 0;			
			$this->font_y = ($this->image_height > $font_h) ? $this->image_height - $font_h : 0;
		}		
		return true;
	}
	
	//获取颜色参数.
	public function set_font_color($x=false, $y=false, $z=false){	  		
	   if(is_int($x)&&is_int($y)&&is_int($z)){			
			$this->font_color = array($x, $y, $z);
		}
		else{			
			$this->font_color = array(255, 255, 255);
		}		
		return $this;
	}
	
	//常设置的文字颜色转换为图片信息.
	protected function handle_font_color(){
		if(empty($this->font_color)){
			$this->font_color = array(255, 255, 255);
		}
		return imagecolorallocate($this->image, $this->font_color[0], $this->font_color[1], $this->font_color[2]);
	}
	
	//生成验证码内容.
	protected function get_vdcode_content(){
		if (empty($this->text_content)){
			$char = range('A', 'Z');		
			$num1 = mt_rand(0, 25);		
			$num2 = mt_rand(1, 9);		
			$num3 = mt_rand(0, 25);		
			$num4 = mt_rand(1, 9);		
			$this->text_content = $char[$num1].$num2.$char[$num3].$num4;	
		}					
		return $this->text_content;
	}		
	
	//验证码字体及坐标的调节.
	protected function handle_vdcode_font_place(){		
		$this->get_vdcode_content();		
		$bbox = imagettfbbox($this->font_size, 0, $this->font_name, $this->text_content);		
		$font_w = $bbox[2]-$bbox[0];		
		$font_h = abs($bbox[7]-$bbox[1]);		
		$this->font_x = ceil(($this->image_width-$font_w)/2);		
		$this->font_y = ceil(($this->image_height+$font_h)/2);		
		return true;
	}
	
	//显示验证码.
	public function vdcode_display(){
		//当前面没有session_start()调用时.
		if (!isset($_SESSION)){
			session_start();
		}
		//初始化图片信息.
		$this->parse_vdcode_init();
		//处理图片颜色信息.		
		$font_color = $this->handle_font_color();
		$this->handle_vdcode_font_place();		
		imagettftext($this->image, $this->font_size, 0, $this->font_x, $this->font_y, $font_color, $this->font_name, $this->text_content);
		//将显示的验证码赋值给session.
		$_SESSION[$this->session_name] = $this->text_content;
		//当有headers内容输出时.
		if(headers_sent()){
			Core_Action::halt('headers already sent'); 
		}
		//显示图片,根据背景图片的格式显示相应格式的图片.
		switch ($this->type){				
			case 'gif':
				header('Content-type:image/gif');
				imagegif($this->image);
				break;					
			case 'jpg':
				header('Content-type:image/jpeg');
				imagejpeg($this->image);
				break;					
			case 'png':
				header('Content-type:image/png');
				imagepng($this->image);
				break;					
			case 'bmp':
				header('Content-type:image/wbmp');
				imagewbmp($this->image);
				break;
		}				
		imagedestroy($this->image);
	}
	
	//自行处理缩略图的宽度和高度，根据图片原来的宽和高的比例来进行计算
	protected function handle_image_size(){
		//当没有所生成的图片的宽度和高度设置时.
		if(empty($this->width)||empty($this->height)){
			Core_Action::halt('You do not set the image height size or width size!');
		}
		$per_w = $this->width/$this->image_width;		
		$per_h = $this->height/$this->image_height;		
		if(ceil($this->image_height*$per_w)>$this->height){			
			$this->width_new = ceil($this->image_width*$per_h);			
			$this->height_new = $this->height;
		}
		else{			
			$this->width_new = $this->width;			
			$this->height_new = ceil($this->image_height*$per_w);
		}		
		return true;
	}
	
	//生成图片的缩略图.
	public function make_limit_image($url, $dist_name){
		if (empty($url)||empty($dist_name)){
			return false;
		}
		//原图片分析.
		$this->parse_image_info($url);
		$this->handle_image_size();
		//新图片分析.
		$image_dist = imagecreatetruecolor($this->width_new, $this->height_new);
		//生成新图片.
		imagecopyresampled($image_dist, $this->image, 0, 0, 0, 0, $this->width_new, $this->height_new, $this->image_width, $this->image_height);
		$this->create_image($image_dist, $dist_name, $this->type);
		imagedestroy($image_dist);
		imagedestroy($this->image);	
		return true;			
	}
	//生成目标图片.
	protected function create_image($image_dist, $dist_name, $image_type){
		if (empty($image_dist)||empty($image_type)||empty($dist_name)){
			return false;
		}
		switch ($image_type){				
			case 'gif':
				imagegif($image_dist, $dist_name.'.gif');
				break;					
			case 'jpg':
				imagejpeg($image_dist, $dist_name.'.jpg');
				break;					
			case 'png':
				imagepng($image_dist, $dist_name.'.png');
				break;					
			case 'bmp':
				imagewbmp($image_dist, $dist_name.'.bmp');
				break;
		}
		return true;
	}
	
	//生成文字水印图片.
	public function make_text_watermark($image_url, $dist_name){
		//分析原图片.
		$this->parse_image_info($image_url);
		//所生成的图片进行分析.
		$this->handle_watermark_font_place();	
		$font_color = $this->handle_font_color();
		//生成新图片.
		imagettftext($this->image, $this->font_size, 0, $this->font_x, $this->font_y, $font_color, $this->font_name, $this->text_content);
		$this->create_image($this->image, $dist_name, $this->type);
		imagedestroy($this->image);
		return true;
	}
	
	//获取水印图片信息
	protected function handle_watermark_image(){
		if($this->image&&!$this->water_image){			
			$water_url = empty($this->image_url) ? HEAVEN_PATH.'/Public/images/watermark'.'.'.$this->type : $this->image_url;	
			list($this->water_width, $this->water_height) = getimagesize($water_url);			
			switch ($this->type){				
				case 'gif':
					$this->water_image = imagecreatefromgif($water_url);
					break;					
				case 'jpg':
					$this->water_image = imagecreatefromjpeg($water_url);
					break;					
				case 'png':
					$this->water_image = imagecreatefrompng($water_url);
					break;					
				case'bmp':
					$this->water_image = imagecreatefromwbmp($water_url);
					break;
			}
		}		
		return true;
	}

	//调整水印区域的位置,默认位置距图片右下角边沿5像素.
	protected function handle_watermark_image_place(){		
		if(empty($this->water_x)||empty($this->water_y)){			
			$this->water_x = ($this->image_width-5>$this->water_width) ? $this->image_width-$this->water_width-5 : 0;	
			$this->water_y = ($this->image_height-5>$this->water_height) ? $this->image_height-$this->water_height-5 : 0;
		}		
		return true;
	}
	
	//生成图片水印.
	public function make_image_watermark($image_url, $dist_name){
		//分析图片信息.
		$this->parse_image_info($image_url);
		//对所生成的图片进行信息分析.
		$this->alpha = empty($this->alpha) ? 85 : $this->alpha;
		$this->handle_watermark_image();
		//分析新图片的水印位置.
		$this->handle_watermark_image_place();
		//生成新图片.
		imagecopymerge($this->image, $this->water_image, $this->water_x, $this->water_y, 0, 0, $this->water_width, $this->water_height, $this->alpha);
		$this->create_image($this->image, $dist_name, $this->type);
		imagedestroy($this->image);			
		return true;
	}
	
	//晰构函数
	public function __destruct(){		
		
	}
	
	/**
     * 用于本类的静态调用,子类需要重载才能正常使用.
     * @access public
     * @param string $params 类的名称
     * @return void
     */
    public static function getInstance(){		
		if(self::$instance == null){		
			self::$instance = new self;
		}		
		return self::$instance;
	}
	
}
?>