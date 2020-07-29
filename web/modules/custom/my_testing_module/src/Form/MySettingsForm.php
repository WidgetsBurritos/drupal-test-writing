<?php

namespace Drupal\my_testing_module\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * My settings form.
 */
class MySettingsForm extends ConfigFormBase {

  const SETTINGS = 'my_testing_module.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'my_testing_module_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);
    $form['log_users'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Log users?'),
      '#default_value' => $config->get('log_users') ?: FALSE,
      '#attached' => [
        'library' => [
          'my_testing_module/my_message_logging',
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('log_users', $form_state->getValue('log_users'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
