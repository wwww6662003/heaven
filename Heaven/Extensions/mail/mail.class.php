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
/**********************CONFIG文件配置说明**************
 *                config文件名:mail.php
 *
 * return array (
 *	'host' => 'smtp.heavencode.com',
 *	'username' => 'heaven',
 *	'password' => 'yourpassword',
 *	'from'=>'service@heavencode.com',
 *	'fromname'=>'heaven support',
 *	'reply'=>'service@heavencode.com',
 *	);
 *
******************************************************/

//加载PHPMailer类文件.
include(APP_ROOT.'/extensions/mail/PHPMailer/class.phpmailer.php');

class mail extends PHPMailer {

	//定义变量
	private static $instance; //用于构建类的singleton模式参数.
	
	//构造函数,用于初始化运行环境.
	public function __construct(){		
		$this->exceptions = true;
	}

	//发关邮件.
	public function send_mail($to, $subject, $body){
		$this->IsSMTP();
		$this->SMTPAuth = true;		
		$this->parse_config();		
		$this->CharSet ="utf-8";
		$this->Encoding = "base64";
		$this->AddAddress($to);
		$this->Subject = $subject;
		$this->MsgHTML($body);
		$this->IsHTML(true);		
		return $this->Send() ? true : false;
	}

	//分析处理SMTP服务配置信息.
	protected function parse_config(){		
		if(empty($this->Host)){			
			if(file_exists(APP_ROOT.'/config/mail.php')){				
				$mail_config = Controller::config_load('mail');				
				if($mail_config['host']&&$mail_config['username']&&$mail_config['password']){		
					$this->Host = $mail_config['host'];
					$this->Username = $mail_config['username'];
					$this->Password = $mail_config['password'];					
					$this->From = empty($mail_config['from']) ? $mail_config['username'].'@'.str_replace('stmp.', '', $mail_config['host']) : $mail_config['from'];
					$this->FromName = empty($mail_config['fromname']) ? $mail_config['username'] : $mail_config['fromname'];
					//设置smtp端口.
					$this->Port = empty($mail_config['port']) ? 25 : $mail_config['port'];
					//设置回复邮箱
					if(empty($mail_config['reply'])){
						$this->AddReplyTo($this->From);
					}
					else{
						$this->AddReplyTo($mail_config['reply']);
					}
					//设置SSL加密
					if($mail_config['ssl']){						
						$this->SMTPSecure='ssl';
					}    
					unset($mail_config);
				}
				else{					
					Controller::halt('The config file data is wrong, Please config reset');
				}
			}
			else{				
				Controller::halt('The config file is not exists!');
			}
		}		
		return true;
	}

	//构晰函数
	public function __destruct(){
		
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