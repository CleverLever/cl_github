<?php
class Cl_github_mcp 
{
	private $addon_name = "Cl_github";
	
	public function __construct() 
	{
		ee()->load->model('Cl_github_settings_model');

		ee()->cp->set_right_nav(array(
			'Global Settings' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_github',
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

	public function get_access_token() 
	{
		$member_id = $this->EE->TMPL->fetch_param('member_id', $this->EE->session->userdata('member_id'));
		if ($this->EE->session->userdata('member_id') == 0) return FALSE;

		$provider = $this->EE->Eedfw_oauth_providers_model->short_name($this->EE->TMPL->fetch_param('provider'))->row_array();
		if (empty($provider)) show_error(lang("error_couldnt_load_provider_settings"));

		$access_token = $this->EE->Eedfw_oauth_access_tokens_model->get($provider['provider_id'], $this->EE->session->userdata('member_id'));
		if (!empty($access_token)) {
			if (time() > $access_token['modified_date'] + $access_token['expires_in']) {
				$access_token = $this->refresh_access_token($provider['short_name'], $access_token['member_id'], $access_token['refresh_token']);
			}
			return $access_token['access_token'];
		} else {
			return FALSE;
		}
	}



}