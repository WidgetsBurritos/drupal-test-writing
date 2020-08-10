<?php

namespace Drupal\TestSite;

/**
 * Setup file used by Nightwatch tests.
 */
class TestSiteInstallTestScript implements TestSetupInterface {

  /**
   * {@inheritdoc}
   */
  public function setup() {
    $modules = ['my_testing_module'];
    \Drupal::service('module_installer')->install($modules);
  }

}
