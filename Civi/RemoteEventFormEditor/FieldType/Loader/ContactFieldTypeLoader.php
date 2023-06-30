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

use Civi\RemoteEventFormEditor\FieldType\EditorFieldType;
use Civi\RemoteEventFormEditor\FieldType\EditorFieldTypeLoaderInterface;
use Civi\RemoteEventFormEditor\FieldType\Type\OptionGroupType;
use Civi\RemoteEventFormEditor\OptionsLoaderInterface;
use CRM_RemoteEventFormEditor_ExtensionUtil as E;

final class ContactFieldTypeLoader implements EditorFieldTypeLoaderInterface {

  private OptionsLoaderInterface $optionsLoader;

  public function __construct(OptionsLoaderInterface $optionsLoader) {
    $this->optionsLoader = $optionsLoader;
  }

  public function getFieldTypes(): iterable {
    yield new EditorFieldType('first_name', 'text', E::ts('First Name'), [
      'label' => E::ts('First Name'),
      'target' => 'Contact:first_name',
      'required' => TRUE,
      'maxLength' => 64,
      'validation' => 'Text',
    ]);

    yield new EditorFieldType('middle_name', 'text', E::ts('Middle Name'), [
      'label' => E::ts('Middle Name'),
      'target' => 'Contact:middle_name',
      'required' => TRUE,
      'maxLength'   => 64,
      'validation' => 'Text',
    ]);

    yield new EditorFieldType('last_name', 'text', E::ts('Last Name'), [
      'label' => E::ts('Last Name'),
      'target' => 'Contact:last_name',
      'required' => TRUE,
      'maxLength'   => 64,
      'validation' => 'Text',
    ]);

    yield new OptionGroupType('gender', E::ts('Gender'),
      $this->optionsLoader->getOptions('Contact', 'gender_id'),
      [
        'label' => E::ts('Gender'),
        'target' => 'Contact:gender_id',
        'required' => TRUE,
        'validation' => 'Positive',
      ]
    );

    yield new EditorFieldType('birth_date', 'date', E::ts('Birth Date'), [
      'label' => E::ts('Birth Date'),
      'target' => 'Contact:birth_date',
      'required' => TRUE,
      'validation' => 'Date',
    ]);

    $locationOptions = $this->optionsLoader->getOptions('Address', 'location_type_id');
    yield new OptionGroupType('location_type', E::ts('Location type'), $locationOptions, [
      'label' => E::ts('Location type'),
      'target' => 'Contact:location_type_id',
      'required' => TRUE,
      'allowedOptions' => array_values($locationOptions),
      'validation' => 'Integer',
    ], [], 'location-type',);

    yield new EditorFieldType('address', 'address', E::ts('Address'), [
      'label' => E::ts('Address'),
      'target' => 'Contact:address',
      'required' => TRUE,
      'street' => 'required',
      'city' => 'required',
      'postalCode' => 'required',
      'county' => 'none',
      'stateProvince' => 'none',
      'country' => 'none',
      'supplementalAddress1' => 'none',
      'supplementalAddress2' => 'none',
      'supplementalAddress3' => 'none',
      'labels' => [
        'street' => E::ts('Street Address'),
        'city' => E::ts('City'),
        'postalCode' => E::ts('Postal Code'),
        'county' => E::ts('County'),
        'stateProvince' => E::ts('State/Province'),
        'country' => E::ts('Country'),
        'supplementalAddress1' => E::ts('Supplemental Address 1'),
        'supplementalAddress2' => E::ts('Supplemental Address 2'),
        'supplementalAddress3' => E::ts('Supplemental Address 3'),
      ],
    ]);

    yield new EditorFieldType('email', 'text', E::ts('Email'), [
      'label' => E::ts('Email'),
      'target' => 'Contact:email',
      'required' => TRUE,
      'validation' => 'Email',
    ]);

    yield new EditorFieldType('phone', 'text', E::ts('Phone'), [
      'label' => E::ts('Phone'),
      'target' => 'Contact:phone',
      'required' => TRUE,
      'validation' => 'Text',
    ]);

    yield new OptionGroupType('prefix', E::ts('Prefix'),
      $this->optionsLoader->getOptions('Contact', 'prefix_id'),
      [
        'label' => E::ts('Prefix'),
        'target' => 'Contact:prefix_id',
        'required' => FALSE,
      ]
    );

    yield new OptionGroupType('suffix', E::ts('Suffix'),
      $this->optionsLoader->getOptions('Contact', 'suffix_id'),
      [
        'label' => E::ts('Suffix'),
        'target' => 'Contact:suffix_id',
        'required' => FALSE,
      ]
    );

    yield new EditorFieldType('formal_title', 'text', E::ts('Title'), [
      'label' => E::ts('Title'),
      'target' => 'Contact:formal_title',
      'required' => FALSE,
    ]);
  }

}
