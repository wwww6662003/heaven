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

	//�������
	private static $instance; //���ڹ������singletonģʽ����

	protected $xml_table;	//EXCEL������.
	protected $xml_menu;	//EXCEL�ı������.
	protected $col_num;		//EXCEL�ı���ĸ���.
	
	/**
     * ���캯��,���ڳ�ʼ�����л���.
     * @access public
     * @return mixed
     */
	public function __construct(){
		
	}

	//����EXCEL��һ�д���,�൱��HTML�е�tr.
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
	
	//����EXCEL�������ݵĴ���.
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
	
	//����EXCEL���ı���
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
	
	//����EXCEL��������,�൱��table.
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
	
	//����EXCEL�����Ϣ����
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
	
	//����EXCEL�еı��,�൱��html�е�td.
	protected function handle_cell($data){		
		if(empty($data)||is_array($data)){			
			return false;
		}		
		$type = (is_numeric($data)&&(substr($data, 0, 1)!=0)) ? 'Number' : 'String';		
		return "<Cell><Data ss:Type=\"".$type."\">".$data."</Data></Cell>\n";
	}
	
	//����EXCEL��CELL����,����CELLǰ��һ��CELL����Ϊ��ʱ.
	protected function handle_index_cell($data, $key){		
		if(empty($data)||is_array($data)){			
			return false;
		}		
		$type = (is_numeric($data)&&(substr($data, 0, 1)!=0)) ? 'Number' : 'String';		
		return "<Cell ss:Index=\"".$key."\"><Data ss:Type=\"".$type."\">".$data."</Data></Cell>\n";
	}
	

	//����EXCEL���ļ�ͷ
	protected function parse_header(){		
		return "<?xml version=\"1.0\"?>
<?mso-application progid=\"Excel.Sheet\"?>
<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:o=\"urn:schemas-microsoft-com:office:office\"
 xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
 xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:html=\"http://www.w3.org/TR/REC-html40\">\n";
	}
	
	//����EXCEL�����ݸ�ʽ
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
   <Font ss:FontName=\"����\" x:CharSet=\"134\" ss:Size=\"12\"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID=\"s22\">
   <Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\"/>
  </Style>
 </Styles>\n";
	}
	
	//����EXCEL�Ľ�β
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
	
	//����EXCEL�ļ�������.
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
	
	//��������
	public function __destruct(){		
		exit;
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