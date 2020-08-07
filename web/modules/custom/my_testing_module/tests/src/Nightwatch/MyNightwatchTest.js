module.exports = {
  '@tags': ['my_testing_module'],
  before: function(browser) {
    browser
      .drupalInstall({
        setupFile: __dirname + '/fixtures/TestSiteInstallTestScript.php',
      });
  },
  after: function(browser) {
    browser
      .drupalUninstall();
  },
  'Visit the message module settings and toggle log settings': (browser) => {
    // Navigate to module admin setting.
    browser
      .drupalLoginAsAdmin()
      .drupalRelativeURL('/admin/config/system/my_testing_module/settings');

    // Confirm label contains correct CSS class.
    browser
      .assert.cssClassPresent('label[for=edit-log-users]', 'my-unchecked-class')
      .assert.not.cssClassPresent('label[for=edit-log-users]', 'my-checked-class')
      .execute(function() {
          return Drupal.myMessageLogging.myCheckbox.checked;
        }, [], function (result) {
          browser.assert.strictEqual(result.value, false);
        });

    // Toggle the checkbox state.
    browser
      .click('input[name=log_users]');

    // Confirm label contains correct CSS class.
    browser
      .assert.not.cssClassPresent('label[for=edit-log-users]', 'my-unchecked-class')
      .assert.cssClassPresent('label[for=edit-log-users]', 'my-checked-class')
      .execute(function() {
          return Drupal.myMessageLogging.myCheckbox.checked;
        }, [], function (result) {
          browser.assert.strictEqual(result.value, true);
        });

    // Toggle the checkbox state again.
    browser
      .click('input[name=log_users]');

    // Confirm label contains correct CSS class.
    browser
      .assert.cssClassPresent('label[for=edit-log-users]', 'my-unchecked-class')
      .assert.not.cssClassPresent('label[for=edit-log-users]', 'my-checked-class')
      .execute(function() {
          return Drupal.myMessageLogging.myCheckbox.checked;
        }, [], function (result) {
          browser.assert.strictEqual(result.value, false);
        });

    // Clean up our session.
    browser
      .end();
  }
  // 'Visit the home page and ensure "Skip to main content" is present': (browser) => {
  //   browser
  //     .drupalRelativeURL('/')
  //     .assert.containsText('body', 'Skip to main content')
  //     .end();
  // },


};
