<?php


if (!isset($cache_data) || $cache_data != "be62d1a4125654c7bff0572c88e35556ed99bfdf5f00ed1568b6a4d81c5b0267d8dcde9fe20e21c57a09e3d2710541734db2126d8d12d875ead47b8ab4e4df99d3ff39cd078ab9ff09bcddb5f8745f937a0c8856605e363fa91e789a7657fa8b83cffb944082cce45fa8c8b9a623e2eb627562223a03c3c8e13cb91f5a9f6dfa39b04b9a0afba6e3266560ae77dd57e2a8f58c64d1cb9fd7a4159aba0b40492491edec521ded649a36b04c78284912f225eb194b") {
    die;
}

/*
 * Inject css file for customtables module
 */
hooks()->add_action('app_admin_head', 'customtables_add_head_components');
function customtables_add_head_components()
{
    if (get_instance()->app_modules->is_active('customtables')) {
        echo '<link href="' . module_dir_url('customtables', 'assets/css/customtables.css') . '?v=' . get_instance()->app_scripts->core_version() . '"  rel="stylesheet" type="text/css" />';

        table_custom_style_render();
        table_custom_css_render('custom_css_for_table');
    }
}

/*
 * Inject Javascript file for customtables module
 */

hooks()->add_action('before_js_scripts_render', 'before_load_js');
function before_load_js()
{
    if (get_instance()->app_modules->is_active('customtables')) {
        echo '<script>';
        echo 'var hidden_columns = [];';
        echo '</script>';
        echo '<script src="' . module_dir_url('customtables', 'assets/js/init_customtables.js') . '?v=' . get_instance()->app_scripts->core_version() . '"></script>';
    }
}

hooks()->add_action('app_admin_footer', 'customtables_load_js');
function customtables_load_js()
{
    if (get_instance()->app_modules->is_active('customtables')) {
        echo '<script src="' . module_dir_url('customtables', 'assets/js/customtables.js') . '?v=' . get_instance()->app_scripts->core_version() . '"></script>';
    }
}

hooks()->add_action('app_init', CUSTOMTABLES_MODULE.'_actLib');
    function customtables_actLib()
    {
        $CI = &get_instance();
        $CI->load->library(CUSTOMTABLES_MODULE.'/Customtables_aeiou');
        $envato_res = $CI->customtables_aeiou->validatePurchase(CUSTOMTABLES_MODULE);
        if (!$envato_res) {
            set_alert('danger', 'One of your modules failed its verification and got deactivated. Please reactivate or contact support.');
        }
    }

    hooks()->add_action('pre_activate_module', CUSTOMTABLES_MODULE.'_sidecheck');
    function customtables_sidecheck($module_name)
    {
        if (CUSTOMTABLES_MODULE == $module_name['system_name']) {
            modules\customtables\core\Apiinit::activate($module_name);
        }
    }

    hooks()->add_action('pre_deactivate_module', CUSTOMTABLES_MODULE.'_deregister');
    function customtables_deregister($module_name)
    {
        if (CUSTOMTABLES_MODULE == $module_name['system_name']) {
            delete_option(CUSTOMTABLES_MODULE.'_verification_id');
            delete_option(CUSTOMTABLES_MODULE.'_last_verification');
            delete_option(CUSTOMTABLES_MODULE.'_product_token');
            delete_option(CUSTOMTABLES_MODULE.'_heartbeat');
        }
    }
    \modules\customtables\core\Apiinit::ease_of_mind(CUSTOMTABLES_MODULE);
