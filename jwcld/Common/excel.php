<?php
/* Created: 2013-9-29
*  Author: XiaoFeng
*  Group: IREG
*/
	/**
	 * 
	 * excel相关操作
	 * @author XiaFeng
	 *
	 */
	class excel{
		
		/**
		 * 
		 * 根据数据导出excel表
		 * @param array $dataArr 数据列表
		 * @param array $nameArr 数据属性列表
		 * @param array $attrArr 数组下表列表
		 * @param String $fileName excel文件名
		 */
		public function exportExcel($dataArr,$nameArr,$attrArr,$fileName,$title,$type){
			
			vendor('PHPExcel.PHPExcel');
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator($author)
										 ->setLastModifiedBy($author)
										 ->setTitle($title)
										 ->setSubject($title)
										 ->setDescription("Big Data & IoT")
										 ->setKeywords("PHPExcel")
										 ->setCategory("infomation");
			$NameHeight = count($nameArr,0);//看名字是多少行
			$length1 = count($nameArr[$NameHeight-1],1);
			$length2 = count($dataArr,0);
			$author = 'IREG';
			$title = 'IREG';
			$n = 0;
			$Letter = array("A","B","C","D","E","F","G","H","I","J","K","L","M",
							"N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
							"AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK",
							"AL","AM","AO","AP","AQ","AR","AS","AT","AU","AV","AW",
							"AX","AY","AZ");
			$length3 = count($Letter,0);
			//生成excel首行标示信息
			//以下是根据督导项目实际用到的信息专门进行修改的
		    $objPHPExcel->getActiveSheet()->mergeCells($Letter[0].'1:'.$Letter[$length1-1].'1');      // 首行信息合并
	
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$nameArr[0][0]);
		    //标题居中
		    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		    //表头居中
		    $objPHPExcel->getActiveSheet()->getStyle('A2:'.$Letter[$length1-1].$NameHeight)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		    //$objPHPExcel->getActiveSheet()->getStyle('A2:'.$Letter[$length1-1].$NameHeight)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		    
		    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
		    $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);
		    $objPHPExcel->getActiveSheet()->getStyle('A2:AS2')->getAlignment()->setWrapText(TRUE);
		    $objPHPExcel->getActiveSheet()->getStyle('A3:AS3')->getAlignment()->setWrapText(TRUE);
		    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
		    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15);

		    if($type==1){ //case 2&4 领导听课次数统计总表
			    
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[0].'2:'.$Letter[0].'3');
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[1].'2:'.$Letter[1].'3');
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[2].'2:'.$Letter[2].'3');
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[3].'2:'.$Letter[5].'2');  
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[6].'2:'.$Letter[8].'2');
				for($i = 0; $i < 2; $i++){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i*3+3].'2',$nameArr[1][$i]);
				}
				for($i = 0; $i < 3; $i++){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i].'2',$nameArr[$NameHeight-1][$i]);
					}
				for($i = 3; $i < $length1; $i++){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i].$NameHeight,$nameArr[$NameHeight-1][$i]);
					}
			}
			elseif($type==2){ //case 3&5 领导听课评价统计总表
				$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(11);
				$objPHPExcel->getActiveSheet()->mergeCells($Letter[0].'2:'.$Letter[0].'3');
				$objPHPExcel->getActiveSheet()->mergeCells($Letter[1].'2:'.$Letter[1].'3');
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[2].'2:'.$Letter[8].'2');  
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[9].'2:'.$Letter[15].'2');
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[16].'2:'.$Letter[22].'2');
				for($i = 0; $i < 3; $i++){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i*7+2].'2',$nameArr[1][$i]);
				}
				for($i = 0; $i < 2; $i++){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i].'2',$nameArr[$NameHeight-1][$i]);
				}
				for($i = 2; $i < $length1; $i++){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i].$NameHeight,$nameArr[$NameHeight-1][$i]);
					}
			}
			//20170409添加导出各单位领导听课评价结果统计表格式 begin
			elseif($type==3){ //case 6 各单位领导听课评价结果统计表
				$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(11);
				for($k = 0; $k < 6; $k++){
					$objPHPExcel->getActiveSheet()->mergeCells($Letter[$k].'2:'.$Letter[$k].'4');
				}
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[6].'2:'.$Letter[12].'2');  
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[13].'2:'.$Letter[19].'2');
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[20].'2:'.$Letter[22].'2');
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[7].'3:'.$Letter[12].'3');
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[14].'3:'.$Letter[19].'3'); 
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[6].'3:'.$Letter[6].'4');
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[13].'3:'.$Letter[13].'4');
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[20].'3:'.$Letter[20].'4');
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[21].'3:'.$Letter[21].'4');
			    $objPHPExcel->getActiveSheet()->mergeCells($Letter[22].'3:'.$Letter[22].'4');
			    //teaid工作证号所在列 设置文本格式 避免数字乱码
			    $objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				
				for($i = 0; $i < 6; $i++){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i].'2',$nameArr[3][$i]);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i+7].$NameHeight,$nameArr[$NameHeight-1][$i+7]);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i+14].$NameHeight,$nameArr[$NameHeight-1][$i+14]);
				}
				for($i = 0; $i < 3; $i++){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i*7+6].'2',$nameArr[1][$i]);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i+20].'3',$nameArr[$NameHeight-1][$i+20]);
				}
				for($i = 0; $i < 2; $i++){
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i*7+7].'3',$nameArr[2][$i]);
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i*7+6].'3',$nameArr[$NameHeight-1][$i*7+6]);
					}
			}
			//20170409添加导出各单位领导听课评价结果统计表格式 end
			
			else{ //普通格式单行表头无合并单元格
				$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(13);
				for($i = 0; $i < $length1; $i++){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$i].$NameHeight,$nameArr[$NameHeight-1][$i]);
				}
			}

			//生成每一行数据
			for($i = 0;  $i < $length2; $i++){
				$n = $i + $NameHeight + 1 ;
				for($j = 1; $j < $length1; $j++){
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($Letter[$j].$n,$dataArr[$i][$attrArr[$j]]);
				}
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$n, $i+1);
			}
			$objPHPExcel->getActiveSheet()->setTitle($title);
			$objPHPExcel->setActiveSheetIndex(0);
			spl_autoload_register(array('Think','autoload'));
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
			$objWriter->save('php://output');
			exit;
		}

		/**
		*根据具体的数据库表将上传的文件导入到数据库表中
		*@param String $file 上传到服务器之后的文件路径
		*@param String $table 需要导入的数据库表
		*
		*/
		public function importExcel($file,$table){
			vendor('PHPExcel.PHPExcel');
			$objPHPExcel = PHPExcel_IOFactory::load($file);
			$sheet = $objPHPExcel->getSheet(0);
			$arrExcel = $sheet->toArray();
			$rows = $sheet->getHighestRow();	//取得总行数
			$cols = $sheet->getHighestColumn();	//取得总列数
			array_shift($arrExcel);	//删除第一行

			//查询数据库的字段
			$m = M($table);
			$fieldarr = $m->getDbFields();
			array_shift($fieldarr);	//删除自增的ID
			//循环给数据字段赋值
			foreach ($arrExcel as $v) {
				$fields[] = array_combine($fieldarr, $v);	//将excel的一行数据赋值给表的字段
			}
			$isSuccess = $m->addAll($fields);
			if(!$isSuccess){
				Log::write($table."导入数据失败",ERR);
				return false;
			}else{
				Log::write($table."导入数据成功",INFO);
				return true;
			}
		}
		//返回excel数据的行数、列数以及具体数据(删除第一行)
		public function returnExcelData($file){
			vendor('PHPExcel.PHPExcel');
			$objPHPExcel = PHPExcel_IOFactory::load($file);
			$sheet = $objPHPExcel->getSheet(0);
			$arrExcel = $sheet->toArray();
			$rows = $sheet->getHighestRow();	//取得总行数
			$cols = $sheet->getHighestColumn();	//取得总列数
			array_shift($arrExcel);	//删除第一行
			array_shift($arrExcel); //删除第二行
			$data = array();
			$data[0]["rows"] = $rows-2;	//已删除一行
			$data[1]["cols"] = $cols;
			$data[2]["data"] = $arrExcel;
			return $data;
		}
		
		//返回excel数据的行数、列数以及具体数据
		public function returnData($file){
			vendor('PHPExcel.PHPExcel');
			$objPHPExcel = PHPExcel_IOFactory::load($file);
			$sheet = $objPHPExcel->getSheet(0);
			$arrExcel = $sheet->toArray();
			$rows = $sheet->getHighestRow();	//取得总行数
			$cols = $sheet->getHighestColumn();	//取得总列数
			
			$data = array();
			$data[0]["rows"] = $rows;
			$data[1]["cols"] = $cols;
			$data[2]["data"] = $arrExcel;
			return $data;
		}
	}

?>