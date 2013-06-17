<?php
/**
 * @package HeavenMVC
 * @version 1.0 Excel.class.php
 * @copyright heaven
 * @author heaven(277808238@qq.com)
 */

if(!defined('IN_HEAVEN')){
	exit;
}
class Lib_Excel extends Core_Base{

	//定义变量
	private static $instance; //用于构建类的singleton模式参数

	protected $xml_table;	//EXCEL表格代码.
	protected $xml_menu;	//EXCEL的标题代码.
	protected $col_num;		//EXCEL的标题的个数.
	
	/**
     * 构造函数,用于初始化运行环境.
     * @access public
     * @return mixed
     */
	public function __construct(){
		
	}

	//处理EXCEL中一行代码,相当于HTML中的tr.
	protected function handle_row($data){		
		if(empty($data)||!is_array($data)){			
			return false;
		}		
		$xml = "<Row>\n";
		foreach ($data as $key=>$value){			
			$xml .= ($key>0&&empty($data[$key-1])) ? $this->handle_index_cell($value, $key+1) : $this->handle_cell($value);
		}
		$xml .= "</Row>\n";		
		return $xml;
	}
	
	//处理EXCEL多行数据的代码.
	protected function add_rows($data){		
		if(empty($data)||!is_array($data)||!is_array($data[0])){			
			return false;
		}		
		$xml_array = array();
		foreach ($data as $row){			
			$xml_array[] = $this->handle_row($row);
		}		
		return implode('', $xml_array);
	}
	
	//配置EXCEL表格的标题
	public function set_menu($data){		
		if(empty($data)||!is_array($data)||is_array($data[0])||array_search('', $data)){			
			return false;
		}		
		$this->col_num = sizeof($data);		
		$xml = "<Row ss:AutoFitHeight=\"0\" ss:Height=\"20\">\n";
		foreach ($data as $value){			
			$type = (is_numeric($data)&&(substr($data, 0, 1)!=0)) ? 'Number' : 'String';			
			$xml .= "<Cell ss:StyleID=\"s22\"><Data ss:Type=\"".$type."\">".$value."</Data></Cell>\n";
		}
		$xml .= "</Row>\n";		
		$this->xml_menu = $xml;		
		return true;
	}
	
	//处理EXCEL表格的内容,相当于table.
	public function get_data($data){		
		$xml_rows = $this->add_rows($data);		
		if(empty($xml_rows)){			
			if(empty($this->xml_menu)){				
				return false;
			}
			else{				
				$row_num = 1;
				$col_num = $this->col_num;						
				$content = $this->xml_menu;
			}
		}
		else{
			
			if(empty($this->xml_menu)){				
				$row_num = sizeof($data);
				$col_num = sizeof($data[0]);				
				$content = $xml_rows;
			}
			else{				
				$row_num = sizeof($data)+1;
				$col_num = $this->col_num;				
				$content = $this->xml_menu.$xml_rows;
			}
		}		
		return $this->xml_table = "<Table ss:ExpandedColumnCount=\"".$col_num."\" ss:ExpandedRowCount=\"".$row_num."\" x:FullColumns=\"1\"
   x:FullRows=\"1\" ss:DefaultColumnWidth=\"60\" ss:DefaultRowHeight=\"20\">\n".$content."</Table>\n";
	}
	
	//处理EXCEL表格信息代码
	protected function parse_table(){		
		$xml_Worksheet = "<Worksheet ss:Name=\"Sheet1\">\n";		
		if(empty($this->xml_table)){			
			$xml_Worksheet .= "<Table ss:ExpandedColumnCount=\"0\" ss:ExpandedRowCount=\"0\" x:FullColumns=\"1\"
   x:FullRows=\"1\" ss:DefaultColumnWidth=\"60\" ss:DefaultRowHeight=\"20\"/>\n";
		}
		else{			
			$xml_Worksheet .= $this->xml_table;
		}
		
		$xml_Worksheet .= "<WorksheetOptions xmlns=\"urn:schemas-microsoft-com:office:excel\">
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>\n";
		return $xml_Worksheet;
	}
	
	//处理EXCEL中的表格,相当于html中的td.
	protected function handle_cell($data){		
		if(empty($data)||is_array($data)){			
			return false;
		}		
		$type = (is_numeric($data)&&(substr($data, 0, 1)!=0)) ? 'Number' : 'String';		
		return "<Cell><Data ss:Type=\"".$type."\">".$data."</Data></Cell>\n";
	}
	
	//处理EXCEL中CELL代码,当该CELL前的一个CELL内容为空时.
	protected function handle_index_cell($data, $key){		
		if(empty($data)||is_array($data)){			
			return false;
		}		
		$type = (is_numeric($data)&&(substr($data, 0, 1)!=0)) ? 'Number' : 'String';		
		return "<Cell ss:Index=\"".$key."\"><Data ss:Type=\"".$type."\">".$data."</Data></Cell>\n";
	}
	

	//分析EXCEL的文件头
	protected function parse_header(){		
		return "<?xml version=\"1.0\"?>
<?mso-application progid=\"Excel.Sheet\"?>
<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:o=\"urn:schemas-microsoft-com:office:office\"
 xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
 xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:html=\"http://www.w3.org/TR/REC-html40\">\n";
	}
	
	//分析EXCEL的内容格式
	protected function parse_top(){		
		return "<ExcelWorkbook xmlns=\"urn:schemas-microsoft-com:office:excel\">
  <WindowHeight>13500</WindowHeight>
  <WindowWidth>20340</WindowWidth>
  <WindowTopX>360</WindowTopX>
  <WindowTopY>75</WindowTopY>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>
 <Styles>
  <Style ss:ID=\"Default\" ss:Name=\"Normal\">
   <Alignment ss:Vertical=\"Center\"/>
   <Borders/>
   <Font ss:FontName=\"宋体\" x:CharSet=\"134\" ss:Size=\"12\"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID=\"s22\">
   <Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\"/>
  </Style>
 </Styles>\n";
	}
	
	//分析EXCEL的结尾
	protected function parse_footer(){		
		return "<Worksheet ss:Name=\"Sheet2\">
  <Table ss:ExpandedColumnCount=\"0\" ss:ExpandedRowCount=\"0\" x:FullColumns=\"1\"
   x:FullRows=\"1\" ss:DefaultColumnWidth=\"60\" ss:DefaultRowHeight=\"20\"/>
  <WorksheetOptions xmlns=\"urn:schemas-microsoft-com:office:excel\">
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
 <Worksheet ss:Name=\"Sheet3\">
  <Table ss:ExpandedColumnCount=\"0\" ss:ExpandedRowCount=\"0\" x:FullColumns=\"1\"
   x:FullRows=\"1\" ss:DefaultColumnWidth=\"60\" ss:DefaultRowHeight=\"20\"/>
  <WorksheetOptions xmlns=\"urn:schemas-microsoft-com:office:excel\">
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
 </Workbook>";
	}
	
	//生成EXCEL文件并下载.
	public function download($file_name){		
		if(empty($file_name)){
			return false;
		}		
		header('Pragma: no-cache');
		header("Content-Type: application/vnd.ms-excel; name=\"".$file_name.".xls\"");
        header("Content-Disposition: inline; filename=\"" . $file_name . ".xls\"");
		$excel_xml = $this->parse_header().$this->parse_top().$this->parse_table().$this->parse_footer();
		echo $excel_xml;
	}
	
	//构晰函数
	public function __destruct(){		
		exit;
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