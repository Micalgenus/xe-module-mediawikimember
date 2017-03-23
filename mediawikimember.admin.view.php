<?php
class mediawikimemberAdminView extends mediawikimember {
  function init() {
    $template_path = sprintf("%stpl/", $this->module_path);
    $this->setTemplatePath($template_path);
  }

  function dispMediawikimemberAdminIndex() {
    $config = &getModel('module')->getModuleConfig('mediawikimember');

    Context::set('able_module', $config->able_module);
    Context::set('site_protocol', $config->site_protocol);
    Context::set('site_url', $config->site_url);
    Context::set('admin_id', $config->admin_id);
    Context::set('admin_pw', $config->admin_pw ? str_repeat('*', 8) : '');

    $this->setTemplateFile('index');
  }
}
?>