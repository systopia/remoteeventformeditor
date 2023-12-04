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

use Civi\Api4\StateProvince;
use Civi\RemoteEventFormEditor\FieldType\EditorFieldType;
use Civi\RemoteEventFormEditor\OptionsLoaderInterface;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\ConcreteProfileFormFieldFactoryInterface;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\ProfileFormFieldFactory;
use Civi\RemoteEventFormEditor\RegistrationProfile\Form\Util\FormFieldNameUtil;
use Civi\RemoteEventFormEditor\Util\ArrayUtil;
use Webmozart\Assert\Assert;

final class AddressFormFieldFactory implements ConcreteProfileFormFieldFactoryInterface {

  private OptionsLoaderInterface $optionsLoader;

  public function __construct(OptionsLoaderInterface $optionsLoader) {
    $this->optionsLoader = $optionsLoader;
  }

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

    /** @phpstan-var array<string, string> $labels */
    $labels = $editorField['labels'];

    $fields = [
      $editorField['target'] => [
        'type' => 'fieldset',
        'name' => $editorField['target'],
        'label' => $editorField['label'],
        'weight' => $weight++,
        'description' => $editorField['description'] ?? NULL,
        'parent' => $parent,
      ],
    ];

    $parent = $editorField['target'];

    $fields += $this->createStreetField($editorField, $weight, $parent);
    $fields += $this->createSupplementalAddressFields($editorField, $weight, $parent);
    $fields += $this->createCityField($editorField, $weight, $parent);
    $fields += $this->createPostalCodeField($editorField, $weight, $parent);
    $fields += $this->createCountryAndRelatedFields($editorField, $weight, $parent);

