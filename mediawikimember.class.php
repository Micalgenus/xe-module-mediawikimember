<?php
class mediawikimember extends ModuleObject {
  function mediawikimember() {
    if (!Context::isInstalled()) return;
  }

  function checkUpdate() {
    $config = getModel('module')->getModuleConfig('mediawikimember');
    if (!$config) return true;
    return false;
  }

  function moduleUpdate() {
    $oModuleModel = &getModel('module');
    $oModuleController = &getController('module');

    $config = $this->InitConfig();
    $oModuleController->insertModuleConfig('mediawikimember', $config);

    return new Object(0, 'success_updated');
  }

  function recompileCache() {
  }

  function InitConfig() {
    $config = getModel('module')->getModuleConfig('mediawikimember');
    $config->admin_id = $config->admin_id ?: "";
    $config->admin_pw = $config->admin_pw ?: "";
    $config->site_url = $config->site_url ?: "";
    $config->able_module = $config->able_module ?: "N";

    return $config;
  }
}