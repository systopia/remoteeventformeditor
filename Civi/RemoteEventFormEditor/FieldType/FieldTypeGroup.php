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

final class FieldTypeGroup implements \JsonSerializable {

  private string $label;

  private string $name;

  /**
   * @phpstan-var array<EditorFieldType>
   */
  private array $types;

  /**
   * @phpstan-param array<EditorFieldType> $types
   */
  public function __construct(string $name, string $label, array $types) {
    $this->name = $name;
    $this->label = $label;
    $this->types = $types;
  }

  public function getLabel(): string {
    return $this->label;
  }

  public function getName(): string {
    return $this->name;
  }

  public function addType(EditorFieldType $type): self {
    $this->types[] = $type;

    return $this;
  }

  /**
   * @phpstan-param array<EditorFieldType> $types
   */
  public function addTypes(array $types): self {
    $this->types = array_merge($this->types, array_values($types));

    return $this;
  }

  /**
   * @phpstan-return array<EditorFieldType>
   */
  public function getTypes(): array {
    return $this->types;
  }

  #[\ReturnTypeWillChange]
  public function jsonSerialize() {
    return [
      'name' => $this->name,
      'label' => $this->label,
      'types' => $this->types,
    ];
  }

}
