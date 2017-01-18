<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tableHelper
 *
 * @author Georges
 */
class tableHelper {
	//put your code here
	
	private $table;
	private $row;
	private $tableID;
	
	public function __construct() {
		$this->table = "";
		$this->rowNum = 0;
	}
			
	public function initTable($params){
		$table = "";
		$table .= "<table ";
		$this->tableID = $params["id"];
		foreach ($params as $key => $value) {
			$table .= "'".$key."' = '".$value."' ";
		}
		$table .= "  border='1' cellpadding='0' width='100%'><tbody>";
		return $table;
	}
	
	public function populateRow($params,$class,$content, $initCell = NULL, $initCellClass = NULL){
		$row = "<tr ";
		
		foreach ($params as $key => $value){
			$row .= "'".$key."' = '".$value."' ";
		}
		
		$row .= ">";
		
		if(!is_null($initCell)){
			$row .= "<th class='".(!is_null($initCellClass)?$initCellClass:"")."' >".$initCell."</th>";
		}
		
		foreach ($content as $cellkey => $value){
			$row .= "<td id='".$this->tableID."_".$this->rowNum."_".$cellkey."' class='".$params["class"]." ".$class[$cellkey]."' ";
			
			foreach ($params as $key => $pvalue){
				$row .= $key." = '".$pvalue."' ";
			}
		
			$row .= ">".$value."</th>";
		}
		
		$row .= "</tr>";
		
		$this->rowNum++;
		return $row;
	}
	
	public function populateHeader($params,$class,$content, $initCell = NULL, $initCellClass = NULL, $initId = NULL){
		$row = "<tr ";
		
		$row .= ">";
		
		if(!is_null($initCell)){
			$row .= "<th class='".(!is_null($initCellClass)?$initCellClass:"")."' id = '".(!is_null($initId)?$initId:"")."' >".$initCell."</th>";
		}
		
		foreach ($content as $key => $value){
			$row .= "<th class='".$params["class"][$key]." ".$class."' ";
			
			foreach ($params as $pkey => $pvalue){
				$row .= $pkey." = '".$pvalue."' ";
			}
		
			$row .= ">".$value."</th>";
		}
		
		$row .= "</tr>";
		return $row;
	}
	
	public function closeTable(){
		return "</tbody></table>";
	}
}