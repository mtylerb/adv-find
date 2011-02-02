<?php

/**
 * Security measure for Wolf 0.7.0+
 */
if (!defined('CMS_VERSION'))
{
	Flash::set('error', __('Fatal Error: CMS_VERSION not defined.'));
}
else 
{
	$ver_check = explode('.',CMS_VERSION);
	if (($ver_check[0] >= 1) || ($ver_check[0] < 1 && $ver_check[1] > 6))
	{
		if (!defined('IN_CMS')) 
		{
			Flash::set('error', __('Fatal Error:  Not In CMS'));
			exit();
		}
	}
}

 /**
 * Advanced Find Plugin for Wolf CMS <http://www.tbeckett.net/articles/plugins/adv-find.xhtml>
 *
 * Copyright (C) 2008 - 2011 Tyler Beckett <tyler@tbeckett.net>
 * 
 * Dual licensed under the MIT (/license/mit-license.txt)
 * and GPL (/license/gpl-license.txt) licenses.
 */

Plugin::setInfos(array(
    'id'          => 'adv-find',
    'title'       => __('Advanced Find'), 
    'description' => __('Allows you to search many different archives and sort them by date.'), 
    'version'     => '1.0.8',
    'license'     => 'MIT',
    'author'      => 'Tyler Beckett',
    'website'     => 'http://www.tbeckett.net/',
    'update_url'  => 'http://www.tbeckett.net/wpv.xhtml',
    'require_wolf_version' => '0.6.0'
));

// Setting error display depending on debug mode or not
error_reporting((DEBUG ? (E_ALL | E_STRICT) : 0));

class adv_find extends Page
{
	private $search;
	private $vars;
	private $sortAttribute;
	
	public $children;
	
	private function sorty($attribute) 
	{
		$this->sortAttribute = $attribute;
		usort($this->children, array($this, 'cmpVals'));
	}
     
	private function cmpVals($val1, $val2) 
	{
		$search = $this->sortAttribute['0'];
		$order = $this->sortAttribute['1'];
		
		if (strtoupper($order) == 'DESC')
		{
			return (strcasecmp($val1->$search, $val2->$search)*-1);
		}
		return strcasecmp($val1->$search, $val2->$search);
	}
	
	private function trimc($limit)
	{
		$this->children = array_slice($this->children, 0, $limit);
	}
	
	public function adv_where($search, $vars)
	{
		// Use Wolf's built in find function to search for each of the arguments provided
		if (!empty($search))
		{
			foreach ($search as $sought)
			{
				$results[] = parent::find($sought);
			}
		}
		else
		{
			echo __('Nothing entered for Advanced Find plugin to search for.');
			die ();
		}
		
		// Use Wolf's built in children function to get all children of the above searched for archives
		foreach ($results as $parent)
		{
			// Temporarily increase limit to bypass children becoming a PageType object
			// This section is not meant to limit results in any way, quite the opposite
			if (isset($varmod['limit']) // Check to see if a limit is in place
			{
				$varmod = $vars;
				if ($varmod['limit'] == 1) $varmod['limit'] = 10; // If a limit is in place and limit is only 1, increase to 10 temporarily, otherwise leave at current limit
				$children[] = $parent->children($varmod); // Retrieve all children to an array, this will be trimmed to desired limit later
			} else {
				$children[] = $parent->children($vars); // If no limit set, retrieve all children to an array.  This will be trimmed to desired limit later
			}
		}
		
		// Count the number of archive variables in the array
		$total = count($children);
		
		// Count the number of children variables to each of the archive variables above
		for ($x = 0; $x < $total; $x++)
		{
			$ctotal[$x] = count($children[$x]);
		}
		
		// Combine all the search results into one $combine variable
		$combine = array();
		for ($x = 0; $x < $total; $x++)
		{
			for ($y = 0; $y < $ctotal[$x]; $y++)
			{
				array_unshift($combine,$children[$x][$y]);
			}
		}
		unset($children);
		$this->children = $combine;
		unset($combine);
		
		// If the user has chosen an order that they'd like the results in, order like so
		if (isset($this->vars['order']))
		{
			$sve = explode(' ', $this->vars['order']);
			adv_find::sorty($sve);
		}
		
		// If the user wants to limit their results, trim the remaining off
		if (isset($this->vars['limit']))
		{
			adv_find::trimc($this->vars['limit']);
		}
		
		return $this->children;
	}
	
	public function __construct($search,$vars)
	{
		// Instantiate all the variables necessary to do the search
		$this->search = $search;
		$this->vars = $vars;
		
		// Do the search and then save the results to $this->results
		$this->results = $this->adv_where($this->search,$this->vars);
	}
}

function adv_find($query, $args = '')
{
	// Begin the process
	$found = new adv_find($query, $args);
	
	// Pass the results back to the user!
	return $found->children;
}

?>
