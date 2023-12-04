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

namespace Civi\RemoteEventFormEditor\RegistrationProfile\Form\FieldFactory;

use Civi\RemoteEventFormEditor\FieldType\EditorFieldType;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\ConcreteProfileFormFieldFactoryInterface;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\ProfileFormFieldFactory;
use Webmozart\Assert\Assert;

final class HiddenFormFieldFactory implements ConcreteProfileFormFieldFactoryInterface {

  public static function getPriority(): int {
    return 10;
  }

  /**
   * @inheritDoc
   */
  public function createFields(
    array $editorField,
    EditorFieldType $editorFieldType,
    int &$weight,
    ?string $parent,
    ProfileFormFieldFactory $factory
  ): array {
    Assert::string($editorField['target']);
    Assert::true($editorField['hidden'] ?? FALSE);
    [$entityName, $entityFieldName] = explode(':', $editorField['target'], 2);

    return [
      $editorField['target'] => [
        'name' => $editorField['target'],
        'entity_name' => $entityName,
        'entity_field_name' => $entityFieldName,
        'type' => 'Value',
        'value' => $editorField['value'] ?? NULL,
      ],
    ];
  }

  /**
   * @inheritDoc
   */
  public function isSupported(array $editorField, EditorFieldType $editorFieldType): bool {
    return ($editorField['hidden'] ?? FALSE) === TRUE;
  }

}
