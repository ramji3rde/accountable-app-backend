<?php
function get_custom_title( $cf1,$cl1, $cf2, $cl2){
  	$cf1 = trim( $cf1 );
	$cl1 = trim( $cl1 );
	$cf2 = trim( $cf2 );
	$cl2 = trim( $cl2 );

	if( ( '' == $cf1 ) && ( '' == $cl1) ){
		return $cf2. ' '. $cl2;
	}else{
		return $cf1. ' '. $cl1;
	}
}
?>