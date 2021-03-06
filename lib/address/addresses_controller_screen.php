<?php

class pz_addresses_controller_screen extends pz_addresses_controller {

	var $name = "addresses";
	var $function = "";
	var $functions = array("my", "all", "addresses" ); // "export", 
	var $function_default = "all";
	var $navigation = array("all", "my" ); // "export"

	function controller($function) {
	
		if(!in_array($function,$this->functions)) $function = $this->function_default;
		$this->function = $function;

		$p = array();

		$p["mediaview"] = "screen";
		$p["controll"] = "addresses";
		$p["function"] = $this->function;
		$p["linkvars"] = array();
		
		switch($this->function)
		{
			case("all"):
				return $this->getAddressesPage($p);
			case("my"):
				return $this->getMyAddressesPage($p);
			case("address"):
				return $this->getAddress($p);
			case("addresses"):
				return $this->getAddresses($p);
				break;
			default:
				return '';
				
		}
	}

	// ------------------------------------------------------------------- Views

	function getNavigation($p = array())
	{
		
		return pz_screen::getNavigation(
			$p,
			$this->navigation, 
			$this->function, 
			$this->name
		);

	}


	// --------------------------------------------------- Formular Views

	public function getAddresses() {
	
		$fulltext = rex_request("search_name","string");
		$mode = rex_request("mode","string","");
		$format = rex_request("format","string","json");

		$r_addresses = array();
		switch($mode)
		{
		
			case("get_user_emails"):
				$filter = array();
				$filter[] = array('field'=>'status', 'value'=>1);
				$filter[] = array('field'=>'name', 'type' => 'like', 'value'=>'%'.$fulltext.'%');
				// $fulltext
				// status = 1
				$users = pz::getUsers($filter); 
				foreach($users as $user) 
				{
					$r_addresses[] = array(
						"id" => $user->getId(),
						"label" => $user->getName()." [".$user->getEmail()."]",
						"value" => $user->getEmail()
					);
				}
				break;
		
			case("get_emails"):

				$addresses = pz_address::getAllByEmailFulltext($fulltext);

				foreach($addresses as $address) 
				{
					foreach($address->getFields() as $field) 
					{
						if( $field->getVar("type") == "EMAIL") 
						{
							$r_addresses[] = array(
								"id" => $field->getVar("value"),
								"label" => $address->getFullname()." - ".$field->getVar("value")." [".$field->getVar("label")."]",
								"value" => $field->getVar("value")
							);
						}
					}
				}
				break;
			default:
		}

		if($format == "json") 
			return json_encode($r_addresses); 
		
		return "";

	}



	public function getAddress() 
	{
		// TODO
		
		$address_id = rex_request("address_id","int",0);
		if($address_id < 1) {
			return FALSE;
		}
				
		if(!($address = pz_address::get($address_id))) {
			return FALSE;
		}
		
		$mode = rex_request("mode","string","");
		switch($mode)
		{
			case("vcard"):
				// TODO:
				return FALSE;
		}

	}



	// -------------------------------------------------------

	static function getAddressListOrders($orders = array(), $p = array())
	{

		$orders['iddesc'] = array("orderby" => "name", "sort" => "desc", "name" => rex_i18n::msg("address_orderby_iddesc"), 
			"link" => "javascript:pz_loadPage('addresses_list','".
			pz::url($p["mediaview"],$p["controll"],$p["function"],array_merge($p["linkvars"],array("mode" => "list", "search_orderby" => "iddesc"))).
			"')");
		$orders['idasc'] = array("orderby" => "name", "sort" => "asc", "name" => rex_i18n::msg("address_orderby_idasc"), 
			"link" => "javascript:pz_loadPage('addresses_list','".
			pz::url($p["mediaview"],$p["controll"],$p["function"],array_merge($p["linkvars"],array("mode" => "list", "search_orderby" => "idasc"))).
			"')");

		$orders['lastnamedesc'] = array("orderby" => "name", "sort" => "desc", "name" => rex_i18n::msg("address_orderby_lastnamedesc"), 
			"link" => "javascript:pz_loadPage('addresses_list','".
			pz::url($p["mediaview"],$p["controll"],$p["function"],array_merge($p["linkvars"],array("mode" => "list", "search_orderby" => "lastnamedesc"))).
			"')");

		$orders['lastnameasc'] = array("orderby" => "name", "sort" => "asc", "name" => rex_i18n::msg("address_orderby_lastnameasc"), 
			"link" => "javascript:pz_loadPage('addresses_list','".
			pz::url($p["mediaview"],$p["controll"],$p["function"],array_merge($p["linkvars"],array("mode" => "list", "search_orderby" => "lastnameasc"))).
			"')");

		$orders['firstnamedesc'] = array("orderby" => "firstname", "sort" => "desc", "name" => rex_i18n::msg("address_orderby_firstnamedesc"), 
			"link" => "javascript:pz_loadPage('addresses_list','".
			pz::url($p["mediaview"],$p["controll"],$p["function"],array_merge($p["linkvars"],array("mode" => "list", "search_orderby" => "firstnamedesc"))).
			"')");

		$orders['firstnameasc'] = array("orderby" => "firstname", "asc" => "asc", "name" => rex_i18n::msg("address_orderby_firstnameasc"), 
			"link" => "javascript:pz_loadPage('addresses_list','".
			pz::url($p["mediaview"],$p["controll"],$p["function"],array_merge($p["linkvars"],array("mode" => "list", "search_orderby" => "firstnameasc"))).
			"')");

		$orders['companydesc'] = array("orderby" => "company", "sort" => "desc", "name" => rex_i18n::msg("address_orderby_companydesc"), 
			"link" => "javascript:pz_loadPage('addresses_list','".
			pz::url($p["mediaview"],$p["controll"],$p["function"],array_merge($p["linkvars"],array("mode" => "list", "search_orderby" => "companydesc"))).
			"')");

		$orders['companyasc'] = array("orderby" => "company", "sort" => "asc", "name" => rex_i18n::msg("address_orderby_companyasc"), 
			"link" => "javascript:pz_loadPage('addresses_list','".
			pz::url($p["mediaview"],$p["controll"],$p["function"],array_merge($p["linkvars"],array("mode" => "list", "search_orderby" => "companyasc"))).
			"')");


		$current_order = 'lastnameasc';
		if(array_key_exists(rex_request("search_orderby"),$orders))
			$current_order = rex_request("search_orderby");

		$orders[$current_order]["active"] = true;
		
		$p["linkvars"]["search_orderby"] = $current_order;

		return array("orders" => $orders, "p" => $p, "current_order" => $current_order);
	}


	


