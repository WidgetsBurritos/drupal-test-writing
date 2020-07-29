<?php

namespace Drupal\my_testing_module\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Controller for custom messages.
 */
class MyMessageController extends ControllerBase implements ContainerInjectionInterface {

  const SETTINGS = 'my_testing_module.settings';

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * Boolean indicating whether or not we're to log user behavior.
   *
   * @var bool
   */
  protected $logUsers;

  /**
   * Logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a new MyMessageController.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   Current user.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory service.
   * @param \Psr\Log\LoggerInterface $logger
   *   Logger service.
   */
  public function __construct(AccountInterface $user, ConfigFactoryInterface $config_factory, LoggerInterface $logger) {
    $this->user = $user;
    $this->logUsers = $config_factory->get(static::SETTINGS)->get('log_users') ?: FALSE;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user')->getAccount(),
      $container->get('config.factory'),
      $container->get('my_testing_module.my_logger')
    );
  }

  /**
   * Adds log message if applicable, otherwise does nothing.
   *
   * @param string $severity
   *   Severity of our message.
   * @param string $message
   *   Message to insert into log.
   */
  private function log($severity, $message) {
    if ($this->logUsers) {
      $this->logger->log($severity, $message);
    }
    return $this->logUsers;
  }

  /**
   * Retrieves the message for the specified user.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   User account.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   User message.
   */
  public function getMessageForUser(AccountInterface $user) {
    if ($user->hasPermission('my super secret privilege')) {
      $this->log('info', 'super secret privilege granted');
      return $this->t("You aren't all that special.");
    }
    elseif ($user->hasPermission('yet another privilege')) {
      $this->log('info', 'yet another privilege granted');
      return $this->t('You have yet another privilege.');
    }
    else {
      $this->log('warning', 'unprivileged access');
    }
    return $this->t('You might be logged in.');
  }

  /**
   * Renders modal content.
   */
  public function displayMessage() {
    return [
      '#markup' => $this->getMessageForUser($this->user),
    ];
  }

  /**
   * Returns the title of the route.
   */
  public function title() {
    return $this->t('Hi @user.', ['@user' => $this->user->getDisplayName()]);
  }

}
