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
use CRM_Remoteevent_RegistrationProfile;

/**
 * @phpstan-type formT array{
 *   id: int,
 *   identifier: string,
 *   name: string,
 *   fields: array<array<string, mixed>>,
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
   *
   * @phpstan-return array<string, array<string, mixed>>
   */
  public function getFields($locale = NULL): array {
    $this->initProfileFormFields();

    return $this->profileFormFields;
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
      $this->profileFormFields[$name] = $field;
    }
  }

}
