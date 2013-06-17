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
	
	//定义变量
	private static $instance; //用于构建类的singleton模式参数
	
	private $list_start; //list开始 如：1,2,3,4,5中的1
	private $list_end;   //list结束 如：1,2,3,4,5中的5
	private $style;		//pagelist的css文件.	
	public $url;	//连接网址
	public $page;	//当前页
	public $total;	//LIST总数
	public $total_pages;//页面总数
	public $num;	//每个页面显示的post数目
	public $template;	//分页显示模板.
	public $per_circle;	//list允许放页码数量，如：1.2.3.4就这4个数字，则$per_circle为4
	public $ext;	//分页程序的扩展功能开关，默认关闭
	public $center;	//list中的坐标，如： 7,8，九，10，11这里的九为当前页，在list中排第三位，则$center为3
	public $html;	//处理完毕的HTML分页代码

	/**
     +----------------------------------------------------------
     * 构造函数,用于初始化运行环境.
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
	public function __construct(){		
		//pagelist class的默认值.
		$this->ext = false;
		$this->center = 3;
		$this->num = 10;
		$this->per_circle = 10;		
		return true;
	}
	
	//获取总页数
	private function get_total_page(){		
		$total_post = trim(intval($this->total));
		$total_page = ceil($total_post/$this->num);		
		return $total_page;
	}
	
	//获取当前页数
	private function get_page_num(){		
		$page = trim(intval($this->page));		
		//当URL中没有?page=1时
		$page = empty($page) ? 1 : $page;		
		//当URL中?page=5的page参数大于总页数时
		$page = ($page > $this->total_pages) ? $this->total_pages : $page;
		return $page;
	}
	
	//返回$this->num=$num.
	public function num($num=false){		
		if(empty($num)){
			$num = 10;
		}
		$this->num = $num;
		return $this;
	}

	//返回$this->total=$total_post.
	public function total($total_post=false){		
		$this->total = $total_post;
		return $this;
	}

	//返回$this->url=$url.
	public function url($url){
		if(empty($url)){
			return false;
		}
		$this->url = $url;
		return $this;
	}

	//返回$this->page=$page.
	public function page($page=false){
		$this->page =$page;
		return $this;
	}

	//返回$this->ext=$ext.
	public function ext($ext=true){
		//将$ext转化为小写字母.
        $ext = strtolower($ext);
		$this->ext = ($ext==true) ? true : false;
		return $this;
	}

	//返回$this->center=$num.
	public function center($num){
		if(empty($num)){
			return false;
		}
		$this->center = $num;
		return $this;
	}

	//返回$this->per_circle=$num.
	public function circle($num){
		if(empty($num)){
			return false;
		}
		$this->per_circle = $num;
		return $this;
	}
	
	//分析css文件.
	public function style($name){
		switch($name){
			case 'simple':
				$this->style = 'heaven_pagelist_simple.min.css';
				//处理分页css文件的路径.
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
<li class="pagelist_ext"><a href="{$first_page_url}" target="_self">第一页</a></li>
<li class="pagelist_ext"><a href="{$page_pre_url}" target="_self">上一页</a></li>
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
<li class="pagelist_ext"><a href="{$page_next_url}" target="_self">下一页</a></li>
<li class="pagelist_ext"><a href="{$page_last_url}" target="_self">最末页</a></li>
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
<li class="pagelist_note">共{$total_num}条{$total_page}页 {$num}条/页</li>
<!-- /pagelist_ext -->
<!-- pagelist_begin -->
<li class="pagelist_ext"><a href="{$first_page_url}" target="_self">第一页</a></li>
<li class="pagelist_ext"><a href="{$page_pre_url}" target="_self">上一页</a></li>
<!-- /pagelist_begin -->
<!-- pagelist_end -->
<li class="pagelist_ext"><a href="{$page_next_url}" target="_self">下一页</a></li>
<li class="pagelist_ext"><a href="{$page_last_url}" target="_self">最末页</a></li>
<!-- /pagelist_end -->
<li>第
<select name="heaven_select_pagelist" class="select_box" onchange="self.location.href=this.options[this.selectedIndex].value">
<!-- pagelist -->
<!-- pagelist_list -->
  <option value="{$page_url}">{$page_num}</option>
<!-- /pagelist_list -->
<!-- pagelist_current -->
  <option value="{$page_num}" selected="selected">{$page_num}</option>
<!-- /pagelist_current -->
<!-- /pagelist -->
</select>页
</li>
</ul>
</div>';
				break;
			default:
				$this->style = 'heaven_pagelist_default.min.css';
		}
		return $this;
	}

    //处理list_start,list_end
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

	//设置模板
	public function template($string){
		if(empty($string)){			
			return false;
		}
		$this->template = $string;
		return $this;
	}

	//模板处理.
	private function parse_template(){		
		//判断template.
		if(empty($this->template)){
			$this->template = '
<div class="heaven_pagelist_box">
<ul>
<li class="pagelist_blank"></li>
<!-- pagelist_ext -->
<li class="pagelist_note">共{$total_num}条{$total_page}页 {$num}条/页</li>
<!-- /pagelist_ext -->
<!-- pagelist_begin -->
<li class="pagelist_ext"><a href="{$first_page_url}" target="_self">第一页</a></li>
<li class="pagelist_ext"><a href="{$page_pre_url}" target="_self">上一页</a></li>
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
<li class="pagelist_ext"><a href="{$page_next_url}" target="_self">下一页</a></li>
<li class="pagelist_ext"><a href="{$page_last_url}" target="_self">最末页</a></li>
<!-- /pagelist_end -->
</ul>
</div>';
		}
		//模板标签分析.
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
		//清空不必要的数据内存.
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
		//处理pagelist_ext.
		$pagelist_ext = ($this->ext) ? str_replace(array('{$total_num}', '{$num}', '{$total_page}'), array($this->total, $this->num, $this->total_pages), $pagelist_ext) : '';
		//处理pagelist_begin.
		$pagelist_begin = ($this->page>1&&$this->total_pages>1) ? str_replace(array('{$first_page_url}', '{$page_pre_url}'), array($this->url.'1', $this->url.($this->page-1)), $pagelist_begin) : '';
		//处理page_end.
		$pagelist_end = ($this->page!=$this->total_pages&&$this->total_pages>1) ? str_replace(array('{$page_next_url}', '{$page_last_url}'), array($this->url.($this->page+1), $this->url.$this->total_pages), $pagelist_end) : '';
		//处理pagelist_queue.
		$pagelist_queue = '';
		for ($i=$this->list_start; $i<=$this->list_end; $i++){
			$pagelist_queue .= ($this->page==$i) ? str_replace('{$page_num}', $i, $pagelist_current) : str_replace(array('{$page_url}', '{$page_num}'), array($this->url.$i, $i), $pagelist_list);
		}
		//赋值模板标签.
		$this->html = str_replace(array('{$pagelist_ext}', '{$pagelist_begin}', '{$pagelist_queue}', '{$pagelist_end}'), array($pagelist_ext, $pagelist_begin, $pagelist_queue, $pagelist_end), $this->template);
		//及时清空$this->template所占用的内存.
		unset($this->template);
		return $this->html;
	}

	//输出处理完毕的HTML，不显示
	public function output(){
		//支持长的url.
		$this->url = trim(str_replace(array("\n","\r"), '', $this->url));
		//显示数字页的列数.
		$this->per_circle = trim(intval($this->per_circle));
		//每页显示的数据信息条数.
		$this->num = trim(intval($this->num));
		//获取总页数.
		$this->total_pages = $this->get_total_page();
		//获取当前页.
		$this->page = $this->get_page_num();
		//处理list_start, list_end.
		$this->parse_list();	
		//处理pagelist template.
		$this->parse_template();		
		return $this;
	}
	
	//模板赋值
	public function assign($tag_name){		
		if(empty($tag_name)){
			return false;
		}
		return Controller::assign($tag_name, $this->html);
	}

	//加载pagelist的CSS文件
	public function addcss(){		
		//处理分页css文件的路径.
		$css_file = (heaven_URL==true) ? heaven_URL : Controller::get_base_url().str_replace('\\', '/', str_replace(APP_ROOT, '', heaven_ROOT));
		//分析css文件.
		if(empty($this->style)){
			$this->style= 'heaven_pagelist_default.css';
		}
		$css_file .= '/Public/images/'.$this->style;
		$this->html = html::css_file($css_file);
		return $this;
	}
	
	//输出本类信息 方便程序直接对实例化对象进行调用
	public function __toString(){		
		if($this->html){			
			return (string)$this->html;
		}
		else{			
			return (string)'This is pagelist Class';
		}
	}
	
	//析构函数，用于程序运行后打扫战场
	public function __destruct(){		
		//清空不必要的内存占用.
		$unset_array = array($this->html, $this->list_start, $this->list_end, $this->style, $this->template);
		foreach($unset_array as $name){
			if(isset($name)){
				unset($name);
			}
		}
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