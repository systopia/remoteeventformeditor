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

namespace Civi\RemoteEventFormEditor\Api4;

use Civi\RemoteEventFormEditor\OptionsLoaderInterface;

final class OptionsLoader implements OptionsLoaderInterface {

  /**
   * @inheritDoc
   */
  public function getOptions(string $entityName, string $field): array {
    $result = civicrm_api4($entityName, 'getFields', [
      'checkPermissions' => FALSE,
      'loadOptions' => TRUE,
      'where' => [
        ['name', '=', $field],
      ],
      'select' => [
        'options',
      ],
    ]);

    /** @var array<scalar|null, string> $options */
    $options = $result->first()['options'] ?? [];

    return array_combine(array_values($options), array_keys($options));
  }

}
