<?php
class mediawikimemberAdminController extends mediawikimember {
  function init() {
  }

  function procMediawikimemberAdminSiteChange() {
    $oModuleModel = &getModel('module');
    $oModuleController = &getController('module');

    $able_module = Context::get('able_module');

    $site_protocol = Context::get('site_protocol');
    $site_url = Context::get('site_url');
    $admin_id = Context::get('admin_id');
    $admin_pw = Context::get('admin_pw');

    $config = $oModuleModel->getModuleConfig('mediawikimember');

    $config->able_module = $able_module;
    $config->site_protocol = $site_protocol;
    $config->site_url = $site_url;
    $config->admin_id = $admin_id;
    $config->admin_pw = $admin_pw ? convert_uuencode($admin_pw) : $config->admin_pw;

    $oModuleController->insertModuleConfig('mediawikimember', $config);

    if ($config->able_module == 'Y') {
      $oModuleController->insertTrigger('member.doLogin', 'mediawikimember', 'controller', 'triggerBeforeLogin', 'before');
      $oModuleController->insertTrigger('moduleHandler.init', 'mediawikimember', 'controller', 'triggerBeforeChangePassword', 'before');
    } else if ($config->able_module != 'Y') {
      if ($oModuleModel->getTrigger('member.doLogin', 'mediawikimember', 'controller', 'triggerBeforeLogin', 'before')) {
        $oModuleController->deleteTrigger('member.doLogin', 'mediawikimember', 'controller', 'triggerBeforeLogin', 'before');
      }

      if ($oModuleModel->getTrigger('moduleHandler.init', 'mediawikimember', 'controller', 'triggerBeforeChangePassword', 'before')) {
        $oModuleController->deleteTrigger('moduleHandler.init', 'mediawikimember', 'controller', 'triggerBeforeChangePassword', 'before');
      }
    }

    // redirect url
    $returnUrl = getNotEncodedUrl('', 'module', 'admin', 'act', 'dispMediawikimemberAdminIndex');
    $this->setRedirectUrl($returnUrl);
  }
}
?>