<?php

namespace Drupal\test\Form;

use Drupal\system\Form\SiteInformationForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Component\Utility\Random;
Use Drupal\Core\Messenger\MessengerTrait;

/**
 * Configure site information settings for this site.
 */
class ApiKeySiteInformationForm extends SiteInformationForm {
  
  use MessengerTrait;
  
  /**
   * @var Drupal\Component\Utility\Random $generator.
   */
  protected $generator;

  /**
   * Gets the random generator for the utility methods.
   *
   * @return \Drupal\Component\Utility\Random
   *   The random generator.
   */
  protected function getRandomGenerator() {
    if (!is_object($this->generator)) {
      $this->generator = new Random();
    }
    return $this->generator;
  }

  /**
   * {@inheritdoc}
   */
  function buildForm(array $form, FormStateInterface $form_state) {
    $site_config = $this->config('system.site');
    $form = parent::buildForm($form, $form_state);
    
    $form['site_information']['generateapikey'] = [
      '#type' => 'button',
      '#value' => $this->t('Generate API Key'),
      '#ajax' => [
        'callback' => '::randomGeneratorCallback',
        'wrapper' => 'edit-random-string',
        'effect' => 'fade',
      ],
    ];
    
    $form['site_information']['random_string'] = [
      '#type' => 'item',
      '#markup' => $this->t('Click this button to unique randomly generated string.'),
    ];
    
    $form['site_information']['apikey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site API Key'),
      '#description' => $this->t('Enter an alphanumeric api key that can be used to access this site.'),
      '#placeholder' => $this->t('No API Key yet'),
      '#required' => TRUE,
      '#default_value' => $site_config->get('siteapikey'),
      '#weight' => 1,
    ];
    
    $form['actions']['submit']['#value'] = $this->t('Update Configuration');
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('system.site')
      ->set('siteapikey', $form_state->getValue('apikey'))
      ->save();

    $this->messenger()->addStatus($this->t('Site API Key has been saved: @api_key', ['@api_key' => $form_state->getValue('apikey')]));
    parent::submitForm($form, $form_state);
  }
  
  /**
   * Callback for ajax button.
   * 
   * Returns a randomly generated string.
   */
  public function randomGeneratorCallback($form, FormStateInterface $form_state) {
    $form['site_information']['random_string']['#markup'] = "<div class='description small'>" . $this->getRandomGenerator()->name(30, TRUE) . "</div>"; 
    return $form['site_information']['random_string'];
  }
}