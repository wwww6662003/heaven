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

	//�������
	private static $instance; //���ڹ������singletonģʽ����
	
	//�������ַ�ת��ΪHTML����
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
	
	//���������Ӵ���
	public static function link($text, $href='#', $options=array()){		
		if(!empty($href)){
			$options['href']=$href;
		}
		//Ϊ��SEOЧ��,link��title����.
		if(empty($options['title'])&&empty($options['TITLE'])){
			$options['title'] = $text;
		}
		return self::tag('a',$options, $text);
	}

	//�������email��html����Ĵ���
	public static function email($text, $email=false, $options=array()){		
		if(empty($email)){
			$options['href']='mailto:'.$text;
		}
		else{
			$options['href']='mailto:'.$email;
		}
		return self::tag('a',$options, $text);
	}
	
	//����ͼƬ����
	public static function image($src, $alt='', $options=array()){		
		if(empty($src)){			
			return false;
		}		
		$options['src'] = $src;		
		if($alt){			
			$options['alt'] = $alt;
			//Ϊ��SEOЧ��,����title.
			if(empty($options['title'])){
				$options['title'] = $alt;
			}
		}
		return self::tag('img', $options);
	}
	
	//�����ǩ����
	public static function tag($tag, $options=array(), $content=false, $close_tag=true){		
		$option_str = '';
		//��$options��Ϊ�ջ����Ͳ�Ϊ����ʱ
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
	
	//����css�ļ�
	public static function css_file($url,$media='')
	{
		if(!empty($media)){			
			$media=' media="'.$media.'"';
		}			
		return "<link rel=\"stylesheet\" type=\"text/css\" href=\"".self::encode($url)."\"".$media." />\r";
	}
	
	//����JavaScript�ļ�
	public static function script_file($url)
	{
		return "<script type=\"text/javascript\" src=\"".self::encode($url)."\"></script>\r";
	}
	
	//���ɱ���HTML����
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

	//�������HTML����
	public static function form($action, $content, $method=false, $options=array()){		
		if(empty($action)){			
			return false;
		}
		$method = empty($method) ? 'post' : $method;
		$options['action'] = $action;
		$options['method'] = $method;		
		return self::tag('form', $options, $content);
	}

	//form��ʼHTML����,��:��<form>�������ݲ�������.
	public static function form_start($action, $method=false, $options=array()){		
		if(empty($action)){			
			return false;
		}		
		$options['action'] = $action;
		$options['method'] = empty($method) ? 'post' : $method;		
		return self::tag('form', $options, false, false);
	}
	
	//form��HTML�Ľ�������
	public static function form_end(){		
		return '</form>';
	}

	//����input����
	public static function input($type, $options=array()){		
		if(empty($type)){			
			return false;
		}		
		$options['type'] = $type;		
		return self::tag('input', $options);
	}

	//����text������
	public static function text($options=array()){			
		return self::input('text', $options);
	}

	//����password��������
	public static function password($options=array()){			
		return self::input('password', $options);
	}

	//����submit�ύ��ť����
	public static function submit($options=array()){			
		return self::input('submit', $options);
	}
	
	//����reset��ť����
	public static function reset($options=array()){			
		return self::input('reset', $options);
	}

	//�������������TextArea��HTML���봦��
	public static function textarea($content=false, $options=array()){		
		$option_str = '';
		//��$options��Ϊ�ջ����Ͳ�Ϊ����ʱ
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

	//����������SELECT��HTML����
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

	//��ѡ��HTML����
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

	//��ѡ��HTML����
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