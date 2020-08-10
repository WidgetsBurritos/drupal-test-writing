<?php

namespace Drupal\Tests\my_testing_module\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Functional tests for my_testing_module.
 *
 * @group my_testing_module
 */
class MyFunctionalTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public $profile = 'testing';

  /**
   * {@inheritdoc}
   */
  public $defaultTheme = 'stable';

  /**
   * Logged in user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $authorizedUser;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'my_testing_module',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->authorizedUser = $this->drupalCreateUser([], 'Regular User');
  }

  /**
   * Functional test confirming the controller is loading.
   */
  public function testMessageControllerProperlyHandlesAccessControls() {
    $assert = $this->assertSession();

    // Test unauthenticated users can't access the controller.
    $this->drupalGet('my-message');
    $assert->pageTextContains('You are not authorized to access this page.');
    $assert->statusCodeEquals(403);

    // Test authenticated users can access the controller.
    $this->drupalLogin($this->authorizedUser);
    $this->drupalGet('my-message');
    $assert->pageTextContains('Hi Regular User.');
    $assert->statusCodeEquals(200);
  }

}
