<?php

use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Context\DrupalContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends DrupalContext {

  /**
   * Asserts that the specified cache tag is present.
   *
   * @Then the cache tag :tag is present
   */
  public function theCacheTagIsPresent($tag) {
    $this->assertSession()->responseHeaderMatches('X-Drupal-Cache-Tags', $tag);
  }

  /**
   * Asserts that the specified cache tag is not present.
   *
   * @Then the cache tag :tag is not present
   */
  public function theCacheTagIsNotPresent($tag) {
    $this->assertSession()->responseHeaderNotMatches('X-Drupal-Cache-Tags', $tag);
  }

  /**
   * Asserts that the specified cache context is present.
   *
   * @Then the cache context :context is present
   */
  public function theCacheContextIsPresent($context) {
    $this->assertSession()->responseHeaderMatches('X-Drupal-Cache-Contexts', $context);
  }

  /**
   * Asserts that the specified cache context is not present.
   *
   * @Then the cache context :context is not present
   */
  public function theCacheContextIsNotPresent($context) {
    $this->assertSession()->responseHeaderNotMatches('X-Drupal-Cache-Contexts', $context);
  }

}
