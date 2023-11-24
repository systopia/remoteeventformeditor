<?php
declare(strict_types = 1);

// phpcs:disable PSR1.Files.SideEffects
require_once 'remoteeventformeditor.civix.php';
// phpcs:enable

use Civi\RemoteEventFormEditor\Api4\OptionsLoader;
use Civi\RemoteEventFormEditor\EventSubscriber\RegisterFormEditorProfileSubscriber;
use Civi\RemoteEventFormEditor\EventSubscriber\UpdateParticipantDataSubscriber;
use Civi\RemoteEventFormEditor\FieldType\FieldTypeContainer;
use Civi\RemoteEventFormEditor\FieldType\FieldTypeGroupContainer;
use Civi\RemoteEventFormEditor\FieldType\Loader\ContactCustomFieldTypeLoader;
use Civi\RemoteEventFormEditor\FieldType\Loader\ContactFieldTypeGroupLoader;
use Civi\RemoteEventFormEditor\FieldType\Loader\ContactFieldTypeLoader;
use Civi\RemoteEventFormEditor\FieldType\Loader\GenericFieldTypeGroupLoader;
use Civi\RemoteEventFormEditor\FieldType\Loader\GenericFieldTypeLoader;
use Civi\RemoteEventFormEditor\FieldType\Loader\ParticipantCustomFieldTypeLoader;
use Civi\RemoteEventFormEditor\FieldType\Loader\ParticipantFieldTypeGroupLoader;
use Civi\RemoteEventFormEditor\OptionsLoaderInterface;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\ConcreteProfileFormFieldFactoryInterface;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\FieldFactory\AddressFormFieldFactory;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\FieldFactory\DefaultFormFieldFactory;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\FieldFactory\FieldsetFormFieldFactory;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\FieldFactory\HiddenFormFieldFactory;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\FieldFactory\OptionGroupFormFieldFactory;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\ProfileFormFieldFactory;
use CRM_RemoteEventFormEditor_ExtensionUtil as E;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

function _remoteeventformeditor_composer_autoload(): void {
  if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    $classLoader = require_once __DIR__ . '/vendor/autoload.php';
    if ($classLoader instanceof \Composer\Autoload\ClassLoader) {
      // Re-register class loader to append it. (It's automatically prepended.)
      $classLoader->unregister();
      $classLoader->register();
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function remoteeventformeditor_civicrm_config(&$config) {
  _remoteeventformeditor_composer_autoload();
  _remoteeventformeditor_civix_civicrm_config($config);
}

function remoteeventformeditor_civicrm_container(ContainerBuilder $container): void {
  _remoteeventformeditor_composer_autoload();

  // Rebuild container if this file changes.
  $container->addResource(new FileResource(__FILE__));

  $container->register('remote_event_form_editor.logger', LoggerInterface::class)
    ->setFactory([\Civi::class, 'log'])
    ->addArgument(E::SHORT_NAME);

  $container->autowire(OptionsLoaderInterface::class, OptionsLoader::class);

  $container->autowire(RegisterFormEditorProfileSubscriber::class)
    ->addTag('kernel.event_subscriber');

  $container->autowire(ProfileFormFieldFactory::class)
    ->addArgument(new TaggedIteratorArgument(ConcreteProfileFormFieldFactoryInterface::SERVICE_TAG))
    ->setPublic(TRUE);

  $container->autowire(AddressFormFieldFactory::class)
    ->addTag(ConcreteProfileFormFieldFactoryInterface::SERVICE_TAG);
  $container->autowire(DefaultFormFieldFactory::class)
    ->addTag(ConcreteProfileFormFieldFactoryInterface::SERVICE_TAG);
  $container->autowire(FieldsetFormFieldFactory::class)
    ->addTag(ConcreteProfileFormFieldFactoryInterface::SERVICE_TAG);
  $container->autowire(HiddenFormFieldFactory::class)
    ->addTag(ConcreteProfileFormFieldFactoryInterface::SERVICE_TAG);
  $container->autowire(OptionGroupFormFieldFactory::class)
    ->addTag(ConcreteProfileFormFieldFactoryInterface::SERVICE_TAG);

  $container->autowire(GenericFieldTypeLoader::class)
    ->addTag('remote_event_form_editor.editor_field_type_loader.generic');
  $container->autowire(ContactFieldTypeLoader::class)
    ->addTag('remote_event_form_editor.editor_field_type_loader.contact');
  $container->autowire(ContactCustomFieldTypeLoader::class)
    ->setArgument('$logger', new Reference('remote_event_form_editor.logger'))
    ->addTag('remote_event_form_editor.editor_field_type_loader.contact');
  $container->autowire(ParticipantCustomFieldTypeLoader::class)
    ->setArgument('$logger', new Reference('remote_event_form_editor.logger'))
    ->addTag('remote_event_form_editor.editor_field_type_loader.participant');

  $container->register(GenericFieldTypeGroupLoader::class)
    ->addArgument(new TaggedIteratorArgument('remote_event_form_editor.editor_field_type_loader.generic'))
    ->addTag('remote_event_form_editor.field_type_group_loader', ['priority' => -10]);
  $container->register(ContactFieldTypeGroupLoader::class)
    ->addArgument(new TaggedIteratorArgument('remote_event_form_editor.editor_field_type_loader.contact'))
    ->addTag('remote_event_form_editor.field_type_group_loader', ['priority' => 20]);
  $container->register(ParticipantFieldTypeGroupLoader::class)
    ->addArgument(new TaggedIteratorArgument('remote_event_form_editor.editor_field_type_loader.participant'))
    ->addTag('remote_event_form_editor.field_type_group_loader', ['priority' => 10]);

  $container->register(FieldTypeGroupContainer::class)
    ->addArgument(new TaggedIteratorArgument('remote_event_form_editor.field_type_group_loader'));
  $container->autowire(FieldTypeContainer::class);

  $container->autowire(\Civi\RemoteEventFormEditor\Api4\Action\RemoteEventFormEditorFieldTypes\GetAction::class)
    ->setPublic(TRUE)
    ->setShared(FALSE);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function remoteeventformeditor_civicrm_install() {
  _remoteeventformeditor_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function remoteeventformeditor_civicrm_enable() {
  _remoteeventformeditor_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function remoteeventformeditor_civicrm_preProcess($formName, &$form) {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function remoteeventformeditor_civicrm_navigationMenu(&$menu) {
//  _remoteeventformeditor_civix_insert_navigation_menu($menu, 'Mailings', [
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ]);
//  _remoteeventformeditor_civix_navigationMenu($menu);
//}
