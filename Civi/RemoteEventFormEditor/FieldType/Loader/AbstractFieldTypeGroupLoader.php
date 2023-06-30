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

use Civi\RemoteEventFormEditor\FieldType\FieldTypeGroup;
use Civi\RemoteEventFormEditor\FieldType\FieldTypeGroupLoaderInterface;

abstract class AbstractFieldTypeGroupLoader implements FieldTypeGroupLoaderInterface {

  /**
   * @phpstan-var iterable<\Civi\RemoteEventFormEditor\FieldType\EditorFieldTypeLoaderInterface>
   */
  private iterable $typeLoaders;

  /**
   * @phpstan-param iterable<\Civi\RemoteEventFormEditor\FieldType\EditorFieldTypeLoaderInterface> $typeLoaders
   */
  public function __construct(iterable $typeLoaders) {
    $this->typeLoaders = $typeLoaders;
  }

  public function getFieldTypeGroup(): FieldTypeGroup {
    return new FieldTypeGroup($this->getName(), $this->getLabel(), $this->getTypes());
  }

  abstract protected function getLabel(): string;

  abstract protected function getName(): string;

  /**
   * @phpstan-return array<\Civi\RemoteEventFormEditor\FieldType\EditorFieldType>
   */
  private function getTypes(): array {
    $types = [];
    foreach ($this->typeLoaders as $typeLoader) {
      $types = array_merge($types, iterator_to_array($typeLoader->getFieldTypes()));
    }

    return $types;
  }

}
