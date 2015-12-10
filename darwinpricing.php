<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Darwinpricing extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'darwinpricing';
        $this->tab = 'pricing_promotion';
        $this->version = '1.0.0';
        $this->author = 'Darwin Pricing';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Darwin Pricing - Geo-Targeted Sales');
        $this->description = $this->l('The most popular Geo-Pricing app on PrestaShop! Make 50% more money with geo-targeted sales campaigns. Automatic targeting optimization & High converting Exit Intent coupon box included!');
    }

    public function install()
    {
        $this->warning = null;

        if (is_null($this->warning) && !function_exists('curl_init')) {
            $this->warning = $this->l('cURL is required to use this module. Please install the php extention cURL.');
        }

        if (is_null($this->warning)
                && !(parent::install()
                && Configuration::updateValue('DARWINPRICING_LIVE_MODE', false)
                && $this->registerHook('header')
                && $this->registerHook('actionValidateOrder')
                && $this->registerHook('actionOrderReturn'))) {
            $this->warning = $this->l('There was an error installing this module.');
        }

        return is_null($this->warning);
    }

    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitDarwinpricingModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDarwinpricingModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'DARWINPRICING_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-globe"></i>',
                        'desc' => $this->l('The URL of the API server for your website, e.g. https://api.darwinpricing.com'),
                        'name' => 'DARWINPRICING_SERVER_URL',
                        'label' => $this->l('API Server'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-user"></i>',
                        'desc' => $this->l('The client ID for your website'),
                        'name' => 'DARWINPRICING_CLIENT_ID',
                        'label' => $this->l('Client ID'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-key"></i>',
                        'desc' => $this->l('The client secret for your website'),
                        'name' => 'DARWINPRICING_CLIENT_SECRET',
                        'label' => $this->l('Client Secret'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'DARWINPRICING_LIVE_MODE' => Configuration::get('DARWINPRICING_LIVE_MODE', false),
            'DARWINPRICING_SERVER_URL' => Configuration::get('DARWINPRICING_SERVER_URL', null),
            'DARWINPRICING_CLIENT_ID' => Configuration::get('DARWINPRICING_CLIENT_ID', null),
            'DARWINPRICING_CLIENT_SECRET' => Configuration::get('DARWINPRICING_CLIENT_SECRET', null),
        );
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookHeader()
    {
        if ($this->isActive()) {
            $widgetUrl = $this->getApiUrl('/widget');
            $this->context->controller->addJS($widgetUrl);
        }
    }

    public function hookActionValidateOrder($params)
    {
        try {
            if ($this->isActive()) {
                $url = $this->getApiUrl('/prestashop/webhook-validate-order', true);
                $body = Tools::jsonEncode($params);
                $this->webhook($url, $body);
            }
        } catch (Exception $exception) {
            $order = $params['order'];
            $id_order = (int)$order->id;
            $message = 'Darwinpricing::hookActionValidateOrder - Cannot send order details';
            PrestaShopLogger::addLog($message, 3, null, 'Order', $id_order);
        }
    }

    public function hookActionOrderReturn($params)
    {
        try {
            if ($this->isActive()) {
                $url = $this->getApiUrl('/prestashop/webhook-order-return', true);
                $body = Tools::jsonEncode($params);
                $this->webhook($url, $body);
            }
        } catch (Exception $exception) {
            $orderReturn = $params['orderReturn'];
            $id_order = $orderReturn->id_order;
            $message = 'Darwinpricing::hookActionOrderReturn - Cannot send order return details';
            PrestaShopLogger::addLog($message, 3, null, 'Order', $id_order);
        }
    }

    protected function getApiUrl($path, $authenticationRequired)
    {
        $serverUrl = Configuration::get('DARWINPRICING_SERVER_URL', null);
        $clientId = Configuration::get('DARWINPRICING_CLIENT_ID', null);
        $clientSecret = Configuration::get('DARWINPRICING_CLIENT_SECRET', null);
        $serverUrl = rtrim($serverUrl, '/');
        $apiUrl = $serverUrl.$path;
        $parameterList = array('platform' => 'prestashop-'._PS_VERSION_, 'site-id' => $clientId);
        if ($authenticationRequired) {
            $parameterList['hash'] = $clientSecret;
        }
        $apiUrl .= '?'.http_build_query($parameterList);
        return $apiUrl;
    }

    protected function isActive()
    {
        $liveMode = Configuration::get('DARWINPRICING_LIVE_MODE', false);
        $serverUrl = Configuration::get('DARWINPRICING_SERVER_URL', null);
        $clientId = Configuration::get('DARWINPRICING_CLIENT_ID', null);
        $clientSecret = Configuration::get('DARWINPRICING_CLIENT_SECRET', null);
        return ($liveMode && isset($serverUrl) && isset($clientId) && isset($clientSecret));
    }

    protected function webhook($url, $body)
    {
        $optionList = array(
            CURLOPT_POST => true,
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT_MS => 3000,
            CURLOPT_POSTFIELDS => $body,
        );
        $ch = curl_init();
        curl_setopt_array($ch, $optionList);
        curl_exec($ch);
        curl_close($ch);
    }
}