	// ------------------------------------------------------- page views

	function getMyAddressesPage($p = array()) 
	{
		$p["title"] = rex_i18n::msg("all_projects");
		
		$s1_content = "";
		$s2_content = "";

		$fulltext = rex_request("search_name","string");
		$mode = rex_request("mode","string");
		switch($mode)
		{
			/*
			case("upload_photo"):
				// TODO
				$address_id = rex_request("address_id","int");
				if($address = pz_address::get($address_id)) {

				}				
				return "PHOTO";
			*/
				/*
			case("view_address"):
				$address_id = rex_request("address_id","int");
				if($address = pz_address::get($address_id)) {
					$r = new pz_address_screen($address);
					return $r->getDetailView($p);
				}
				return "";
				*/

			case("delete_address"):
				$address_id = rex_request("address_id","int");
				if($address = pz_address::get($address_id)) {
					$r = new pz_address_screen($address);
					$return = $r->getDeleteForm($p);
					$address->delete();
					return $return;
				}
				
			case("edit_address"):
				$address_id = rex_request("address_id","int");
				if($address = pz_address::get($address_id)) {
					$r = new pz_address_screen($address);
					return $r->getEditForm($p);
				}
				return "";
			case("add_address"):
				return pz_address_screen::getAddForm($p);
				break;
			case("list"):
				$addresses = pz::getUser()->getAddresses($fulltext);
				return pz_address_screen::getBlockListView(
							$addresses,
							array_merge( $p, array("linkvars" => array("mode" =>"list", "search_name" => $fulltext) ) )
						);
				break;
			case(""):
				$s1_content .= pz_address_screen::getAddressesSearchForm($p);
				$addresses = pz::getUser()->getAddresses($fulltext);
				$s2_content .= pz_address_screen::getBlockListView(
							$addresses,
							array_merge( $p, array("linkvars" => array("mode" =>"list", "search_name" => $fulltext) ) )
						);
				$form = pz_address_screen::getAddForm($p);
				break;
			default:
				break;
		}

		$s1_content .= $form;

		$f = new rex_fragment();
		$f->setVar('header', pz_screen::getHeader($p), false);
		$f->setVar('function', $this->getNavigation($p), false);
		$f->setVar('section_1', $s1_content, false);
		$f->setVar('section_2', $s2_content, false);
		return $f->parse('pz_screen_main.tpl');
		
	}	

	function getAddressesPage($p = array()) 
	{
		$p["title"] = rex_i18n::msg("all_projects");
		
		$s1_content = "";
		$s2_content = "";

		$fulltext = rex_request("search_name","string");

		$orders = array();
		$result = pz_addresses_controller_screen::getAddressListOrders($orders, $p);		
		$orders = $result["orders"];
		$current_order = $result["current_order"];
		$p = $result["p"];
		
		$mode = rex_request("mode","string");
		switch($mode)
		{
			case("delete_address"):
				$address_id = rex_request("address_id","int");
				if($address = pz_address::get($address_id)) {
					$r = new pz_address_screen($address);
					$return = $r->getDeleteForm($p);
					$address->delete();
					return $return;
				}
			case("edit_address"):
				$address_id = rex_request("address_id","int");
				if($address = pz_address::get($address_id)) {
					$r = new pz_address_screen($address);
					return $r->getEditForm($p);
				}
				return "";
			case("add_address"):
				return pz_address_screen::getAddForm($p);
				break;
			case("list"):
				$addresses = pz_address::getAllByFulltext($fulltext, array($orders[$current_order]));
				$p["linkvars"]["mode"] = "list";
				$p["linkvars"]["search_name"] = rex_request("search_name","string");
				
				return pz_address_screen::getBlockListView($addresses, $p, $orders);
				break;
			case(""):
				$p["linkvars"]["mode"] = "list";
				$p["linkvars"]["search_name"] = rex_request("search_name","string");
				$s1_content .= pz_address_screen::getAddressesSearchForm($p);
				$addresses = pz_address::getAllByFulltext($fulltext, array($orders[$current_order]));
				$s2_content .= pz_address_screen::getBlockListView($addresses, $p, $orders);
				$form = pz_address_screen::getAddForm($p);
				break;
			default:
				break;
		}

		$s1_content .= $form;

		$f = new rex_fragment();
		$f->setVar('header', pz_screen::getHeader($p), false);
		$f->setVar('function', $this->getNavigation($p), false);
		$f->setVar('section_1', $s1_content, false);
		$f->setVar('section_2', $s2_content, false);
		return $f->parse('pz_screen_main.tpl');
		
	}


}