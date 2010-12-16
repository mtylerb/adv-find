<?php

/**
 * Advanced Find Plugin for Frog CMS (original)
 * Copyright (C) 2008 Tyler Beckett <tyler@tbeckett.net>
 * Adopted by Dejan Andjelkovic 2010 for Wolf CMS as maintainer

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.

 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Plugin::setInfos(array(
    'id'          => 'adv-find',
    'title'       => 'Advanced Find', 
    'description' => 'Allows you to search many different archives and sort them by date.', 
    'version'     => '1.0.2',
    'license'     => 'AGPL',
    'author'      => 'Tyler Beckett',
    'website'     => 'http://www.tbeckett.net/',
    'update_url'  => 'http://www.tbeckett.net/wpv.xhtml',
    'require_wolf_version' => '0.5.5'
));

error_reporting(E_ALL^E_NOTICE);

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
		foreach ($search as $sought)
		{
			$results[] = parent::find($sought);
		}
		
		// Use Wolf's built in children function to get all children of the above searched for archives
		foreach ($results as $parent)
		{
			$children[] = $parent->children($vars);
		}
		
		// Count the number of archive variables in the array
		$total = count($children);
		
		// Count the number of children variables to each of the archive variables above
		for ($x = 0; $x <= $total; $x++)
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
