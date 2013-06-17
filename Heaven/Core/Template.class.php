<?php
/**
 * @package HeavenMVC
 * @version 1.0 View.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */
if (! defined ( 'IN_HEAVEN' )) {
	exit ();
}
class Core_Template extends Core_Base{
	private static $instance; 
	public $left_delimiter; 
	public $right_delimiter; 
	
	/**
     * 构造函数,用于初始化运行环境.
     * @access public
     * @return mixed
     */
	public function __construct(){
		$this->left_delimiter = '<!--\s?{';
		$this->right_delimiter = '}\s?-->';
	}
	
	
	/**
	 * 处理模板文件的模板标签,即过滤 <!--{}-->
	 * @access public
	 * @return void
	 * @param string $content
	 */
	public function handle_template_content($content){
		//待处理的模板标签
		$Regexp_array = array(
		'/'.$this->left_delimiter.'\s*\$(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*\$(\w+?)\[\'(\w+?)\'\]\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*include\s+(.*?)\s*'.$this->right_delimiter.'/is',
		'/'.$this->left_delimiter.'\s*widget\s+(.*?)\s*'.$this->right_delimiter.'/is',
		'/'.$this->left_delimiter.'eval\s+(.+?)'.$this->right_delimiter.'/is',

		'/'.$this->left_delimiter.'echo\s+(.+?)\s\$(\w+?)\s(.+?)'.$this->right_delimiter.'/is',
		'/'.$this->left_delimiter.'echo\s+\$(\w+?)\[\'(\w+?)\'\]\s*'.$this->right_delimiter.'/is',
		'/'.$this->left_delimiter.'echo\s+\$(\w+?)\[\'(\w+?)\'\](.+?)'.$this->right_delimiter.'/is',
		'/'.$this->left_delimiter.'echo\s+\$(\w+?)\s(.+?)'.$this->right_delimiter.'/is',
		'/'.$this->left_delimiter.'echo\s+\$(\w+?)\s*'.$this->right_delimiter.'/is',
		'/'.$this->left_delimiter.'echo\s+(.+?)\$(\w+?)\[\'(\w+?)\'\](.+?)'.$this->right_delimiter.'/is',
		'/'.$this->left_delimiter.'echo\s+(.+?)'.$this->right_delimiter.'/is',

		'/'.$this->left_delimiter.'\s*if\s+\$(\w+?)\s*([>|<])\s*\$(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*if\s+\$(\w+?)\s*([>|<|!|=]=)\s*\$(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*if\s+\$(\w+?)\s*([>|<])\s*(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*if\s+\$(\w+?)\s*([>|<|!|=]=)\s*(\w+?)\s*'.$this->right_delimiter.'/i',

		'/'.$this->left_delimiter.'\s*else\s*if\s+\$(\w+?)\s*([>|<])\s*\$(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*else\s*if\s+\$(\w+?)\s*([>|<|!|=]=)\s*\$(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*else\s*if\s+\$(\w+?)\s*([>|<])\s*(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*else\s*if\s+\$(\w+?)\s*([>|<|!|=]=)\s*(\w+?)\s*'.$this->right_delimiter.'/i',

		'/'.$this->left_delimiter.'\s*if\s+\$(\w+?)\[\'(\w+?)\'\]\s*([>|<])\s*\$(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*if\s+\$(\w+?)\[\'(\w+?)\'\]\s*([>|<|!|=]=)\s*\$(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*if\s+\$(\w+?)\[\'(\w+?)\'\]\s*([>|<])\s*(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*if\s+\$(\w+?)\[\'(\w+?)\'\]\s*([>|<|!|=]=)\s*(\w+?)\s*'.$this->right_delimiter.'/i',

		'/'.$this->left_delimiter.'\s*else\s*if\s+\$(\w+?)\[\'(\w+?)\'\]\s*([>|<])\s*\$(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*else\s*if\s+\$(\w+?)\[\'(\w+?)\'\]\s*([>|<|!|=]=)\s*\$(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*else\s*if\s+\$(\w+?)\[\'(\w+?)\'\]\s*([>|<])\s*(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*else\s*if\s+\$(\w+?)\[\'(\w+?)\'\]\s*([>|<|!|=]=)\s*(\w+?)\s*'.$this->right_delimiter.'/i',

		'/'.$this->left_delimiter.'\s*else\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*\/if\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*loop\s+\$(\w+?)\s+\$(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*loop\s+\$(\w+?)\s+\$(\w+?)\s?=>\s?\$(\w+?)\s*'.$this->right_delimiter.'/i',
		'/'.$this->left_delimiter.'\s*\/loop\s*'.$this->right_delimiter.'/i',
		
		//Heaven add
		/*'/'.'\s*{\s*\$([\w_]+?)\s*}\s*'.'/i',*/
		);
		//被替换的编译语句.
		$Replace_array = array(
		"<?php echo \$this->tpl_vars['\\1']; ?>",
		"<?php echo \$this->tpl_vars['\\1']['\\2']; ?>",
		"<?php \$this->layout('\\1'); ?>",
		"<?php Widget::render('\\1'); ?>",
		"<?php \\1 ?>",
		
		"<?php echo \\1\$this->tpl_vars['\\2']\\3; ?>",
		"<?php echo \$this->tpl_vars['\\1']['\\2']; ?>",
		"<?php echo \$this->tpl_vars['\\1']['\\2']\\3; ?>",	
		"<?php echo \$this->tpl_vars['\\1']\\2; ?>",
		"<?php echo \$this->tpl_vars['\\1']; ?>",
		"<?php echo \\1\$this->tpl_vars['\\2']['\\3']\\4; ?>",
		"<?php echo \\1; ?>",
			
		"<?php\r\nif(\$this->tpl_vars['\\1']\\2\$this->tpl_vars['\\3']){\r\n?>",
		"<?php\r\nif(\$this->tpl_vars['\\1']\\2\$this->tpl_vars['\\3']){\r\n?>",
		"<?php\r\nif(\$this->tpl_vars['\\1']\\2\\3){\r\n?>",
		"<?php\r\nif(\$this->tpl_vars['\\1']\\2\\3){\r\n?>",

		"<?php\r\n}\r\nelse if(\$this->tpl_vars['\\1']\\2\$this->tpl_vars['\\3']){\r\n?>",
		"<?php\r\n}\r\nelse if(\$this->tpl_vars['\\1']\\2\$this->tpl_vars['\\3']){\r\n?>",
		"<?php\r\n}\r\nelse if(\$this->tpl_vars['\\1']\\2\\3){\r\n?>",
		"<?php\r\n}\r\nelse if(\$this->tpl_vars['\\1']\\2\\3){\r\n?>",

		"<?php\r\nif(\$this->tpl_vars['\\1']['\\2']\\3\$this->tpl_vars['\\4']){\r\n?>",
		"<?php\r\nif(\$this->tpl_vars['\\1']['\\2']\\3\$this->tpl_vars['\\4']){\r\n?>",
		"<?php\r\nif(\$this->tpl_vars['\\1']['\\2']\\3\\4){\r\n?>",
		"<?php\r\nif(\$this->tpl_vars['\\1']['\\2']\\3\\4){\r\n?>",

		"<?php\r\n}\r\nelse if(\$this->tpl_vars['\\1']['\\2']\\3\$this->tpl_vars['\\4']){\r\n?>",
		"<?php\r\n}\r\nelse if(\$this->tpl_vars['\\1']['\\2']\\3\$this->tpl_vars['\\4']){\r\n?>",
		"<?php\r\n}\r\nelse if(\$this->tpl_vars['\\1']['\\2']\\3\\4){\r\n?>",
		"<?php\r\n}\r\nelse if(\$this->tpl_vars['\\1']['\\2']\\3\\4){\r\n?>",

		"<?php\r\n}\r\nelse{\r\n?>",
		"<?php\r\n}\r\n?>",
		"<?php\r\nif(is_array(\$this->tpl_vars['\\1'])){\r\n\tforeach(\$this->tpl_vars['\\1'] as \$this->tpl_vars['\\2']){\r\n?>",
		"<?php\r\nif(is_array(\$this->tpl_vars['\\1'])){\r\n\tforeach(\$this->tpl_vars['\\1'] as \$this->tpl_vars['\\2']=>\$this->tpl_vars['\\3']){\r\n?>",
		"<?php\r\n\t}\r\n}\r\n?>",
		//Heaven add
		/*"<?php echo common_config('\\1');?>",*/
		);
		return preg_replace($Regexp_array,$Replace_array,$content);
	}
	/**
	 * 实例化本类
	 * @access public 
	 * @return unknown
	 */
 public static function getInstance(){		
		if(self::$instance == null){		
			self::$instance = new self;
		}		
		return self::$instance;
	}
	
}
?>