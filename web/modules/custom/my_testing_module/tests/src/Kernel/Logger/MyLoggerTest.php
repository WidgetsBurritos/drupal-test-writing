<?php

namespace Drupal\Tests\my_testing_module\Kernel\Logger;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the functionality of the logging service.
 *
 * @group my_testing_module
 */
class MyLoggerTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['my_testing_module', 'dblog'];

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
   * @return array
   *   Associative array containing messages and variables.
   */
  protected function getLastWatchdogRowBySeverity($severity) {
    $query = $this->container->get('database')->select('watchdog', 'w');
    $query->fields('w', ['message', 'variables']);
    $query->condition('w.type', 'my_testing_module');
    $query->condition('w.severity', $severity);
    $query->orderBy('w.timestamp', 'DESC');
    $query->range(0, 1);
    return $query->execute()->fetchAssoc();
  }

  /**
   * Tests logging service functions.
   */
  public function testLogMessages() {
    $logger = $this->container->get('my_testing_module.my_logger');

    // Look for Alert message.
    $logger->alert('This is an alert, @name', ['@name' => 'Drupal']);
    $expected = [
      'message' => 'This is an alert, @name',
      'variables' => serialize(['@name' => 'Drupal']),
    ];
    $this->assertEquals($expected, $this->getLastWatchdogRowBySeverity(RfcLogLevel::ALERT));

    // Look for Critical message.
    $logger->critical('This is critical, @name', ['@name' => 'Drupal']);
    $expected = [
      'message' => 'This is critical, @name',
      'variables' => serialize(['@name' => 'Drupal']),
    ];
    $this->assertEquals($expected, $this->getLastWatchdogRowBySeverity(RfcLogLevel::CRITICAL));

    // Look for Debug message.
    $logger->debug('This is a debug message, @name', ['@name' => 'Drupal']);
    $expected = [
      'message' => 'This is a debug message, @name',
      'variables' => serialize(['@name' => 'Drupal']),
    ];
    $this->assertEquals($expected, $this->getLastWatchdogRowBySeverity(RfcLogLevel::DEBUG));

    // Look for Emergency message.
    $logger->emergency('@name, we have a problem!', ['@name' => 'Houston']);
    $expected = [
      'message' => '@name, we have a problem!',
      'variables' => serialize(['@name' => 'Houston']),
    ];
    $this->assertEquals($expected, $this->getLastWatchdogRowBySeverity(RfcLogLevel::EMERGENCY));

    // Look for Error message.
    $logger->error('Ruh Roh, @name', ['@name' => 'Raggy']);
    $expected = [
      'message' => 'Ruh Roh, @name',
      'variables' => serialize(['@name' => 'Raggy']),
    ];
    $this->assertEquals($expected, $this->getLastWatchdogRowBySeverity(RfcLogLevel::ERROR));

    // Look for Info message.
    $logger->info('FYI, @name. Here is some information.', ['@name' => 'Drupal']);
    $expected = [
      'message' => 'FYI, @name. Here is some information.',
      'variables' => serialize(['@name' => 'Drupal']),
    ];
    $this->assertEquals($expected, $this->getLastWatchdogRowBySeverity(RfcLogLevel::INFO));

    // Look for Notice message.
    $logger->notice('Putting you on notice, @name', ['@name' => 'Drupal']);
    $expected = [
      'message' => 'Putting you on notice, @name',
      'variables' => serialize(['@name' => 'Drupal']),
    ];
    $this->assertEquals($expected, $this->getLastWatchdogRowBySeverity(RfcLogLevel::NOTICE));

    // Look for Warning message.
    $logger->warning('Danger @name!', ['@name' => 'Will Robinson']);
    $expected = [
      'message' => 'Danger @name!',
      'variables' => serialize(['@name' => 'Will Robinson']),
    ];
    $this->assertEquals($expected, $this->getLastWatchdogRowBySeverity(RfcLogLevel::WARNING));
  }

}
