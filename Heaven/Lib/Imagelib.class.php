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
	//�������.
	private static $instance; //���ڹ������singletonģʽ����

	public $image_url;	//ԭͼƬ·��,��ͼƬ����֤��ʱָ����ͼƬ,��ˮӡͼƬʱָˮӡͼƬ.
	public $font_name;	//��������.
	public $font_size;	//�����С.	
	protected $image;	//ʵ������������.
	protected $width;	//ͼ����.
	protected $height;	//ͼ��߶�.
	protected $type;	//ͼƬ�ĸ�ʽ,��:JPG,GIF,PNG��	
	protected $font_x;	//���ֵĺ�����.
	protected $font_y;	//������.	
	protected $font_color;	//������ɫ.
	protected $text_content; //��֤������.
	public $session_name;	//������֤��SESSION�����ƣ���������������֤
	protected $image_width;	 //ˮӡԭͼƬ�Ŀ��
	protected $image_height; //ˮӡԭͼƬ�ĸ߶�	
	protected $width_new;	//��������ͼ��ʵ�ʿ��
	protected $height_new;	//��������ͼ��ʵ�ʸ߶�	
	protected $water_image;		//ˮӡͼƬ��ʵ��������	
	protected $water_x;		//����ˮӡ����ĺ�����
	protected $water_y;		//����ˮӡ�����������
	protected $alpha;	//ˮӡͼƬˮӡ�����͸����
	protected $water_width;	//ˮӡͼƬ�Ŀ��
	protected $water_height;	//ˮӡͼƬ�ĸ߶�
	
	/**
     * ���캯��,���ڳ�ʼ�����л���.
     * @access public
     * @return mixed
     */
	public function __construct(){		
				
		$this->font_size = 16;		
		$this->font_name = HEAVEN_PATH.'/Public/Fonts/aispec.ttf';
				
		return true;
	}
	
	//��ʼ�����л���,ʵ����image����.
	protected function parse_vdcode_init(){		
		if(!$this->image){			
			//������֤��ı���ͼƬ.
			$this->image_url = empty($this->image_url) ? HEAVEN_PATH.'/Public/Images/vdcode_bg.jpg' : $this->image_url;			
			$this->session_name = empty($this->session_name) ? 'heaven_image_vdcode' : $this->session_name;
			$this->parse_image_info($this->image_url);											
		}		
		return true;
	}
	
	//��ʼ�����л���,��ȡͼƬ��ʽ��ʵ����.
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
	
	//������������.
	public function set_font_name($name, $size=false){
		if ($name){
			$this->font_name = $name;
		}		
		if ($size){
			$this->font_size = intval($size);
		}
		return $this;
	}
	
	//���������С.
	public function set_font_size($size){
		if ($size){
			$this->font_size = intval($size);
		}		
		return $this;
	}

	//���ñ���ͼƬ��ˮӡͼƬ��URL.
	public function set_image_url($url){
		if ($url){
			$this->image_url = $url;
		}		
		return $this;
	}

	//����ˮӡͼƬˮӡ������λ��.
	public function set_watermark_position($x, $y){
		if ($x){
			$this->water_x = $x;
		}
		if($y){
			$this->water_y = $y;
		}
		return $this;
	}
	
	//��������ͼƬ�Ĵ�С.
	public function set_image_size($width, $height){
		if ($width){
			$this->width = $width;
		}
		if ($height){
			$this->height = $height;
		}		
		return $this;
	}

	//����ˮӡͼƬˮӡ�����͸����.
	public function set_watermark_alpha($param){
		if ($param){
			$this->alpha = intval($param);
		}		
		return $this;
	}

	//������֤���session_name.
	public function set_session_name($name){
		if ($name){
			$this->session_name = $name;
		}
		return $this;
	}
	
	//������֤������.
	public function set_text_content($content){
		if ($content){
			$this->text_content = $content;
		}
		return $this;
	}
	
	//��������ˮӡ�����λ��
	protected function handle_watermark_font_place(){		
		if(empty($this->font_x)||empty($this->font_y)){	
			if(empty($this->text_content)){
				Core_Action::halt('You do not set the watermark text on image!');
			}
			$bbox = imagettfbbox($this->font_size, 0, $this->font_name, $this->text_content);			
			$font_w = $bbox[2] - $bbox[0] + 5; //����margin_rightΪ5px,�ش˼�5			
			$font_h = abs($bbox[7] - $bbox[1]);			
			$this->font_x = ($this->image_width > $font_w) ? $this->image_width - $font_w : 0;			
			$this->font_y = ($this->image_height > $font_h) ? $this->image_height - $font_h : 0;
		}		
		return true;
	}
	
	//��ȡ��ɫ����.
	public function set_font_color($x=false, $y=false, $z=false){	  		
	   if(is_int($x)&&is_int($y)&&is_int($z)){			
			$this->font_color = array($x, $y, $z);
		}
		else{			
			$this->font_color = array(255, 255, 255);
		}		
		return $this;
	}
	
	//�����õ�������ɫת��ΪͼƬ��Ϣ.
	protected function handle_font_color(){
		if(empty($this->font_color)){
			$this->font_color = array(255, 255, 255);
		}
		return imagecolorallocate($this->image, $this->font_color[0], $this->font_color[1], $this->font_color[2]);
	}
	
	//������֤������.
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
	
	//��֤�����弰����ĵ���.
	protected function handle_vdcode_font_place(){		
		$this->get_vdcode_content();		
		$bbox = imagettfbbox($this->font_size, 0, $this->font_name, $this->text_content);		
		$font_w = $bbox[2]-$bbox[0];		
		$font_h = abs($bbox[7]-$bbox[1]);		
		$this->font_x = ceil(($this->image_width-$font_w)/2);		
		$this->font_y = ceil(($this->image_height+$font_h)/2);		
		return true;
	}
	
	//��ʾ��֤��.
	public function vdcode_display(){
		//��ǰ��û��session_start()����ʱ.
		if (!isset($_SESSION)){
			session_start();
		}
		//��ʼ��ͼƬ��Ϣ.
		$this->parse_vdcode_init();
		//����ͼƬ��ɫ��Ϣ.		
		$font_color = $this->handle_font_color();
		$this->handle_vdcode_font_place();		
		imagettftext($this->image, $this->font_size, 0, $this->font_x, $this->font_y, $font_color, $this->font_name, $this->text_content);
		//����ʾ����֤�븳ֵ��session.
		$_SESSION[$this->session_name] = $this->text_content;
		//����headers�������ʱ.
		if(headers_sent()){
			Core_Action::halt('headers already sent'); 
		}
		//��ʾͼƬ,���ݱ���ͼƬ�ĸ�ʽ��ʾ��Ӧ��ʽ��ͼƬ.
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
	
	//���д�������ͼ�Ŀ�Ⱥ͸߶ȣ�����ͼƬԭ���Ŀ�͸ߵı��������м���
	protected function handle_image_size(){
		//��û�������ɵ�ͼƬ�Ŀ�Ⱥ͸߶�����ʱ.
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
	
	//����ͼƬ������ͼ.
	public function make_limit_image($url, $dist_name){
		if (empty($url)||empty($dist_name)){
			return false;
		}
		//ԭͼƬ����.
		$this->parse_image_info($url);
		$this->handle_image_size();
		//��ͼƬ����.
		$image_dist = imagecreatetruecolor($this->width_new, $this->height_new);
		//������ͼƬ.
		imagecopyresampled($image_dist, $this->image, 0, 0, 0, 0, $this->width_new, $this->height_new, $this->image_width, $this->image_height);
		$this->create_image($image_dist, $dist_name, $this->type);
		imagedestroy($image_dist);
		imagedestroy($this->image);	
		return true;			
	}
	//����Ŀ��ͼƬ.
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
	
	//��������ˮӡͼƬ.
	public function make_text_watermark($image_url, $dist_name){
		//����ԭͼƬ.
		$this->parse_image_info($image_url);
		//�����ɵ�ͼƬ���з���.
		$this->handle_watermark_font_place();	
		$font_color = $this->handle_font_color();
		//������ͼƬ.
		imagettftext($this->image, $this->font_size, 0, $this->font_x, $this->font_y, $font_color, $this->font_name, $this->text_content);
		$this->create_image($this->image, $dist_name, $this->type);
		imagedestroy($this->image);
		return true;
	}
	
	//��ȡˮӡͼƬ��Ϣ
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

	//����ˮӡ�����λ��,Ĭ��λ�þ�ͼƬ���½Ǳ���5����.
	protected function handle_watermark_image_place(){		
		if(empty($this->water_x)||empty($this->water_y)){			
			$this->water_x = ($this->image_width-5>$this->water_width) ? $this->image_width-$this->water_width-5 : 0;	
			$this->water_y = ($this->image_height-5>$this->water_height) ? $this->image_height-$this->water_height-5 : 0;
		}		
		return true;
	}
	
	//����ͼƬˮӡ.
	public function make_image_watermark($image_url, $dist_name){
		//����ͼƬ��Ϣ.
		$this->parse_image_info($image_url);
		//�������ɵ�ͼƬ������Ϣ����.
		$this->alpha = empty($this->alpha) ? 85 : $this->alpha;
		$this->handle_watermark_image();
		//������ͼƬ��ˮӡλ��.
		$this->handle_watermark_image_place();
		//������ͼƬ.
		imagecopymerge($this->image, $this->water_image, $this->water_x, $this->water_y, 0, 0, $this->water_width, $this->water_height, $this->alpha);
		$this->create_image($this->image, $dist_name, $this->type);
		imagedestroy($this->image);			
		return true;
	}
	
	//��������
	public function __destruct(){		
		
	}
	
	/**
     * ���ڱ���ľ�̬����,������Ҫ���ز�������ʹ��.
     * @access public
     * @param string $params �������
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