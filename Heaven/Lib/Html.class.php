<?php
/**
 * @package HeavenMVC
 * @version 1.0 Html.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */

if(!defined('IN_HEAVEN')){
	exit;
}
class Lib_Html extends Core_Base{

	//定义变量
	private static $instance; //用于构建类的singleton模式参数
	
	//将特殊字符转化为HTML代码
	public static function encode($text)
	{
		if(is_array($text)){			
			foreach ($text as $key=>$value){				
				$text[$key] = self::encode($value);
			}			
			return $text;
		}
		else{			
			return htmlspecialchars($text);
		}	
	}
	
	//处理超级连接代码
	public static function link($text, $href='#', $options=array()){		
		if(!empty($href)){
			$options['href']=$href;
		}
		//为了SEO效果,link的title处理.
		if(empty($options['title'])&&empty($options['TITLE'])){
			$options['title'] = $text;
		}
		return self::tag('a',$options, $text);
	}

	//用于完成email的html代码的处理
	public static function email($text, $email=false, $options=array()){		
		if(empty($email)){
			$options['href']='mailto:'.$text;
		}
		else{
			$options['href']='mailto:'.$email;
		}
		return self::tag('a',$options, $text);
	}
	
	//处理图片代码
	public static function image($src, $alt='', $options=array()){		
		if(empty($src)){			
			return false;
		}		
		$options['src'] = $src;		
		if($alt){			
			$options['alt'] = $alt;
			//为了SEO效果,加入title.
			if(empty($options['title'])){
				$options['title'] = $alt;
			}
		}
		return self::tag('img', $options);
	}
	
	//处理标签代码
	public static function tag($tag, $options=array(), $content=false, $close_tag=true){		
		$option_str = '';
		//当$options不为空或类型不为数组时
		if(!empty($options)&&is_array($options)){			
			foreach ($options as $name=>$value){			
				$option_str .= ' '.$name.'="'.$value.'"';
			}
		}		
		$html = '<'.$tag.$option_str;		
		if($content==true){	
			return $close_tag ? $html.'>'.$content.'</'.$tag.'>' : $html.'>'.$content;
		}
		else{			
			return $close_tag ? $html.'/>' : $html.'>';
		}
	}
	
	//加载css文件
	public static function css_file($url,$media='')
	{
		if(!empty($media)){			
			$media=' media="'.$media.'"';
		}			
		return "<link rel=\"stylesheet\" type=\"text/css\" href=\"".self::encode($url)."\"".$media." />\r";
	}
	
	//加载JavaScript文件
	public static function script_file($url)
	{
		return "<script type=\"text/javascript\" src=\"".self::encode($url)."\"></script>\r";
	}
	
	//生成表格的HTML代码
	public static function table($content=array(), $options=array()){		
		if(empty($content)){			
			return false;
		}		
		$html = self::tag('table', $options, false, false);		
		foreach ($content as $lines){			
			if(is_array($lines)){				
				$html .= '<tr>';
				foreach ($lines as $value){					
					$html .= self::tag('td','',$value);
				}
				$html .= '</tr>';
			}
		}		
		return $html.'</table>';
	}

	//处理表单的HTML代码
	public static function form($action, $content, $method=false, $options=array()){		
		if(empty($action)){			
			return false;
		}
		$method = empty($method) ? 'post' : $method;
		$options['action'] = $action;
		$options['method'] = $method;		
		return self::tag('form', $options, $content);
	}

	//form开始HTML代码,即:将<form>代码内容补充完整.
	public static function form_start($action, $method=false, $options=array()){		
		if(empty($action)){			
			return false;
		}		
		$options['action'] = $action;
		$options['method'] = empty($method) ? 'post' : $method;		
		return self::tag('form', $options, false, false);
	}
	
	//form的HTML的结束代码
	public static function form_end(){		
		return '</form>';
	}

	//处理input代码
	public static function input($type, $options=array()){		
		if(empty($type)){			
			return false;
		}		
		$options['type'] = $type;		
		return self::tag('input', $options);
	}

	//处理text表单代码
	public static function text($options=array()){			
		return self::input('text', $options);
	}

	//处理password输入框代码
	public static function password($options=array()){			
		return self::input('password', $options);
	}

	//处理submit提交按钮代码
	public static function submit($options=array()){			
		return self::input('submit', $options);
	}
	
	//处理reset按钮代码
	public static function reset($options=array()){			
		return self::input('reset', $options);
	}

	//多行文字输入框TextArea的HTML代码处理
	public static function textarea($content=false, $options=array()){		
		$option_str = '';
		//当$options不为空或类型不为数组时
		if(!empty($options)&&is_array($options)){			
			foreach ($options as $name=>$value){			
				$option_str .= ' '.$name.'="'.$value.'"';
			}
		}		
		$html = '<textarea'.$option_str;		
		if($content==true){			
			return $html.'>'.$content.'</textarea>';
		}
		else{			
			return $html.'></textarea>';
		}
	}

	//处理下拉框SELECT的HTML代码
	public static function select($content_array, $options=array(), $selected=false){		
		if(empty($content_array)||!is_array($content_array)){			
			return false;
		}		
		$option_str = '';
		foreach ($content_array as $key=>$value){			
			if($selected==true){				
				$option_str .= ($key==$selected) ? '<option value="'.$key.'" selected="selected">'.$value.'</option>' : '<option value="'.$key.'">'.$value.'</option>';
			}
			else{				
				$option_str .= '<option value="'.$key.'">'.$value.'</option>';
			}
		}		
		return self::tag('select', $options, $option_str);
	}

	//复选框HTML代码
	public static function checkbox($content_array, $options=array(), $selected=false){		
		if(empty($content_array)||!is_array($content_array)){			
			return false;
		}		
		$html = '';
		foreach ($content_array as $key=>$value){			
			$options['value'] = $key;
			if(is_array($selected)&&!empty($selected)){				
				if(in_array($key, $selected)){					
					$options['checked'] = 'checked';
				}
				else{
					if(isset($options['checked'])){
						unset($options['checked']);
					}
				}
			}			
			$html .= '<label>'.self::input('checkbox', $options).$value.'</label>';
		}		
		return $html;
	}

	//单选框HTML代码
	public static function radio($content_array, $options=array(), $selected=0){		
		if(empty($content_array)||!is_array($content_array)){			
			return false;
		}		
		$html = '';
		foreach ($content_array as $key=>$value){			
			$options['value'] = $key;			
			if($selected==$key){				
				$options['checked'] = 'checked';
			}
			else{				
				if(isset($options['checked'])){					
					unset($options['checked']);
				}
			}			
			$html .= '<label>'.self::input('radio', $options).$value.'</label>';
		}		
		return $html;
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