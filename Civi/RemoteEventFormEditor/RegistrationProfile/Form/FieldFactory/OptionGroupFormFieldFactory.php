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
use Civi\RemoteEventFormEditor\FieldType\Type\OptionGroupType;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\ConcreteProfileFormFieldFactoryInterface;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\ProfileFormFieldFactory;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\Util\DependentFieldNameUtil;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\Util\FormFieldNameUtil;
use Civi\RemoteEventFormEditor\Util\ArrayUtil;
use Webmozart\Assert\Assert;

final class OptionGroupFormFieldFactory implements ConcreteProfileFormFieldFactoryInterface {

  public static function getPriority(): int {
    return 1;
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
    Assert::string($editorField['label']);
    Assert::isInstanceOf($editorFieldType, OptionGroupType::class);
    /** @var \Civi\RemoteEventFormEditor\FieldType\Type\OptionGroupType $editorFieldType */

    [$entityName, $entityFieldName] = explode(':', $editorField['target'], 2);
    $fieldName = FormFieldNameUtil::toProfileFormFieldName($editorField['target']);

    $fields = [
      $fieldName => [
        'name' => $fieldName,
        'entity_name' => $entityName,
        'entity_field_name' => $entityFieldName,
        'type' => $editorFieldType->isMultiple() ? 'Multi-Select' : 'Select',
        'weight' => $weight++,
        // @phpstan-ignore-next-line
        'options' => $this->getOptions($editorFieldType, $editorField['allowedOptions']),
        'required' => TRUE === ($editorField['required'] ?? FALSE) ? 1 : 0,
        'label' => $editorField['label'],
        'description' => $editorField['description'] ?? NULL,
        'value' => $editorField['value'] ?? NULL,
        'parent' => $parent,
        'dependencies' => DependentFieldNameUtil::getDependentProfileFormFieldNames($editorField),
      ],
    ];

    if (($editorField['validation'] ?? NULL) === 'Boolean') {
      // CiviCRM uses "0" and "1" as options for boolean fields, so we have to
      // convert the loaded value. We do not perform the inverse conversion
      // because the APIv3 calls used in de.systopia.remoteevent work better
      // with integers/integerish strings.
      $fields[$fieldName]['prefill_value_callback'] = fn ($value) => NULL === $value ? NULL : ($value ? '1' : '0');
    }

    return $fields;
  }

  /**
   * @inheritDoc
   */
  public function isSupported(array $editorField, EditorFieldType $editorFieldType): bool {
    return $editorFieldType instanceof OptionGroupType;
  }

  /**
   * @phpstan-param array<scalar|null> $allowedOptions
   *
   * @phpstan-return array<scalar|null, string>
   *   Mapping of value to label.
   */
  private function getOptions(OptionGroupType $fieldType, array $allowedOptions): array {
    // @phpstan-ignore-next-line
    return ArrayUtil::flip(array_filter(
      $fieldType->getOptions(),
      fn ($value) => in_array($value, $allowedOptions, TRUE),
    ));
  }

}
