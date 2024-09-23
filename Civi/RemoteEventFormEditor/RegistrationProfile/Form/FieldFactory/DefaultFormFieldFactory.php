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
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\Util\DependentFieldNameUtil;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\Util\FormFieldNameUtil;
use Webmozart\Assert\Assert;

final class DefaultFormFieldFactory implements ConcreteProfileFormFieldFactoryInterface {

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
    Assert::string($editorField['target']);
    Assert::string($editorField['label']);

    [$entityName, $entityFieldName] = explode(':', $editorField['target'], 2);
    $fieldName = FormFieldNameUtil::toProfileFormFieldName($editorField['target']);

    $fields = [
      $fieldName => [
        'name' => $fieldName,
        'entity_name' => $entityName,
        'entity_field_name' => $entityFieldName,
        'type' => $this->getType($editorField, $editorFieldType),
        'weight' => $weight++,
        'required' => ($editorField['required'] ?? FALSE) === TRUE ? 1 : 0,
        'label' => $editorField['label'],
        'description' => $editorField['description'] ?? NULL,
        'validation' => $this->getValidation($editorField, $editorFieldType),
        'value' => $editorField['value'] ?? NULL,
        'parent' => $parent,
        'dependencies' => DependentFieldNameUtil::getDependentProfileFormFieldNames($editorField),
      ],
    ];

    if ('file' === $editorFieldType->getInput()) {
      $fields[$fieldName]['max_filesize'] = $editorField['maxFilesize'] ?? NULL;
    }
    else {
      $fields[$fieldName]['maxlength'] = $editorField['maxLength'] ?? NULL;
    }

    return $fields;
  }

  /**
   * @inheritDoc
   */
  public function isSupported(array $editorField, EditorFieldType $editorFieldType): bool {
    return in_array($editorFieldType->getInput(), [
      'text',
      'number',
      'date',
      'datetime',
      'checkbox',
      'file',
    ], TRUE);
  }

  /**
   * @phpstan-param array<string, mixed> $editorField
   */
  private function getType(array $editorField, EditorFieldType $editorFieldType): string {
    switch ($editorFieldType->getInput()) {
      case 'text':
        return ($editorField['multiLine'] ?? FALSE) === TRUE ? 'Textarea' : 'Text';

      case 'number':
        return 'Text';

      case 'date':
        return 'Date';

      case 'datetime':
        return 'Datetime';

      case 'checkbox':
        return 'Checkbox';

      case 'file':
        return 'File';

      default:
        throw new \InvalidArgumentException(sprintf('Unsupported input type "%s"', $editorFieldType->getInput()));
    }
  }

  /**
   * @phpstan-param array<string, mixed> $editorField
   */
  private function getValidation(array $editorField, EditorFieldType $editorFieldType): ?string {
    $validation = $editorField['validation'] ?? NULL;
    Assert::nullOrString($validation);
    if ($validation === 'regex') {
      Assert::string($editorField['validationRegex']);

      return 'regex:' . $this->getRegex($editorField['validationRegex']);
    }

    if (
      (
        'date' === $editorFieldType->getInput()
        || 'datetime' === $editorFieldType->getInput()
      )
      && 'Text' === $validation
    ) {
      // Custom fields with data_type 'Timestamp' falsely used to have 'Text' as validation. Because it is possible to
      // switch between data_type 'Timestamp' and 'Date' we have to check both inputs.
      // @phpstan-ignore-next-line
      return $editorFieldType->getInitialData()['validation'] ?? $validation;
    }

    return $validation;
  }

  private function getRegex(string $regex): string {
    if ('' === $regex) {
      return '//';
    }

    if ($regex[0] === $regex[strlen($regex) - 1]) {
      return $regex;
    }

    $fieldSeparators = ['#', '/', '@', '!'];
    foreach ($fieldSeparators as $fieldSeparator) {
      if (!str_contains($regex, $fieldSeparator)) {
        return $fieldSeparator . $regex . $fieldSeparator;
      }
    }

    throw new \InvalidArgumentException('Could not determine regex field separator');
  }

}
