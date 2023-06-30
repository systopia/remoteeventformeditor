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

class EditorFieldType implements \JsonSerializable {

  /**
   * @phpstan-var array<string, mixed>
   */
  private array $extraData;

  private string $identifier;

  private string $input;

  private string $label;

  /**
   * @phpstan-var array<string, mixed>
   */
  private array $initialData;

  /**
   * @phpstan-param array<string, mixed> $initialData
   * @phpstan-param array<string, mixed> $extraData
   */
  public function __construct(string $identifier, string $input, string $label,
    array $initialData = [], array $extraData = []
  ) {
    $this->identifier = $identifier;
    $this->input = $input;
    $this->label = $label;
    $this->initialData = $initialData;
    $this->extraData = $extraData;
  }

  /**
   * @return array<string, mixed>
   */
  public function getExtraData(): array {
    return $this->extraData;
  }

  /**
   * @return mixed|null
   */
  public function getExtraDataValue(string $key) {
    return $this->extraData[$key] ?? NULL;
  }

  public function getIdentifier(): string {
    return $this->identifier;
  }

  public function getInput(): string {
    return $this->input;
  }

  public function getLabel(): string {
    return $this->label;
  }

  /**
   * @return array<string, mixed>
   */
  public function getInitialData(): array {
    return $this->initialData;
  }

  #[\ReturnTypeWillChange]
  public function jsonSerialize() {
    return $this->toArray();
  }

  /**
   * @phpstan-return array<string, scalar|mixed|null>&array{
   *   identifier: string,
   *   input: string,
   *   label: string,
   *   initialData: array<string, mixed>,
   * }
   */
  public function toArray(): array {
    return [
      'identifier' => $this->identifier,
      'input' => $this->input,
      'label' => $this->label,
      'initialData' => $this->initialData,
    ] + $this->extraData;
  }

}
