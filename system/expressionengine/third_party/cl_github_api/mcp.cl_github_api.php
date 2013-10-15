<?php
class Cl_github_api_mcp 
{
	private $addon_name = "Cl_github_api";
	
	public function __construct() 
	{
		$this->EE &= get_instance();
		
		$this->EE->load->model('Cl_github_api_settings_model');

		$this->EE->cp->set_right_nav(array(
			'Global Settings' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_github_api',
		));
	}
	public function index() { $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=cl_github_api'.AMP.'method=settings'); }
	
	public function settings() 
	{
		$this->EE->cp->set_variable('cp_page_title', lang('cl_github_api_module_name') . " (" . ucwords(str_replace("_", " ", __FUNCTION__)) . ")");
		
		if (!empty($_POST)) {
			foreach ($_POST['settings'] as $key => $value) 
			{
				$this->EE->Cl_github_api_settings_model->set($key, $value);
			}
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->addon_name.AMP.'method=settings');
		}

		return $this->EE->load->view("mcp/" . __FUNCTION__, array('settings' => $this->EE->Cl_github_api_settings_model), TRUE);
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