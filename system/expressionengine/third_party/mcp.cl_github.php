<?php
class Cl_github_mcp 
{
	private $settings;
	private $addon_name;
	
	public function __construct() {

		ee()->load->helper('form');
		$this->addon_name = cl_rstr_replace("_mcp", "", __CLASS__);

		ee()->load->model('Cl_github_settings_model');

		ee()->cp->set_right_nav(array(
			'settings' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_github',
		));
	}
	public function index() { ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_github'.AMP.'method=settings'); }
	
	public function settings() 
	{
		ee()->cp->set_variable('cp_page_title', lang('cl_github_module_name') . " (" . ucwords(str_replace("_", " ", __FUNCTION__)) . ")");
		
		if (!empty($_POST)) {
			foreach ($_POST['settings'] as $key => $value) 
			{
				ee()->Cl_github_settings_model->set($key, $value);
			}
			ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->addon_name.AMP.'method=settings');
		}

		return ee()->load->view("mcp/" . __FUNCTION__, array('settings' => ee()->Cl_github_settings_model), TRUE);
	}

}