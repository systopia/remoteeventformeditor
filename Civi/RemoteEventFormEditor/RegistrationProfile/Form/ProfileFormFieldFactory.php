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

namespace Civi\RemoteEventFormEditor\RegistrationProfile\Form;

use Civi\RemoteEventFormEditor\FieldType\FieldTypeContainer;
use Webmozart\Assert\Assert;

final class ProfileFormFieldFactory {

  /**
   * @phpstan-var iterable<ConcreteProfileFormFieldFactoryInterface>
   */
  private iterable $factories;

  private FieldTypeContainer $fieldTypeContainer;

  /**
   * @phpstan-param iterable<ConcreteProfileFormFieldFactoryInterface> $factories
   */
  public function __construct(iterable $factories, FieldTypeContainer $fieldTypeContainer) {
    $this->factories = $factories;
    $this->fieldTypeContainer = $fieldTypeContainer;
  }

  /**
   * @phpstan-param array<string, mixed> $editorField
   *
   * @phpstan-return array<string, array<string, mixed>>
   *
   * @see \CRM_Remoteevent_RegistrationProfile::getFields()
   *   Documents allowed return values.
   */
  public function createFields(array $editorField, int &$weight, ?string $parent = NULL): array {
    Assert::string($editorField['type']);
    $fieldType = $this->fieldTypeContainer->getFieldType($editorField['type']);

    if (NULL !== $fieldType) {
      foreach ($this->factories as $factory) {
        if ($factory->isSupported($editorField, $fieldType)) {
          return $factory->createFields($editorField, $fieldType, $weight, $parent, $this);
        }
      }
    }

    throw new \InvalidArgumentException(sprintf('Unsupported type "%s"', $editorField['type']));
  }

  /**
   * @phpstan-param array<string, mixed> $editorField
   */
  public function isSupported(array $editorField): bool {
    Assert::string($editorField['type']);
    $fieldType = $this->fieldTypeContainer->getFieldType($editorField['type']);
    if (NULL === $fieldType) {
      return FALSE;
    }

    foreach ($this->factories as $factory) {
      if ($factory->isSupported($editorField, $fieldType)) {
        return TRUE;
      }
    }

    return FALSE;
  }

}
