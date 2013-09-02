<?php
class Cl_github
{
	public function __construct() 
	{
		$this->EE =& get_instance();
		$this->EE->load->model('Cl_github_settings_model');
	}
}