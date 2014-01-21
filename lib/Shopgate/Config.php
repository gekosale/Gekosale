<?php

class ShopgateConfigWellCommerce extends \ShopgateConfig
{

    public function startup ()
    {
        $settings = \Gekosale\App::getRegistry()->core->loadModuleSettings('shopgate', (int) \Gekosale\Helper::getViewId());
         
        $this->shop_number = $settings['shopgateshopnumber'];
        $this->customerNumber = $settings['shopgatecustomernumber'];
        $this->apiKey = $settings['shopgateapikey'];
        $this->alias = 'wellcommerce';
        
        $this->plugin_name = 'WellCommerce';
        $this->enable_redirect_keyword_update = 24;
        $this->enable_ping = 1;
        $this->enable_add_order = 1;
        $this->enable_update_order = 1;
        $this->enable_get_orders = 0;
        $this->enable_get_customer = 1;
        $this->enable_get_items_csv = 1;
        $this->enable_get_categories_csv = 1;
        $this->enable_get_reviews_csv = 1;
        $this->enable_get_pages_csv = 0;
        $this->enable_get_log_file = 1;
        $this->enable_mobile_website = 1;
        $this->enable_cron = 0;
        $this->enable_clear_logfile = 1;
        $this->encoding = 'UTF-8';
        $this->use_stock = 1;
        $this->currency = 'PLN';
        
        $this->additionalSettings['plugin'] = 'WellCommerce';
    }

    public function getServer ()
    {
        return 'pg';
    }
}

