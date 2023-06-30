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

namespace Civi\RemoteEventFormEditor\FieldType;

final class FieldTypeContainer {

  /**
   * @phpstan-var array<string, EditorFieldType>
   */
  private array $types = [];

  private FieldTypeGroupContainer $fieldTypeGroupContainer;

  public function __construct(FieldTypeGroupContainer $fieldTypeGroupContainer) {
    $this->fieldTypeGroupContainer = $fieldTypeGroupContainer;
  }

  public function getFieldType(string $identifier): ?EditorFieldType {
    $this->loadFieldTypes();

    return $this->types[$identifier] ?? NULL;
  }

  /**
   * @phpstan-return array<string, EditorFieldType>
   *   Field types with identifier as key.
   */
  public function getFieldTypes(): array {
    $this->loadFieldTypes();

    return $this->types;
  }

  public function hasFieldType(string $identifier): bool {
    $this->loadFieldTypes();

    return isset($this->types[$identifier]);
  }

  private function loadFieldTypes(): void {
    if ([] !== $this->types) {
      return;
    }

    foreach ($this->fieldTypeGroupContainer->getFieldTypeGroups() as $group) {
      foreach ($group->getTypes() as $type) {
        $this->types[$type->getIdentifier()] = $type;
      }
    }
  }

}
