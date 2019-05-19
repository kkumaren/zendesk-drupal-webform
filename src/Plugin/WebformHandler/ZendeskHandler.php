<?php
/**
 * Created by PhpStorm.
 * User: Steven
 * Date: 2019-05-18
 * Time: 10:05 AM
 */

namespace Drupal\zendesk_webform\Plugin\WebformHandler;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\zendesk_webform\Client\ZendeskClient;


/**
 * Form submission to Zendesk handler.
 *
 * @WebformHandler(
 *   id = "zendesk",
 *   label = @Translation("Zendesk"),
 *   category = @Translation("Zendesk"),
 *   description = @Translation("Sends a form submission to Zendesk to create a support ticket."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */
class ZendeskHandler extends WebformHandlerBase
{
    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration()
    {
        return [
            'subdomain' => '',
            'email' => '',
            'name' => '',
            'subject' => '',
            'message' => '',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildConfigurationForm(array $form, FormStateInterface $form_state)
    {
        /*$form['external_id'] = [
            '#type' => 'integer',
            '#title' => $this->t('Requester email address'),
            '#description' => $this->t(''),
            //'#value' => $form_state->form_id,
            '#disabled' => true
        ];*/

        $form['requester'] = [
            '#type' => 'email',
            '#title' => $this->t('Requester email address'),
            '#description' => $this->t(''),
            '#required' => true
        ];

        $form['subject'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Subject'),
            '#description' => $this->t(''),
            '#required' => true
        ];

        $form['comment'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Ticket Body'),
            '#description' => $this->t(''),
            '#required' => true
        ];

        $form['tags'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Ticket Tags'),
            '#description' => $this->t(''),
            '#default_value' => '',
            '#multiple' => true,
            '#required' => false
        ];

        $form['priority'] = [
            '#type' => 'select',
            '#title' => $this->t('Ticket Priority'),
            '#description' => $this->t(''),
            '#default_value' => '',
            '#options' => [
                '',
                'low',
                'normal',
                'high',
                'urgent'
            ],
            '#required' => false
        ];

        $form['status'] = [
            '#type' => 'select',
            '#title' => $this->t('Ticket Status'),
            '#description' => $this->t(''),
            '#default_value' => '',
            '#options' => [
                '',
                'new',
                'open',
                'pending',
                'hold',
                'solved',
                'closed'
            ],
            '#required' => false
        ];

        $form['collaborators'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Ticket CCs'),
            '#description' => $this->t(''),
            '#default_value' => '',
            '#multiple' => true,
            '#required' => false
        ];

        $form['token_tree_link'] = $this->tokenManager->buildTreeLink();

        return parent::buildConfigurationForm($form, $form_state); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission)
    {
        parent::submitForm($form, $form_state, $webform_submission); // TODO: Change the autogenerated stub

        $webform = $webform_submission->getData();
        $config = $this->configuration;

        $request = [
            'subject' => 'test 1 ticket',
            'requester' => [
                'email' => 'scsisland@gmail.com'
            ],
            'comment' => [
                'body' => 'this is a test tickets'
            ],
            'tags' => [],
            'priority' => 'low',
            'status' => 'new',
            'collaborators' => [],
        ];

        try {
            $client = new ZendeskClient();
            $ticket = $client->tickets()->create($request);
        }
        catch( \Exception $e ){

            $message = $e->getMessage();

            // Encode HTML entities to prevent broken markup from breaking the page.
            $message = nl2br(htmlentities($message));

            // Log error message.
            $context = [
                '@exception' => get_class($e),
                '@form' => $this->getWebform()->label(),
                '@state' => '??',
                '@message' => $message,
                'link' => $this->getWebform()->toLink($this->t('Edit'), 'handlers')->toString(),
            ];
            $this->getLogger()->error('@form webform submission to zendesk failed. @exception: @message', $context);
        }

    }

    public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
    {
        parent::submitConfigurationForm($form, $form_state); // TODO: Change the autogenerated stub
    }

    public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE)
    {
        parent::postSave($webform_submission, $update); // TODO: Change the autogenerated stub
    }

    public function getSummary()
    {
        return parent::getSummary(); // TODO: Change the autogenerated stub
    }
}