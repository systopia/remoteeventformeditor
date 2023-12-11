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
use CRM_RemoteEventFormEditor_ExtensionUtil as E;

final class GenericFieldTypeLoader implements EditorFieldTypeLoaderInterface {

  public function getFieldTypes(): iterable {
    yield new EditorFieldType('fieldset', 'fieldset', E::ts('Group'), ['items' => []]);
    yield new EditorFieldType('text', 'text', E::ts('Text'), ['validation' => 'Text']);
    yield new EditorFieldType('number', 'number', E::ts('Number'), ['validation' => 'Float']);
    // Disabled for now until we have a proper way to set the options in the UI.
    // yield new EditorFieldType('select', 'select', E::ts('Select'));
    // yield new EditorFieldType('multi-select', 'multi-select', E::ts('Multi select'));
    yield new EditorFieldType('date', 'date', E::ts('Date'), ['validation' => 'Date']);
    yield new EditorFieldType('datetime', 'datetime', E::ts('Date and Time'), ['validation' => 'Timestamp']);
    yield new EditorFieldType('checkbox', 'checkbox', E::ts('Checkbox'), ['validation' => 'Boolean']);
  }

}
