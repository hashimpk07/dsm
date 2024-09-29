<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Billing, payment and other duration units.
 *
 * @author nithin
 */
class content_type_model extends PropertyModel
{
	public function __construct()
	{
		$this->collection = array(
				'I' => 'Image',
				'IU' => 'Image URL',
				'V' => 'Video',
				'VU' => 'Video URL',
				'T' => 'Text',
				'H' => 'HTML',
				'S' => 'Scrolling Text',
			) ;
	}
	public function color($type)
	{
		$color = array(
				'I' => 'green',
				'IU' => 'lightgreen',
				'V' => 'red',
				'VU' => 'lightred',
				'T' => 'brown',
				'H' => 'blue',
				'S' => 'orange',
//				'I' => 'inherit', //green',
//				'IU' => 'inherit', //lightgreen',
//				'V' => 'inherit', //red',
//				'VU' => 'inherit', //lightred',
//				'T' => 'inherit', //brown',
//				'H' => 'inherit', //blue',
//				'S' => 'inherit', //orange',
			) ;
		
		if( isset($color[$type]) )
		{
			return $color[$type] ;
		}
		return '#000000' ;
	}
}