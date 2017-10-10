<?php

namespace Frontend\Modules\Mailmotor\EventListener;

use Frontend\Core\Language\Locale;
use Frontend\Modules\FormBuilder\Event\FormBuilderSubmittedEvent;
use Common\ModulesSettings;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;
use MailMotor\Bundle\MailMotorBundle\Helper\Subscriber;

/**
 * Subscribe from formbuilder submitted form
 */
final class SubscribeFromFormBuilderSubmittedForm
{
    /**
     * @var ModulesSettings
     */
    private $modulesSettings;

    /**
     * @var Subscriber
     */
    private $subscriber;

    public function __construct(Subscriber $subscriber, ModulesSettings $modulesSettings)
    {
        $this->subscriber = $subscriber;
        $this->modulesSettings = $modulesSettings;
    }

    public function onFormBuilderSubmittedEvent(FormBuilderSubmittedEvent $event): void
    {
        if (!$this->modulesSettings->get(
            'Mailmotor',
            'automatically_subscribe_from_form_builder_submitted_form',
            false
        )) {
            return;
        }
        $form = $event->getForm();
        $data = $event->getData();
        $email = null;

        // Check if we have a replyTo email set
        foreach ($form['fields'] as $field) {
            if (array_key_exists('reply_to', $field['settings']) &&
                $field['settings']['reply_to'] === true
            ) {
                $email = unserialize($data[$field['id']]['value'], ['allowed_classes' => false]);
            }
        }

        $language = $form['language'] ?? $this->modulesSettings->get('Core', 'default_language', 'en');

        // We subscribe the replyTo email
        try {
            if (!$this->subscriber->exists($email)) {
                // @TODO check if we are allowed to do this like this according to GDPR
                $this->subscriber->subscribe(
                    $email,
                    $language,
                    [],
                    [],
                    false, // will ignore double-optin and so subscribes the user immediately
                    $this->modulesSettings->get('Mailmotor', 'list_id_' . Locale::frontendLanguage())
                );
            }
        } catch (NotImplementedException $e) {
            // We do nothing as fallback when no mail-engine is chosen in the Backend
        }
    }
}
