<?php

namespace Drupal\Tests\my_testing_module\Unit\Controller;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Tests\UnitTestCase;
use Drupal\my_testing_module\Controller\MyMessageController;

/**
 * @coversDefaultClass \Drupal\my_testing_module\Controller\MyMessageController
 *
 * @group my_testing_module
 */
class MyMessageControllerTest extends UnitTestCase {

  use StringTranslationTrait;

  /**
   * The current user.
   *
   * @var \Drupal\user\Entity\UserInterface
   */
  protected $user;

  /**
   * Mock logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    // Mock the current user.
    $this->user = $this->getMockBuilder('\Drupal\Core\Session\AccountInterface')
      ->getMock();
    $this->user->expects($this->any())
      ->method('getDisplayName')
      ->will($this->returnValue('John Doe'));

    // Mock the config factory.
    $config = $this->getMockBuilder('\Drupal\Core\Config\ImmutableConfig')
      ->disableOriginalConstructor()
      ->getMock();
    $config->expects($this->any())
      ->method('get')
      ->will($this->returnValue(FALSE));
    $this->config_factory = $this->getMockBuilder('\Drupal\Core\Config\ConfigFactoryInterface')
      ->getMock();
    $this->config_factory->expects($this->any())
      ->method('get')
      ->will($this->returnValue($config));

    // Mock the logger service.
    $this->logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')
      ->getMock();

    // Stub string translation service.
    $this->setStringTranslation($this->getStringTranslationStub());
  }

  /**
   * Confirm controller title is showing the correct user.
   *
   * @covers \Drupal\my_testing_module\Controller\MyMessageController::title
   */
  public function testTitleShowsCurrentUser() {
    $controller = new MyMessageController($this->user, $this->config_factory, $this->logger);
    $controller->setStringTranslation($this->getStringTranslationStub());
    $expected = $this->t('Hi @user.', ['@user' => 'John Doe']);
    $this->assertEquals($expected, $controller->title());
  }

}
