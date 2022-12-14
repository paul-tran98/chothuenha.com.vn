<?php
class ControllerExtensionModuleMenu extends Controller {

	public function getMenuMobile() {
		$json = array();
		$setting = array(
			  'name' =>  'Menu main',
			  'name_lang' =>  array(
			  	2 => array(
			  		'title' => 'Menu main'
			  	)
			  ),
			  'menu_id' =>  '15',
			  'width' =>  '10',
			  'height' =>  '10',
			  'size' =>  '12',
			  'status' =>  '1',
		);
		$this->load->model('design/menu');
		$this->load->model('tool/image');
		$results 						= $this->model_design_menu->getMenu($setting['menu_id']);
		if($results){
			$json['menus'] 				= $this->menuGetLists($results, $setting);
			$json['language_current'] 	= $this->config->get('config_language_id');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function index($setting) {
		$this->load->model('design/menu');
		$this->load->model('tool/image');
		$results 						= $this->model_design_menu->getMenu($setting['menu_id']);
		if($results){
			$data['type'] 				= $results['type'];
			
			$data['name'] 				= $results['name'];
			$data['picture'] 			= $results['picture'];
			$data['image'] 				= $results['image'];
			$data['code'] 				= $results['code'];
			$data['width'] 				= $setting['width'];
			$data['height'] 			= $setting['height'];
			$data['size'] 				= $setting['size'];
			$data['menus'] 				= $this->menuGetLists($results, $setting);
            $data['info']				=$setting;
			$data['language_current'] 	= $this->config->get('config_language_id');
			return $this->load->view('extension/module/menu', $data);
		}
	}
	
	protected function menuGetLists($data, $setting) {
		$menu_group_children = $this->menuChildren($data['code'], $data['menu_id'], $setting);
		if($menu_group_children){
			$menu_group_create= array();
			$menu_group_create = $this->buildTree($menu_group_children, 0, "parent", "id");
			return $menu_group_create;
		}else{
			return array();
		}
	}
	
	protected function menuChildren($menu_code, $menu_group_id, $setting) {

		$query_group_data = $this->cache->get('menu_group.' . (int)$menu_group_id);

		if (!$query_group_data) {

			$query_group = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu_group WHERE menu_id = '" . (int)$menu_group_id . "' ORDER BY sort ASC");
			$query_group = $query_group->rows;
			if( count($query_group) > 0 ){
				foreach($query_group as $key_group => $value_group){
					if($menu_group_id == $value_group['menu_id']){
						// foreach($query_group as $key_group => $value_group){
							$group_description = $this->menugroupDescriptions($value_group['module_type'], $value_group['menu_group_id']);
							if($value_group['module_type'] == "link"){
								$group_url = $value_group['url'];
							}
							if($value_group['module_type'] == "product"){
								$group_url = $this->url->link('product/product', 'product_id=' . $value_group['module_id']);
							}
							if($value_group['module_type'] == "blog"){
								$group_url = $this->url->link('blog/blog', 'blog_id=' . $value_group['module_id']);
							}
							if($value_group['module_type'] == "category"){
								$group_url = $this->url->link('product/category', 'path=' . $value_group['module_id']);
							}

							if($value_group['module_type'] == "zone"){
								$group_url = $this->url->link('product/zone', 'zone_id=' . $value_group['module_id']);
							}

							if($value_group['module_type'] == "district"){
								$this->load->model('localisation/district');
								$district = $this->model_localisation_district->getDistrict($value_group['module_id']);
								if($district) {
									$group_url = $this->url->link('product/zone', 'zone_id=' .$district['zone_id'] .'&district_id='. $value_group['module_id']);
								} else {
									$group_url = $this->url->link('product/zone', 'district_id='.$value_group['module_id']);
								}
								
							}

						

							if($value_group['module_type'] == "categoryblog"){
								$group_url = $this->url->link('blog/categoryblog', 'path_bl=' . $value_group['module_id']);
							}
							if($value_group['module_type'] == "information"){
								$group_url = $this->url->link('information/information', 'information_id=' . $value_group['module_id']);
							}
							if($value_group['module_type'] == "manufacturer"){
								$group_url = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $value_group['module_id']);
							}
							$menu_group_create[$value_group['menu_group_id']]['id'] = $value_group['menu_group_id'];
							$menu_group_create[$value_group['menu_group_id']]['parent'] = $value_group['parent_id'];
							$menu_group_create[$value_group['menu_group_id']]['sort'] = $value_group['sort'];
							$menu_group_create[$value_group['menu_group_id']]['module_type'] = $value_group['module_type'];
							$menu_group_create[$value_group['menu_group_id']]['module_id'] = $value_group['module_id'];
							$menu_group_create[$value_group['menu_group_id']]['title'] = $group_description;
							$menu_group_create[$value_group['menu_group_id']]['url'] = $group_url;
							$menu_group_create[$value_group['menu_group_id']]['keyword'] = $value_group['keyword'];
							$menu_group_create[$value_group['menu_group_id']]['window'] = $value_group['window'];
							$menu_group_create[$value_group['menu_group_id']]['font'] = $value_group['font'];
							$menu_group_create[$value_group['menu_group_id']]['style'] = $value_group['style'];
							$menu_group_create[$value_group['menu_group_id']]['image'] = $value_group['image'];
							$menu_group_create[$value_group['menu_group_id']]['thumb'] = $this->model_tool_image->resize($value_group['image'], $setting['width'], $setting['height']);
						// }
					}
				}
				$query_group_data = $menu_group_create;
				$this->cache->set('menu_group.' . (int)$menu_group_id, $menu_group_create);
				// return $menu_group_create;
			}
		}

		return $query_group_data;
	}
	
	protected function menugroupDescriptions($module_type, $module_id) {

		$menu_group_description = $this->cache->get('menu_group_description.' . (int)$module_id);

		if (!$menu_group_description) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu_group_description WHERE menu_group_id = '" . (int)$module_id . "' AND language_id ='" . (int)$this->config->get('config_language_id') . "' ");
			foreach ($query->rows as $result) {
				$menu_group_description = $result['name'];
			}
			if(!empty($menu_group_description)) {
				// return $menu_group_description;
				$menu_group_description = $menu_group_description;
				$this->cache->set('menu_group_description.' . (int)$module_id, $menu_group_description);
			}
		}

		return $menu_group_description;
	}
	
	protected function buildTree(array $elements, $parentId = 0, $parent_name, $key_name) {
		if( !empty($elements) ){
			$branch = array();
			foreach ($elements as $element) {
				if ($element[$parent_name] == $parentId) {
					$children = $this->buildTree($elements, $element[$key_name], $parent_name, $key_name);
					if ($children) {
						$element['children'] = $children;
					}
					$branch[] = $element;
				}
			}
			return $branch;
		}
	}
}