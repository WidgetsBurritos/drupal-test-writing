module.exports = {
  '@tags': ['my_testing_module'],
  before: function(browser) {
    browser
      .drupalInstall();
  },
  after: function(browser) {
    browser
      .drupalUninstall();
  },
  'Visit the home page and ensure "Skip to main content" is present': (browser) => {
    browser
      .drupalRelativeURL('/')
      .assert.containsText('body', 'Skip to main content')
      .end();
  },

};
