<?php
/*-------------------------------------------------------+
| SYSTOPIA Remote Event Extension                        |
| Copyright (C) 2022 SYSTOPIA                            |
| Author: P. Batroff (batroff@systopia.de)               |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

declare(strict_types = 1);

namespace Civi\RemoteEventFormEditor\RegistrationProfile;

use Civi\RemoteEventFormEditor\RegistrationProfile\Form\ProfileFormFieldFactory;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\Util\FormFieldNameUtil;
use Civi\RemoteParticipant\Event\GetParticipantFormEventBase;
use CRM_Remoteevent_RegistrationProfile;

/**
 * @phpstan-type formT array{
 *   id: int,
 *   identifier: string,
 *   name: string,
 *   fields: array<array<string, mixed>>
 * }
 * Values returned by RemoteEventFormEditorForm::get().
 *
 * @see RemoteEventFormEditorForm::get()
 */
final class FormEditorRegistrationProfile extends CRM_Remoteevent_RegistrationProfile {

  /**
   * Abbreviation of form builder.
   */
  public const ID_PREFIX = 'fb';

  public const FULL_ID_PREFIX = self::ID_PREFIX . '-';

  /**
   * @phpstan-var formT
   */
  private array $form;

  private ProfileFormFieldFactory $profileFormFieldFactory;

  /**
   * @phpstan-var array<string, array<string, mixed>>
   */
  private array $profileFormFields = [];

  /**
   * @phpstan-var array<string, array<string, mixed>>
   */
  private array $profileHiddenFormFields = [];

  /**
   * @phpstan-param formT $form
   */
  public function __construct(array $form) {
    $this->form = $form;

    // @phpstan-ignore-next-line
    $this->profileFormFieldFactory = \Civi::service(ProfileFormFieldFactory::class);
  }

  public function getId(): int {
    return $this->form['id'];
  }

  /**
   * @inheritDoc
   */
  public function getName(): string {
    return $this->form['identifier'];
  }

  /**
   * @inheritDoc
   */
  public function getLabel(): string {
    return $this->form['name'];
  }

  /**
   * @inheritDoc
   */
  public function addDefaultValues(GetParticipantFormEventBase $resultsEvent): void {
    $fieldNameMap = $this->getContactFieldNameMap();
    $this->addDefaultContactValues($resultsEvent, array_keys($fieldNameMap), $fieldNameMap);
    // @todo: Address fields
  }

  /**
   * @inheritDoc
   *
   * @phpstan-return array<string, array<string, mixed>>
   */
  public function getFields($locale = NULL): array {
    $this->initProfileFormFields();

    return $this->profileFormFields;
  }

  /**
   * {@inheritDoc}
   *
   * @phpstan-param array<string, mixed> $contactData
   *
   * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
   */
  protected function adjustContactData(&$contactData): void {
    // phpcs:enable
    foreach ($contactData as $fieldName => $value) {
      $newFieldName = FormFieldNameUtil::fromProfileFormFieldName($fieldName);
      unset($contactData[$fieldName]);

      if ('Contact:state_province_id' === $newFieldName) {
        // value contains "{country_id}-{state_province_id}"
        if (is_string($value) && str_contains($value, '-')) {
          $value = explode('-', $value, 2)[1];
        }
        $contactData['state_province_id'] = $value;
      }
      elseif (str_starts_with($newFieldName, 'Contact:')) {
        $contactData[substr($newFieldName, 8)] = $value;
      }
      elseif (!str_starts_with($fieldName, 'Participant:')) {
        $contactData[$newFieldName] = $value;
      }
    }

    foreach ($this->getHiddenFields() as $fieldName => $field) {
      if (str_starts_with($fieldName, 'Contact:')) {
        $contactData[substr($fieldName, 8)] = $field['value'];
      }
      elseif (!str_starts_with($fieldName, 'Participant:')) {
        $contactData[$fieldName] = $field['value'] ?? NULL;
      }
    }
  }

  /**
   * @phpstan-return array<string, array<string, mixed>>
   */
  private function getHiddenFields(): array {
    $this->initProfileFormFields();

    return $this->profileHiddenFormFields;
  }

  private function initProfileFormFields(): void {
    if ([] !== $this->profileFormFields) {
      return;
    }

    $fields = [];
    $weight = 0;
    foreach ($this->form['fields'] as $editorField) {
      $fields = array_merge($fields, $this->profileFormFieldFactory->createFields($editorField, $weight));
    }

    foreach ($fields as $name => $field) {
      if (($field['#hidden'] ?? FALSE) === TRUE) {
        $this->profileHiddenFormFields[$name] = $field;
      }
      else {
        $this->profileFormFields[$name] = $field;
      }
    }
  }

  /**
   * @phpstan-return array<string, string>
   *   Maps contact entity field names to form field names.
   */
  private function getContactFieldNameMap(): array {
    $contactFieldNamePrefix = FormFieldNameUtil::toProfileFormFieldName('Contact:');
    $contactFieldNamePrefixLength = strlen($contactFieldNamePrefix);
    $participantFieldNamePrefix = FormFieldNameUtil::toProfileFormFieldName('Participant:');

    $fieldNameMap = [];
    foreach (array_keys($this->getFields()) as $formFieldName) {
      if (str_starts_with($formFieldName, $contactFieldNamePrefix)) {
        $fieldNameMap[substr($formFieldName, $contactFieldNamePrefixLength)] = $formFieldName;
      }
      elseif (!str_starts_with($formFieldName, $participantFieldNamePrefix)) {
        $fieldNameMap[$formFieldName] = $formFieldName;
      }
    }

    return $fieldNameMap;
  }

}
