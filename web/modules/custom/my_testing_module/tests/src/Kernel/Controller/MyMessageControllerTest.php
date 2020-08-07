<?php

namespace Drupal\Tests\my_testing_module\Kernel\Controller;

use Drupal\KernelTests\KernelTestBase;
use Drupal\my_testing_module\Controller\MyMessageController;
use Drupal\Tests\user\Traits\UserCreationTrait;
use Drupal\Core\Logger\RfcLogLevel;

/**
 * Tests the functionality of my message controller.
 *
 * @group my_testing_module
 */
class MyMessageControllerTest extends KernelTestBase {

  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['my_testing_module', 'dblog', 'user', 'system'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installSchema('dblog', ['watchdog']);
  }

  /**
   * Helper method to retrieve the last watchdog message by severity.
   *
   * @param string $severity
   *   Message severity.
   * @param int $row_ct
   *   Number of rows to return.
   *
   * @return array
   *   Associative array containing messages.
   */
  protected function getLastWatchdogRowsBySeverity($severity, $row_ct = 1) {
    $query = $this->container->get('database')->select('watchdog', 'w');
    $query->fields('w', ['message']);
    $query->condition('w.type', 'my_testing_module');
    $query->condition('w.severity', $severity);
    $query->orderBy('w.timestamp', 'DESC');
    $query->range(0, $row_ct);
    return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Tests admin user messages are properly logged, if logging enabled.
   *
   * @covers \Drupal\my_testing_module\Controller\MyMessageController::displayMessage
   */
  public function testGetMessageForAdminUserLogsMessagesWhenSet() {
    // Enable logging.
    $this->config(MyMessageController::SETTINGS)
      ->set('log_users', TRUE)
      ->save();

      // Setup Admin User (UID=1).
    $this->setUpCurrentUser(['uid' => 1]);
    $controller = MyMessageController::create($this->container);

    // Confirm response.
    $expected = [
      '#markup' => implode('<br>', [
        'You are logged in.',
        'You are special.',
        'You have yet another privilege.',
      ]),
    ];
    $this->assertEquals($expected, $controller->displayMessage());

    // Confirm log messages.
    $log_messages = $this->getLastWatchdogRowsBySeverity(RfcLogLevel::INFO, 3);
    $expected = [
      ['message' => 'super secret privilege granted'],
      ['message' => 'yet another privilege granted'],
    ];
    $this->assertEquals($expected, $log_messages);
  }

  /**
   * Tests admin user messages are properly logged, if logging enabled.
   *
   * @covers \Drupal\my_testing_module\Controller\MyMessageController::displayMessage
   */
  public function testGetMessageForAdminUserLogsMessagesWhenNotSet() {
    // Enable logging.
    $this->config(MyMessageController::SETTINGS)
      ->set('log_users', FALSE)
      ->save();

      // Setup Admin User (UID=1).
    $this->setUpCurrentUser(['uid' => 1]);
    $controller = MyMessageController::create($this->container);

    // Confirm response.
    $expected = [
      '#markup' => implode('<br>', [
        'You are logged in.',
        'You are special.',
        'You have yet another privilege.',
      ]),
    ];
    $this->assertEquals($expected, $controller->displayMessage());

    // Confirm log messages.
    $log_messages = $this->getLastWatchdogRowsBySeverity(RfcLogLevel::INFO, 3);
    $expected = [];
    $this->assertEquals($expected, $log_messages);
  }

  /**
   * Tests super secret user messages are properly logged, if logging enabled.
   *
   * @covers \Drupal\my_testing_module\Controller\MyMessageController::displayMessage
   */
  public function testGetMessageForMySuperSecretUserLogsMessagesWhenSet() {
    // Enable logging.
    $this->config(MyMessageController::SETTINGS)
      ->set('log_users', TRUE)
      ->save();

    // Setup user w/ expected privilege
    $this->setUpCurrentUser(['uid' => 2], ['my super secret privilege']);
    $controller = MyMessageController::create($this->container);

    // Confirm response.
    $expected = [
      '#markup' => implode('<br>', [
        'You are logged in.',
        'You are special.',
      ]),
    ];
    $this->assertEquals($expected, $controller->displayMessage());

    // Confirm log messages.
    $log_messages = $this->getLastWatchdogRowsBySeverity(RfcLogLevel::INFO, 3);
    $expected = [
      ['message' => 'super secret privilege granted'],
    ];
    $this->assertEquals($expected, $log_messages);
  }

  /**
   * Tests super secret user messages are properly logged, if logging enabled.
   *
   * @covers \Drupal\my_testing_module\Controller\MyMessageController::displayMessage
   */
  public function testGetMessageForMySuperSecretUserLogsMessagesWhenNotSet() {
    // Enable logging.
    $this->config(MyMessageController::SETTINGS)
      ->set('log_users', FALSE)
      ->save();

    // Setup user w/ expected privilege
    $this->setUpCurrentUser(['uid' => 2], ['my super secret privilege']);
    $controller = MyMessageController::create($this->container);

    // Confirm response.
    $expected = [
      '#markup' => implode('<br>', [
        'You are logged in.',
        'You are special.',
      ]),
    ];
    $this->assertEquals($expected, $controller->displayMessage());

    // Confirm log messages.
    $log_messages = $this->getLastWatchdogRowsBySeverity(RfcLogLevel::INFO, 3);
    $expected = [];
    $this->assertEquals($expected, $log_messages);
  }

  /**
   * Tests yet another priv user messages are properly logged, if logging enabled.
   *
   * @covers \Drupal\my_testing_module\Controller\MyMessageController::displayMessage
   */
  public function testGetMessageForYetAnotherPrivilegeUserLogsMessagesWhenSet() {
    // Enable logging.
    $this->config(MyMessageController::SETTINGS)
      ->set('log_users', TRUE)
      ->save();

    // Setup user w/ expected privilege
    $this->setUpCurrentUser(['uid' => 3], ['yet another privilege']);
    $controller = MyMessageController::create($this->container);

    // Confirm response.
    $expected = [
      '#markup' => implode('<br>', [
        'You are logged in.',
        'You have yet another privilege.',
      ]),
    ];
    $this->assertEquals($expected, $controller->displayMessage());

    // Confirm log messages.
    $log_messages = $this->getLastWatchdogRowsBySeverity(RfcLogLevel::INFO, 3);
    $expected = [
      ['message' => 'yet another privilege granted'],
    ];
    $this->assertEquals($expected, $log_messages);
  }

  /**
   * Tests yet another priv user messages are properly logged, if logging enabled.
   *
   * @covers \Drupal\my_testing_module\Controller\MyMessageController::displayMessage
   */
  public function testGetMessageForYetAnotherPrivilegeUserLogsMessagesWhenNotSet() {
    // Enable logging.
    $this->config(MyMessageController::SETTINGS)
      ->set('log_users', FALSE)
      ->save();

    // Setup user w/ expected privilege
    $this->setUpCurrentUser(['uid' => 3], ['yet another privilege']);
    $controller = MyMessageController::create($this->container);

    // Confirm response.
    $expected = [
      '#markup' => implode('<br>', [
        'You are logged in.',
        'You have yet another privilege.',
      ]),
    ];
    $this->assertEquals($expected, $controller->displayMessage());

    // Confirm log messages.
    $log_messages = $this->getLastWatchdogRowsBySeverity(RfcLogLevel::INFO, 3);
    $expected = [];
    $this->assertEquals($expected, $log_messages);
  }

  /**
   * Tests unprivileged user messages are properly logged, if logging enabled.
   *
   * @covers \Drupal\my_testing_module\Controller\MyMessageController::displayMessage
   */
  public function testGetMessageForUnprivilegeUserLogsMessagesWhenSet() {
    // Enable logging.
    $this->config(MyMessageController::SETTINGS)
      ->set('log_users', TRUE)
      ->save();

    // Setup user w/ expected privilege
    $this->setUpCurrentUser(['uid' => 4]);
    $controller = MyMessageController::create($this->container);

    // Confirm response.
    $expected = [
      '#markup' => implode('<br>', [
        'You are logged in.',
      ]),
    ];
    $this->assertEquals($expected, $controller->displayMessage());

    // Confirm log messages.
    $log_messages = $this->getLastWatchdogRowsBySeverity(RfcLogLevel::WARNING, 3);
    $expected = [
      ['message' => 'unprivileged access'],
    ];
    $this->assertEquals($expected, $log_messages);
  }

  /**
   * Tests unprivileged user messages are properly logged, if logging enabled.
   *
   * @covers \Drupal\my_testing_module\Controller\MyMessageController::displayMessage
   */
  public function testGetMessageForUnprivilegedUserLogsMessagesWhenNotSet() {
    // Enable logging.
    $this->config(MyMessageController::SETTINGS)
      ->set('log_users', FALSE)
      ->save();

    // Setup user w/ expected privilege
    $this->setUpCurrentUser(['uid' => 4], []);
    $controller = MyMessageController::create($this->container);

    // Confirm response.
    $expected = [
      '#markup' => implode('<br>', [
        'You are logged in.',
      ]),
    ];
    $this->assertEquals($expected, $controller->displayMessage());

    // Confirm log messages.
    $log_messages = $this->getLastWatchdogRowsBySeverity(RfcLogLevel::WARNING, 3);
    $expected = [];
    $this->assertEquals($expected, $log_messages);
  }

}
