<?php
/**
 * @package HeavenMVC
 * @version 1.0 Pagelist.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */

if(!defined('IN_HEAVEN')){
	exit;
}
class Lib_Pagelist extends Core_Base{
	
	//�������
	private static $instance; //���ڹ������singletonģʽ����
	
	private $list_start; //list��ʼ �磺1,2,3,4,5�е�1
	private $list_end;   //list���� �磺1,2,3,4,5�е�5
	private $style;		//pagelist��css�ļ�.	
	public $url;	//������ַ
	public $page;	//��ǰҳ
	public $total;	//LIST����
	public $total_pages;//ҳ������
	public $num;	//ÿ��ҳ����ʾ��post��Ŀ
	public $template;	//��ҳ��ʾģ��.
	public $per_circle;	//list�����ҳ���������磺1.2.3.4����4�����֣���$per_circleΪ4
	public $ext;	//��ҳ�������չ���ܿ��أ�Ĭ�Ϲر�
	public $center;	//list�е����꣬�磺 7,8���ţ�10��11����ľ�Ϊ��ǰҳ����list���ŵ���λ����$centerΪ3
	public $html;	//������ϵ�HTML��ҳ����

	/**
     +----------------------------------------------------------
     * ���캯��,���ڳ�ʼ�����л���.
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
	public function __construct(){		
		//pagelist class��Ĭ��ֵ.
		$this->ext = false;
		$this->center = 3;
		$this->num = 10;
		$this->per_circle = 10;		
		return true;
	}
	
	//��ȡ��ҳ��
	private function get_total_page(){		
		$total_post = trim(intval($this->total));
		$total_page = ceil($total_post/$this->num);		
		return $total_page;
	}
	
	//��ȡ��ǰҳ��
	private function get_page_num(){		
		$page = trim(intval($this->page));		
		//��URL��û��?page=1ʱ
		$page = empty($page) ? 1 : $page;		
		//��URL��?page=5��page����������ҳ��ʱ
		$page = ($page > $this->total_pages) ? $this->total_pages : $page;
		return $page;
	}
	
	//����$this->num=$num.
	public function num($num=false){		
		if(empty($num)){
			$num = 10;
		}
		$this->num = $num;
		return $this;
	}

	//����$this->total=$total_post.
	public function total($total_post=false){		
		$this->total = $total_post;
		return $this;
	}

	//����$this->url=$url.
	public function url($url){
		if(empty($url)){
			return false;
		}
		$this->url = $url;
		return $this;
	}

	//����$this->page=$page.
	public function page($page=false){
		$this->page =$page;
		return $this;
	}

	//����$this->ext=$ext.
	public function ext($ext=true){
		//��$extת��ΪСд��ĸ.
        $ext = strtolower($ext);
		$this->ext = ($ext==true) ? true : false;
		return $this;
	}

	//����$this->center=$num.
	public function center($num){
		if(empty($num)){
			return false;
		}
		$this->center = $num;
		return $this;
	}

	//����$this->per_circle=$num.
	public function circle($num){
		if(empty($num)){
			return false;
		}
		$this->per_circle = $num;
		return $this;
	}
	
	//����css�ļ�.
	public function style($name){
		switch($name){
			case 'simple':
				$this->style = 'heaven_pagelist_simple.min.css';
				//�����ҳcss�ļ���·��.
				$css_dir = (heaven_URL==true) ? heaven_URL : Controller::get_base_url().str_replace('\\', '/', str_replace(APP_ROOT, '', heaven_ROOT));
				$this->template = '
<div class="heaven_pagelist_box">
<ul>
<!-- pagelist_begin -->
<li class="pagelist_ext"><a href="{$first_page_url}" target="_self"><img src="'.$css_dir.'/Public/images/pre_02.gif" width="17" height="11" /></a></li>
<li class="pagelist_ext"><a href="{$page_pre_url}" target="_self"><img src="'.$css_dir.'/Public/images/s_pre.gif" width="16" height="11" /></a></li>
<!-- /pagelist_begin -->
<!-- pagelist -->
<!-- pagelist_list -->
<li><a href="{$page_url}" target="_self">{$page_num}</a></li>
<!-- /pagelist_list -->
<!-- pagelist_current -->
<li class="pagelist_current">{$page_num}</li>
<!-- /pagelist_current -->
<!-- /pagelist -->
<!-- pagelist_end -->
<li class="pagelist_ext"><a href="{$page_next_url}" target="_self"><img src="'.$css_dir.'/Public/images/s_next.gif" width="14" height="11" /></a></li>
<li class="pagelist_ext"><a href="{$page_last_url}" target="_self"><img src="'.$css_dir.'/Public/images/next_02.gif" width="15" height="11" /></a></li>
<!-- /pagelist_end -->
</ul>
</div>';
				break;
			case 'classic':
				$this->style = 'heaven_pagelist_classic.min.css';
				$this->template = '
<div class="heaven_pagelist_box">
<ul>
<!-- pagelist_begin -->
<li class="pagelist_ext"><a href="{$first_page_url}" target="_self">��һҳ</a></li>
<li class="pagelist_ext"><a href="{$page_pre_url}" target="_self">��һҳ</a></li>
<!-- /pagelist_begin -->
<!-- pagelist -->
<!-- pagelist_list -->
<li><a href="{$page_url}" target="_self">{$page_num}</a></li>
<!-- /pagelist_list -->
<!-- pagelist_current -->
<li class="pagelist_current">{$page_num}</li>
<!-- /pagelist_current -->
<!-- /pagelist -->
<!-- pagelist_end -->
<li class="pagelist_ext"><a href="{$page_next_url}" target="_self">��һҳ</a></li>
<li class="pagelist_ext"><a href="{$page_last_url}" target="_self">��ĩҳ</a></li>
<!-- /pagelist_end -->
</ul>
</div>';
				break;
			case 'select':
				$this->style = 'heaven_pagelist_select.min.css';
				$this->template = '
<div class="heaven_pagelist_box">
<ul>
<li class="pagelist_blank"></li>
<!-- pagelist_ext -->
<li class="pagelist_note">��{$total_num}��{$total_page}ҳ {$num}��/ҳ</li>
<!-- /pagelist_ext -->
<!-- pagelist_begin -->
<li class="pagelist_ext"><a href="{$first_page_url}" target="_self">��һҳ</a></li>
<li class="pagelist_ext"><a href="{$page_pre_url}" target="_self">��һҳ</a></li>
<!-- /pagelist_begin -->
<!-- pagelist_end -->
<li class="pagelist_ext"><a href="{$page_next_url}" target="_self">��һҳ</a></li>
<li class="pagelist_ext"><a href="{$page_last_url}" target="_self">��ĩҳ</a></li>
<!-- /pagelist_end -->
<li>��
<select name="heaven_select_pagelist" class="select_box" onchange="self.location.href=this.options[this.selectedIndex].value">
<!-- pagelist -->
<!-- pagelist_list -->
  <option value="{$page_url}">{$page_num}</option>
<!-- /pagelist_list -->
<!-- pagelist_current -->
  <option value="{$page_num}" selected="selected">{$page_num}</option>
<!-- /pagelist_current -->
<!-- /pagelist -->
</select>ҳ
</li>
</ul>
</div>';
				break;
			default:
				$this->style = 'heaven_pagelist_default.min.css';
		}
		return $this;
	}

    //����list_start,list_end
	private function parse_list(){		 
		if(empty($this->total_pages)||empty($this->page)){		
			return false;
		}	 
		if($this->total_pages>$this->per_circle){				
			if($this->page+$this->per_circle>=$this->total_pages+$this->center){					
				$this->list_start = $this->total_pages-$this->per_circle+1;
				$this->list_end = $this->total_pages;
			}
			else{					
				$this->list_start = ($this->page>$this->center) ? $this->page-$this->center+1 : 1;
				$this->list_end = ($this->page>$this->center) ? $this->page+$this->per_circle-$this->center : $this->per_circle;
			}				
		}
		else{				
			$this->list_start = 1;
			$this->list_end = $this->total_pages;
		}		
		return true;
	}

	//����ģ��
	public function template($string){
		if(empty($string)){			
			return false;
		}
		$this->template = $string;
		return $this;
	}

	//ģ�崦��.
	private function parse_template(){		
		//�ж�template.
		if(empty($this->template)){
			$this->template = '
<div class="heaven_pagelist_box">
<ul>
<li class="pagelist_blank"></li>
<!-- pagelist_ext -->
<li class="pagelist_note">��{$total_num}��{$total_page}ҳ {$num}��/ҳ</li>
<!-- /pagelist_ext -->
<!-- pagelist_begin -->
<li class="pagelist_ext"><a href="{$first_page_url}" target="_self">��һҳ</a></li>
<li class="pagelist_ext"><a href="{$page_pre_url}" target="_self">��һҳ</a></li>
<!-- /pagelist_begin -->
<!-- pagelist -->
<!-- pagelist_list -->
<li><a href="{$page_url}" target="_self">{$page_num}</a></li>
<!-- /pagelist_list -->
<!-- pagelist_current -->
<li class="pagelist_current">{$page_num}</li>
<!-- /pagelist_current -->
<!-- /pagelist -->
<!-- pagelist_end -->
<li class="pagelist_ext"><a href="{$page_next_url}" target="_self">��һҳ</a></li>
<li class="pagelist_ext"><a href="{$page_last_url}" target="_self">��ĩҳ</a></li>
<!-- /pagelist_end -->
</ul>
</div>';
		}
		//ģ���ǩ����.
		preg_match('#<!-- pagelist_ext -->(.+?)<!-- /pagelist_ext -->#is', $this->template, $matches_ext);
		preg_match('#<!-- pagelist_begin -->(.+?)<!-- \/pagelist_begin -->#is', $this->template, $matches_begin);
		preg_match('#<!-- pagelist_list -->(.+?)<!-- /pagelist_list -->#is', $this->template, $matches_list);
		preg_match('#<!-- pagelist_current -->(.+?)<!-- /pagelist_current -->#is', $this->template, $matches_current);
		preg_match('#<!-- pagelist_end -->(.+?)<!-- /pagelist_end -->#is', $this->template, $matches_end);
		$pagelist_ext = trim($matches_ext[1]);
		$pagelist_begin = trim($matches_begin[1]);
		$pagelist_list = trim($matches_list[1]);
		$pagelist_current = trim($matches_current[1]);
		$pagelist_end = trim($matches_end[1]);
		//��ղ���Ҫ�������ڴ�.
		$unset_array = array($matches_ext, $matches_begin, $matches_list, $matches_current, $matches_end);
		foreach($unset_array as $name){			
			if(isset($name)){
				unset($name);
			}
		}
		$Regexp_array = array(
			'#<!-- pagelist_ext -->(.+?)<!-- /pagelist_ext -->#is',
			'#<!-- pagelist_begin -->(.+?)<!-- \/pagelist_begin -->#is',
			'#<!-- pagelist -->(.+?)<!-- /pagelist -->#is',
			'#<!-- pagelist_end -->(.+?)<!-- /pagelist_end -->#is',
		);
		$Replace_array = array(
			'{$pagelist_ext}',
			'{$pagelist_begin}',
			'{$pagelist_queue}',
			'{$pagelist_end}',
		);
		$this->template=preg_replace($Regexp_array,$Replace_array,$this->template);
		//����pagelist_ext.
		$pagelist_ext = ($this->ext) ? str_replace(array('{$total_num}', '{$num}', '{$total_page}'), array($this->total, $this->num, $this->total_pages), $pagelist_ext) : '';
		//����pagelist_begin.
		$pagelist_begin = ($this->page>1&&$this->total_pages>1) ? str_replace(array('{$first_page_url}', '{$page_pre_url}'), array($this->url.'1', $this->url.($this->page-1)), $pagelist_begin) : '';
		//����page_end.
		$pagelist_end = ($this->page!=$this->total_pages&&$this->total_pages>1) ? str_replace(array('{$page_next_url}', '{$page_last_url}'), array($this->url.($this->page+1), $this->url.$this->total_pages), $pagelist_end) : '';
		//����pagelist_queue.
		$pagelist_queue = '';
		for ($i=$this->list_start; $i<=$this->list_end; $i++){
			$pagelist_queue .= ($this->page==$i) ? str_replace('{$page_num}', $i, $pagelist_current) : str_replace(array('{$page_url}', '{$page_num}'), array($this->url.$i, $i), $pagelist_list);
		}
		//��ֵģ���ǩ.
		$this->html = str_replace(array('{$pagelist_ext}', '{$pagelist_begin}', '{$pagelist_queue}', '{$pagelist_end}'), array($pagelist_ext, $pagelist_begin, $pagelist_queue, $pagelist_end), $this->template);
		//��ʱ���$this->template��ռ�õ��ڴ�.
		unset($this->template);
		return $this->html;
	}

	//���������ϵ�HTML������ʾ
	public function output(){
		//֧�ֳ���url.
		$this->url = trim(str_replace(array("\n","\r"), '', $this->url));
		//��ʾ����ҳ������.
		$this->per_circle = trim(intval($this->per_circle));
		//ÿҳ��ʾ��������Ϣ����.
		$this->num = trim(intval($this->num));
		//��ȡ��ҳ��.
		$this->total_pages = $this->get_total_page();
		//��ȡ��ǰҳ.
		$this->page = $this->get_page_num();
		//����list_start, list_end.
		$this->parse_list();	
		//����pagelist template.
		$this->parse_template();		
		return $this;
	}
	
	//ģ�帳ֵ
	public function assign($tag_name){		
		if(empty($tag_name)){
			return false;
		}
		return Controller::assign($tag_name, $this->html);
	}

	//����pagelist��CSS�ļ�
	public function addcss(){		
		//�����ҳcss�ļ���·��.
		$css_file = (heaven_URL==true) ? heaven_URL : Controller::get_base_url().str_replace('\\', '/', str_replace(APP_ROOT, '', heaven_ROOT));
		//����css�ļ�.
		if(empty($this->style)){
			$this->style= 'heaven_pagelist_default.css';
		}
		$css_file .= '/Public/images/'.$this->style;
		$this->html = html::css_file($css_file);
		return $this;
	}
	
	//���������Ϣ �������ֱ�Ӷ�ʵ����������е���
	public function __toString(){		
		if($this->html){			
			return (string)$this->html;
		}
		else{			
			return (string)'This is pagelist Class';
		}
	}
	
	//�������������ڳ������к��ɨս��
	public function __destruct(){		
		//��ղ���Ҫ���ڴ�ռ��.
		$unset_array = array($this->html, $this->list_start, $this->list_end, $this->style, $this->template);
		foreach($unset_array as $name){
			if(isset($name)){
				unset($name);
			}
		}
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