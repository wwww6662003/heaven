<?php
// +---------------------------------------------------------------
// | Heaven Framework
// +---------------------------------------------------------------
// | Copyright (c) 2012 http://code.google.com/p/heavenmvc/ All rights reserved.
// +---------------------------------------------------------------
// | Email: wangwei(wwww6662003@163.com) QQ:86610497
// +---------------------------------------------------------------

if(!defined('IN_HEAVEN')){
	exit;
}
class swfupload extends Base{
	
	//定义变量
	private static $instance; //用于构建类的singleton模式参数
	public  $html;
	protected $server_name;	//当前执行文件的目录，绝对路径
	
	
	//构造函数.
	public function __construct(){
		
		return $this->server_name = Controller::get_base_url();
	}
	
	//在网页头添加SWF UPLOAD 的JS脚本.
	public function get_script($upload_handle_file, $size=false, $kinds=false){		
		if(empty($upload_handle_file)){	
			return false;
		}
		//开启session;
		session_start();	
		$session_id = session_id();
		//参数设置.
		$size = empty($size) ? 10 : $size;
		$kinds = empty($kinds) ? '*.*' : $kinds;
		//输出内容.
		$content = "<script type=\"text/javascript\" src=\"".$this->server_name."/extensions/swfupload/swfupload/js/swfupload.js\"></script>\r";
		$content .= "<script type=\"text/javascript\" src=\"".$this->server_name."/extensions/swfupload/swfupload/js/swfupload.queue.js\"></script>\r";
		$content .= "<script type=\"text/javascript\" src=\"".$this->server_name."/extensions/swfupload/swfupload/js/fileprogress.js\"></script>\r";
		$content .= "<script type=\"text/javascript\" src=\"".$this->server_name."/extensions/swfupload/swfupload/js/handlers.js\"></script>\r";
		$content .= "<link href=\"".$this->server_name."/extensions/swfupload/swfupload/css/swfupload_default.css\" rel=\"stylesheet\" type=\"text/css\" />\r";		
		$js_content .= <<<EOT
<script type="text/javascript">
		var swfu;
		window.onload = function(){
			var settings = {
				flash_url : "$this->server_name/extensions/swfupload/swfupload/swfupload.swf",
				upload_url: "$upload_handle_file",
				post_params: {"PHPSESSID" : "{$session_id}"},
				file_size_limit : "$size MB",
				file_types : "$kinds",
				file_types_description : "All Files",
				file_upload_limit : 10,
				file_queue_limit : 0,
				custom_settings : {
					progressTarget : "fsUploadProgress",
					cancelButtonId : "btnCancel"
				},
				debug: false,

				// Button settings
				button_image_url: "$this->server_name/extensions/swfupload/swfupload/images/XPButtonNoText_61x22.png",
				button_width: "61",
				button_height: "22",
				button_placeholder_id: "spanButtonPlaceHolder",
				button_text: '<span class="theFont">上传</span>',
				button_text_style: ".theFont { font-size: 12; }",
				button_text_left_padding: 14,
				button_text_top_padding: 1,
				
				// The event handler functions are defined in handlers.js
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				queue_complete_handler : queueComplete	// Queue plugin event
			};

			swfu = new SWFUpload(settings);
	     };
</script>\r
EOT;
		$this->html = $content.$js_content;
		return $this;
	}
	
	//上传文件所对应的html
	public function get_html(){
		
		$this->html = <<<EOT
<div class="fieldset flash" id="fsUploadProgress">
	<span class="legend">上传队列</span>
</div>
<div id="divStatus">已上传 0 个文件</div>
<div>
	<span id="spanButtonPlaceHolder"></span>
	<input id="btnCancel" type="button" value="取消全部" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 12px; height: 22px;" />
</div>
EOT;
		return $this;
	}
	
	//模板赋值
	public function assign($tag_name){		
		if(empty($tag_name)){
			return false;
		}
		return Controller::assign($tag_name, $this->html);
	}

	//输出本类信息 方便程序直接对实例化对象进行调用
	public function __toString(){		
		if($this->html){			
			return (string)$this->html;
		}
		else{			
			return (string)'This is SwfUpload Class';
		}
	}

	//构晰函数
	public function __destruct(){		
		if($this->html){
			unset($this->html);
		}
	}
	
	//用于本类的静态调用,子类需要重载才能正常使用.
    public static function getInstance(){		
		if(self::$instance == null){		
			self::$instance = new self;
		}		
		return self::$instance;
	}
}
?>