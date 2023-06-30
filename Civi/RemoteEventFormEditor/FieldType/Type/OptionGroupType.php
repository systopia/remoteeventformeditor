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

namespace Civi\RemoteEventFormEditor\FieldType\Type;

use Civi\RemoteEventFormEditor\FieldType\EditorFieldType;

class OptionGroupType extends EditorFieldType {

  /**
   * @phpstan-param array<string, scalar|null> $options
   *   Options in the form "label => value".
   * @phpstan-param array<string, mixed> $initialData
   * @phpstan-param array<string, mixed> $extraData
   */
  public function __construct(string $identifier, string $label, array $options,
    array $initialData = [], array $extraData = [], string $input = 'option-group'
  ) {
    parent::__construct(
      $identifier,
      $input,
      $label,
      $initialData + ['allowedOptions' => array_values($options)],
      ['options' => $options] + $extraData,
    );
  }

  /**
   * @phpstan-return array<string, scalar|null>
   */
  public function getOptions(): array {
    // @phpstan-ignore-next-line
    return $this->getExtraDataValue('options');
  }

  public function isMultiple(): bool {
    // @phpstan-ignore-next-line
    return $this->getExtraDataValue('multiple') ?? FALSE;
  }

}
