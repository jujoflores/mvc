<?php
abstract class Utils {

	public static function formatDateToHuman($date, $originalFormat){
		if($date == '0000-00-00 00:00:00' || $date == '1969-12-31 18:00:00'){
			return 'N/A';
		}
		$parts = explode(' ', $date);
		$dateParts = explode('-', $parts[0]);
		if($originalFormat === 'dd/mm/yy'){
			return "{$dateParts[2]}/{$dateParts[1]}/{$dateParts[0]} {$parts[1]}";
		}elseif($originalFormat === 'mm/dd/yy'){
			return "{$dateParts[1]}/{$dateParts[2]}/{$dateParts[0]} {$parts[1]}";
		}else{
			return '';
		}
	}
	
	public static function formatDateToDatabase($date, $originalFormat){
		$parts = explode(' ', $date);
		$dateParts = explode('/', $parts[0]);
		if($originalFormat === 'dd/mm/yy'){
			return "{$dateParts[2]}-{$dateParts[1]}-{$dateParts[0]} {$parts[1]}";
		}elseif($originalFormat === 'mm/dd/yy'){
			return "{$dateParts[2]}-{$dateParts[0]}-{$dateParts[1]} {$parts[1]}";
		}else{
			return '';
		}
	}
	
}