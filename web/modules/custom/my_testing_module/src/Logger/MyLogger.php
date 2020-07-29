<?php

namespace Drupal\my_testing_module\Logger;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\RfcLoggerTrait;
use Psr\Log\LoggerInterface;

/**
 * Provides helper methods for logging needs.
 */
class MyLogger implements LoggerInterface {

  use RfcLoggerTrait;

  /**
   * The logger channel.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a MyLogger object.
   */
  public function __construct(LoggerChannelFactoryInterface $logger_channel_factory) {
    $this->logger = $logger_channel_factory->get('my_testing_module');
  }

  /**
   * Log a message.
   *
   * @param string $severity
   *   Severity of our message.
   * @param string $message
   *   Message to insert into log.
   * @param array $context
   *   Log context.
   */
  public function log($severity, $message, array $context = []) {
    $this->logger->log($severity, $message, $context);
  }

}
