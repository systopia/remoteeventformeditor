<?php
/*
 * Copyright (C) 2023 SYSTOPIA GmbH
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation in version 3.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types = 1);

namespace Civi\RemoteEventFormEditor\RegistrationProfile\Form;

use Civi\RemoteEventFormEditor\FieldType\EditorFieldType;

interface ConcreteProfileFormFieldFactoryInterface {

  public const SERVICE_TAG = 'remote_event_form_editor.profile_form_field_factory';

  /**
   * @return int Priority of the factory. (Default: 0)
   */
  public static function getPriority(): int;

  /**
   * @phpstan-param array<string, mixed> $editorField
   *
   * @phpstan-return array<string, array<string, mixed>>
   *
   * @see \CRM_Remoteevent_RegistrationProfile::getFields()
   *   Documents allowed return values.
   */
  public function createFields(
    array $editorField,
    EditorFieldType $editorFieldType,
    int &$weight,
    ?string $parent,
    ProfileFormFieldFactory $factory
  ): array;

  /**
   * @phpstan-param array<string, mixed> $editorField
   */
  public function isSupported(array $editorField, EditorFieldType $editorFieldType): bool;

}