    return $fields;
  }

  /**
   * @inheritDoc
   */
  public function isSupported(array $editorField, EditorFieldType $editorFieldType): bool {
    return 'address' === $editorFieldType->getInput();
  }

  /**
   * @phpstan-param array<string, mixed> $editorField
   *
   * @phpstan-return array<string, array<string, mixed>>
   */
  private function createStreetField(array $editorField, int &$weight, string $parent): array {

    if ('none' === ($editorField['street'] ?? 'none')) {
      return [];
    }

    /** @phpstan-var array<string, string> $labels */
    $labels = $editorField['labels'];

    return [
      FormFieldNameUtil::toProfileFormFieldName('Contact:street_address') => [
        'name' => FormFieldNameUtil::toProfileFormFieldName('Contact:street_address'),
        'entity_name' => 'Contact',
        'entity_field_name' => 'street_address',
        'type' => 'Text',
        'weight' => $weight++,
        'required' => $editorField['street'] === 'required' ? 1 : 0,
        'label' => $labels['street'],
        'maxlength' => 96,
        'parent' => $parent,
      ],
    ];
  }

  /**
   * @phpstan-param array<string, mixed> $editorField
   *
   * @phpstan-return array<string, array<string, mixed>>
   */
  private function createSupplementalAddressFields(array $editorField, int &$weight, string $parent): array {
    $fields = [];

    /** @phpstan-var array<string, string> $labels */
    $labels = $editorField['labels'];

    for ($i = 1; $i <= 3; ++$i) {
      $editorFieldName = 'supplementalAddress' . $i;
      if ('none' !== ($editorField[$editorFieldName] ?? 'none')) {
        $entityFieldName = 'supplemental_address_' . $i;
        $fieldName = FormFieldNameUtil::toProfileFormFieldName('Contact:' . $entityFieldName);
        $fields[$fieldName] = [
          'name' => $fieldName,
          'entity_name' => 'Contact',
          'entity_field_name' => $entityFieldName,
          'type' => 'Text',
          'weight' => $weight++,
          'required' => $editorField[$editorFieldName] === 'required' ? 1 : 0,
          'label' => $labels[$editorFieldName],
          'maxlength' => 96,
          'parent' => $parent,
        ];
      }
    }

    return $fields;
  }

  /**
   * @phpstan-param array<string, mixed> $editorField
   *
   * @phpstan-return array<string, array<string, mixed>>
   */
  private function createCityField(array $editorField, int &$weight, string $parent): array {
    if ('none' === ($editorField['city'] ?? 'none')) {
      return [];
    }

    /** @phpstan-var array<string, string> $labels */
    $labels = $editorField['labels'];

    return [
      FormFieldNameUtil::toProfileFormFieldName('Contact:city') => [
        'name' => FormFieldNameUtil::toProfileFormFieldName('Contact:city'),
        'entity_name' => 'Contact',
        'entity_field_name' => 'city',
        'type' => 'Text',
        'weight' => $weight++,
        'required' => $editorField['city'] === 'required' ? 1 : 0,
        'label' => $labels['city'],
        'maxlength' => 64,
        'parent' => $parent,
      ],
    ];
  }

  /**
   * @phpstan-param array<string, mixed> $editorField
   *
   * @phpstan-return array<string, array<string, mixed>>
   */
  private function createPostalCodeField(array $editorField, int &$weight, string $parent): array {
    if ('none' === ($editorField['postalCode'] ?? 'none')) {
      return [];
    }

    /** @phpstan-var array<string, string> $labels */
    $labels = $editorField['labels'];
    return [
      FormFieldNameUtil::toProfileFormFieldName('Contact:postal_code') => [
        'name' => FormFieldNameUtil::toProfileFormFieldName('Contact:postal_code'),
        'entity_name' => 'Contact',
        'entity_field_name' => 'postal_code',
        'type' => 'Text',
        'weight' => $weight++,
        'required' => $editorField['postalCode'] === 'required' ? 1 : 0,
        'label' => $labels['postalCode'],
        'maxlength' => 64,
        'parent' => $parent,
      ],
    ];
  }

  /**
   * @phpstan-param array<string, mixed> $editorField
   *
   * @phpstan-return array<string, array<string, mixed>>
   */
  private function createCountryAndRelatedFields(array $editorField, int &$weight, string $parent): array {
    if ('none' === ($editorField['country'] ?? 'none')) {
      return [];
    }

    /** @phpstan-var array<string, string> $labels */
    $labels = $editorField['labels'];

    /** @phpstan-var array<string, int> $countryIds */
    $countryIds = $this->optionsLoader->getOptions('Address', 'country_id');
    $fields = [
      FormFieldNameUtil::toProfileFormFieldName('Contact:country_id') => [
        'name' => FormFieldNameUtil::toProfileFormFieldName('Contact:country_id'),
        'entity_name' => 'Contact',
        'entity_field_name' => 'country_id',
        'type' => 'Select',
        'options' => ArrayUtil::flip($countryIds),
        'weight' => $weight++,
        'required' => $editorField['country'] === 'required' ? 1 : 0,
        'label' => $labels['country'],
        'parent' => $parent,
      ],
    ];

    if ('none' !== ($editorField['stateProvince'] ?? 'none')) {
      // ":" in 'dependent_field' is not possible, so we have to use "-" instead.
      $fields[FormFieldNameUtil::toProfileFormFieldName('Contact:country_id')]['dependencies'] = [
        [
          'dependent_field' => FormFieldNameUtil::toProfileFormFieldName('Contact:state_province_id'),
          'hide_unrestricted' => 1,
          'hide_restricted_empty' => 1,
          'command' => 'restrict',
          'regex_subject' => 'dependent',
          'regex' => '^({current_value}-[0-9]+)$',
        ],
      ];

      $fields[FormFieldNameUtil::toProfileFormFieldName('Contact:state_province_id')] = [
        'name' => FormFieldNameUtil::toProfileFormFieldName('Contact:state_province_id'),
        'entity_name' => 'Contact',
        'entity_field_name' => 'state_province_id',
        'value_callback' => fn ($value) => is_string($value) && str_contains($value, '-')
        ? explode('-', $value, 2)[1]
        : $value,
        'prefill_value_callback' => fn ($value, array $contact) => $contact['country_id'] . '-' . $value,
        'type' => 'Select',
        'weight' => $weight++,
        'required' => $editorField['stateProvince'] === 'required' ? 1 : 0,
        'options' => $this->getStateProvinceOptions($countryIds),
        'label' => $labels['stateProvince'],
        'parent' => $parent,
      ];
    }

    return $fields;
  }

  /**
   * @phpstan-param array<int> $countryIds
   *
   * @phpstan-return array<string, string>
   */
  private function getStateProvinceOptions(array $countryIds): array {
    $options = [];

    $stateProvinces = StateProvince::get(FALSE)
      ->addSelect('id', 'country_id', 'name')
      ->addWhere('country_id', 'IN', $countryIds)
      ->addWhere('is_active', '=', TRUE)
      ->execute();
    /** @phpstan-var array{id: int, country_id: int, name: string} $stateProvince */
    foreach ($stateProvinces as $stateProvince) {
      $key = $stateProvince['country_id'] . '-' . $stateProvince['id'];
      $options[$key] = $stateProvince['name'];
    }

    return $options;
  }

}
