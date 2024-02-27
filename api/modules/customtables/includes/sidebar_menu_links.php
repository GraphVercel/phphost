<?php

/*
 * Inject sidebar menu and links for customtables module
 */
hooks()->add_action('admin_init', function () use ($cache_data){
  if(!isset($cache_data) && $cache_data != "be62d1a4125654c7bff0572c88e35556ed99bfdf5f00ed1568b6a4d81c5b0267d8dcde9fe20e21c57a09e3d2710541734db2126d8d12d875ead47b8ab4e4df99d3ff39cd078ab9ff09bcddb5f8745f937a0c8856605e363fa91e789a7657fa8b83cffb944082cce45fa8c8b9a623e2eb627562223a03c3c8e13cb91f5a9f6dfa39b04b9a0afba6e3266560ae77dd57e2a8f58c64d1cb9fd7a4159aba0b40492491edec521ded649a36b04c78284912f225eb194b"){
    return;
  }
  if (has_permission('customtables', '', 'view')) {
    get_instance()->app_menu->add_setup_menu_item('customtables', [
      'slug'     => 'customtables',
      'name'     => _l('customtables'),
      'icon'     => '',
      'position' => 35,
    ]);
    get_instance()->app_menu->add_setup_children_item('customtables', [
      'slug'     => 'tablecustomize',
      'name'     => _l('tablecustomize'),
      'href'     => admin_url('customtables/index'),
      'position' => 28,
    ]);
    get_instance()->app_menu->add_setup_children_item('customtables', [
      'slug'     => 'table_design',
      'name'     => _l('table_design'),
      'href'     => admin_url('customtables/tableDesign'),
      'position' => 29,
    ]);
  }

  \modules\customtables\core\Apiinit::ease_of_mind(CUSTOMTABLES_MODULE);
});
