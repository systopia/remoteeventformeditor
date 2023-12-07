<?php
/*
 * Copyright (C) 2022 SYSTOPIA GmbH
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

namespace Civi\RemoteEventFormEditor\FieldType\Loader;

use Civi\Api4\Generic\Result;
use Civi\RemoteEventFormEditor\FieldType\EditorFieldType;
use Civi\RemoteEventFormEditor\FieldType\EditorFieldTypeLoaderInterface;
use Civi\RemoteEventFormEditor\FieldType\Type\OptionGroupType;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

/**
 * @phpstan-type field array{
 *   input_type: string,
 *   data_type: string,
 *   label: string,
 *   name: string,
 *   required: bool,
 *   default_value: mixed,
 *   options: false|array<scalar, string>,
 *   readonly: bool,
 *   input_attrs: array<string, mixed>
 * }
 */
abstract class AbstractCustomFieldTypeLoader implements EditorFieldTypeLoaderInterface {

  /**
   * A field with more than this number of options is filtered out. This happens
   * for example with fk_entity StateProvince. (With the current editor UI we
   * cannot handle so many options with acceptable performance.)
   */
  private const MAX_OPTIONS_COUNT = 2000;

  private string $entityName;

  private LoggerInterface $logger;

  public function __construct(string $entityName, LoggerInterface $logger) {
    $this->entityName = $entityName;
    $this->logger = $logger;
  }

  // phpcs:disable Generic.Metrics.CyclomaticComplexity.MaxExceeded
  public function getFieldTypes(): iterable {
    // phpcs:enable
    $result = $this->getEntityFields();
    /** @phpstan-var field $field */
    foreach ($result as $field) {
      if ($field['readonly']) {
        continue;
      }

      $identifier = $this->entityName . ':' . $field['name'];
      $label = $field['label'];
      $initialData = $this->buildInitialData($field);

      switch ($field['input_type']) {
        case 'Date':
          if ('Timestamp' === $field['data_type']) {
            yield new EditorFieldType($identifier, 'datetime', $label, $initialData);
          }
          else {
            yield new EditorFieldType($identifier, 'date', $label, $initialData);
          }
          break;

        case 'Text':
          if ('Float' === $field['data_type'] || 'Money' === $field['data_type']) {
            yield new EditorFieldType($identifier, 'number', $label, $initialData);
            break;
          }
          // fall through
        case 'Url':
          yield new EditorFieldType($identifier, 'text', $label, $initialData);
          break;

        case 'TextArea':
          yield new EditorFieldType($identifier, 'text', $label, $initialData + ['multiLine' => TRUE]);
          break;

        case 'Number':
          yield new EditorFieldType($identifier, 'number', $label, $initialData);
          break;

        case 'Autocomplete-Select':
          // fall through
        case 'CheckBox':
          // fall through
        case 'Radio':
          // fall through
        case 'Select':
          $extraData = [];

          if (is_bool($field['options'])) {
            // Treat boolean as empty array.
            // This happens for custom fields with a multiple choice option group that has no (active) option.
            $field['options'] = [];
          }

          Assert::isArray($field['options']);
          if (count($field['options']) > self::MAX_OPTIONS_COUNT) {
            $field['options'] = array_slice($field['options'], 0, self::MAX_OPTIONS_COUNT, TRUE);
            $extraData['optionLimitExceeded'] = TRUE;
          }

          if ('CheckBox' === $field['input_type'] || TRUE === ($field['input_attrs']['multiple'] ?? FALSE)) {
            $extraData['multiple'] = TRUE;
          }
          $options = self::swapOptions($field['options']);
          yield new OptionGroupType($identifier, $label, $options, $initialData, $extraData);
          break;

        case 'RichTextEditor':
          // fall through
        case 'EntityRef':
          // fall through
        case 'File':
          // not supported
          break;

        default:
          $this->logger->info(sprintf('Unknown input type "%s"', $field['input_type']));
      }
    }
  }

  /**
   * @phpstan-param field $field
   *
   * @phpstan-return array{
   *   label: string,
   *   target: string,
   *   required: bool,
   *   value: mixed,
   *   validation: string,
   * }
   */
  private function buildInitialData(array $field): array {
    return [
      'label' => $field['label'],
      'target' => $this->entityName . ':' . $field['name'],
      'required' => $field['required'],
      'value' => $field['default_value'],
      'validation' => $this->getValidation($field['data_type']),
    ];
  }

  private function getEntityFields(): Result {
    return civicrm_api4($this->entityName, 'getFields', [
      'checkPermissions' => FALSE,
      'loadOptions' => TRUE,
      'where' => [
        ['type', '=', 'Custom'],
      ],
    ]);
  }

  private function getValidation(string $dataType): string {
    switch ($dataType) {
      case 'Boolean':
        return 'Boolean';

      case 'Date':
        return 'Date';

      case 'Timestamp':
        return 'Timestamp';

      case 'Url':
        return 'Link';

      case 'Float':
        // fall through
      case 'Money':
        return 'Float';

      case 'Integer':
        return 'Integer';

      default:
        return 'Text';
    }
  }

  /**
   * Similar to array_flip(), but supports any scalar.
   *
   * @phpstan-param array<scalar, string> $options
   *
   * @phpstan-return array<string, scalar>
   */
  private static function swapOptions(array $options): array {
    /** @phpstan-var array<string, scalar> $swappedOptions */
    $swappedOptions = array_combine(array_values($options), array_keys($options));

    return $swappedOptions;
  }

}
