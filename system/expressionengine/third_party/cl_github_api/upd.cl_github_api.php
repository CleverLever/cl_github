<?php

class Cl_github_api_upd 
{
	public $version = "1.2.1";
	
	private $addon_name = "Cl_github_api";
	private $has_cp_backend = "y";
	private $has_publish_fields = "n";
	private $settings = array();

	private $mod_actions = array(
		'oauth_callback',
	);

	public function __construct() 
	{
		$this->EE &= get_instance();
		$this->EE->load->add_package_path(dirname(__FILE__));
	}

	function install()
	{
		$this->_install_module();
		$this->_install_actions();
		$this->_install_models();

		return TRUE;
	}

	function uninstall() 
	{
		$this->_uninstall_module();
		$this->_uninstall_actions();
		$this->_uninstall_models();

		return TRUE;
	}
	
	private function _install_module() 
	{
		$data = array(
			'module_name' => $this->addon_name,
			'module_version' => $this->version,
			'has_cp_backend' => $this->has_cp_backend,
			'has_publish_fields' => $this->has_publish_fields,
			'settings' => json_encode($this->settings),
		);
		$this->EE->db->insert('modules', $data);
	}

	private function _uninstall_module() 
	{
		$this->EE->db->delete('modules', array('module_name' => $this->addon_name));
	}
	
	private function _install_actions() 
	{
		// get existing actions
		$this->EE->db->select('method')
			->from('actions')
			->like('class', $this->addon_name, 'after');
		$existing_methods = array();
		foreach ($this->EE->db->get()->result() as $row) $existing_methods[] = $row->method;

		// insert new actions
		foreach ($this->mod_actions as $method)	{
			if ( ! in_array($method, $existing_methods)) {
				$this->EE->db->insert('actions', array('class' => $this->addon_name, 'method' => $method));
			}
		}
	}
	
	private function _uninstall_actions()
	{
		$this->EE->db->like('class', $this->addon_name, 'after')->delete('actions');
	}
	
	private function _install_models()
	{
		foreach (glob(dirname(__FILE__) . "/models/*.php") as $model)
		{
			$model = ucfirst(pathinfo($model, PATHINFO_FILENAME));

			$this->EE->load->model($model);
			if (isset($this->EE->$model->table)) $this->EE->$model->create_table();
		}
	}
	
	private function _uninstall_models()
	{
		foreach (glob(dirname(__FILE__) . "/models/*.php") as $model)
		{
			$model = ucfirst(pathinfo($model, PATHINFO_FILENAME));

			$this->EE->load->model($model);
			if (isset($this->EE->$model->table)) $this->EE->$model->drop_table();
		}
	}
}
