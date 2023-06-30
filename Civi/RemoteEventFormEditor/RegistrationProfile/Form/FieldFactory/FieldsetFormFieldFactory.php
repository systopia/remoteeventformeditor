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

final class FieldsetFormFieldFactory implements ConcreteProfileFormFieldFactoryInterface {

  public static function getPriority(): int {
    return 0;
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
    Assert::string($editorField['label']);
    Assert::isArray($editorField['items']);

    $fieldsetName = str_replace(' ', '_', $editorField['label']);

    $fields = [
      $fieldsetName => [
        'name' => $fieldsetName,
        'type' => 'fieldset',
        'weight' => $weight++,
        'label' => $editorField['label'],
        'description' => $editorField['description'] ?? NULL,
      ],
    ];
    /** @phpstan-var array<string, mixed> $item */
    foreach ($editorField['items'] as $item) {
      $fields = array_merge($fields, $factory->createFields($item, $weight, $fieldsetName));
    }

    return $fields;
  }

  /**
   * @inheritDoc
   */
  public function isSupported(array $editorField, EditorFieldType $editorFieldType): bool {
    return 'fieldset' === $editorFieldType->getInput();
  }

}
