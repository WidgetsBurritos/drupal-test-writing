/**
 * @file
 * Provides some nonsense javascript functionality for our nonsense module.
 */

(function (Drupal, drupalSettings) {
  'use strict';

  Drupal.myMessageLogging = Drupal.myMessageLogging || {};
  Drupal.myMessageLogging.myCheckbox = null;
  Drupal.myMessageLogging.isChecked = null;

  /**
   * Writes to console indicating whether or not checkbox is checked.
   */
  Drupal.myMessageLogging.setCheckboxMessage = function() {
    if (Drupal.myMessageLogging.myCheckbox.checked) {
      Drupal.myMessageLogging.myLabel.classList.add('my-checked-class');
      Drupal.myMessageLogging.myLabel.classList.remove('my-unchecked-class');
    }
    else {
      Drupal.myMessageLogging.myLabel.classList.remove('my-checked-class');
      Drupal.myMessageLogging.myLabel.classList.add('my-unchecked-class');
    }
  }

  /**
   * Attaches the necessary chartjs functionality just once.
   */
  Drupal.behaviors.myMessageLogging = {
    attach: function attach(context) {
      if (!Drupal.myMessageLogging.myCheckbox) {
        Drupal.myMessageLogging.myCheckbox = document.querySelector('input[name=log_users]');
        Drupal.myMessageLogging.myLabel = document.querySelector('label[for=edit-log-users]');
        // Set initial checkbox message.
        Drupal.myMessageLogging.setCheckboxMessage();
        // Set checkbox toggle.
        Drupal.myMessageLogging.myCheckbox.addEventListener('change', Drupal.myMessageLogging.setCheckboxMessage);
      }
    }
  };


})(Drupal, drupalSettings);
