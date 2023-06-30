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

use PHPUnit\Framework\TestCase;

/**
 * @covers \Civi\RemoteEventFormEditor\FieldType\FieldTypeContainer
 */
final class FieldTypeContainerTest extends TestCase {

  private FieldTypeContainer $fieldTypeContainer;

  /**
   * @phpstan-var array<string, \Civi\RemoteEventFormEditor\FieldType\EditorFieldType>
   */
  private $types = [];

  protected function setUp(): void {
    parent::setUp();
    $this->types = [
      'type0' => new EditorFieldType('type0', 'text', 'Type 0'),
      'type1' => new EditorFieldType('type1', 'date', 'Type 1'),
      'type2' => new EditorFieldType('type2', 'number', 'Type 2'),
    ];
    $fieldTypeGroup1 = new FieldTypeGroup('test', 'Test', [
      $this->types['type0'],
      $this->types['type1'],
    ]);
    $fieldTypeGroup2 = new FieldTypeGroup('test', 'Test', [
      $this->types['type2'],
    ]);

    $fieldTypeGroupContainerMock = $this->createMock(FieldTypeGroupContainer::class);
    $fieldTypeGroupContainerMock->method('getFieldTypeGroups')->willReturn([$fieldTypeGroup1, $fieldTypeGroup2]);
    $this->fieldTypeContainer = new FieldTypeContainer($fieldTypeGroupContainerMock);
  }

  public function testGetFieldType(): void {
    static::assertSame($this->types['type1'], $this->fieldTypeContainer->getFieldType('type1'));
    static::assertSame($this->types['type2'], $this->fieldTypeContainer->getFieldType('type2'));
    static::assertNull($this->fieldTypeContainer->getFieldType('foo'));
  }

  public function testGetFieldTypes(): void {
    static::assertEquals($this->types, $this->fieldTypeContainer->getFieldTypes());
  }

  public function testHasFieldType(): void {
    static::assertTrue($this->fieldTypeContainer->hasFieldType('type1'));
    static::assertTrue($this->fieldTypeContainer->hasFieldType('type2'));
    static::assertFalse($this->fieldTypeContainer->hasFieldType('foo'));
  }

}
