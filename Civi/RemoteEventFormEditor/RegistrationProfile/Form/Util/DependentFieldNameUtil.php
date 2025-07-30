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

namespace Civi\RemoteEventFormEditor\RegistrationProfile\Form\Util;

final class DependentFieldNameUtil {

  /**
   * Converts the dependent field names of the editor field to the names used in
   * the profile form. If the field has no dependencies an empty array is
   * returned.
   *
   * @phpstan-param array<string, mixed> $editorField
   *
   * @phpstan-return array<array<string, mixed>>
   *
   * @see toProfileFormFieldNames()
   */
  public static function getDependentProfileFormFieldNames(array $editorField): array {
    if (!is_array($editorField['dependencies'] ?? NULL)) {
      return [];
    }

    // @phpstan-ignore argument.type
    return self::toProfileFormFieldNames($editorField['dependencies']);
  }

  /**
   * Converts the dependent field names to the names used in the profile form.
   *
   * @phpstan-param array<array<string, mixed>> $dependencies
   *
   * @phpstan-return array<array<string, mixed>>
   *
   * @see FormFieldNameUtil::toProfileFormFieldName()
   */
  public static function toProfileFormFieldNames(array $dependencies): array {
    foreach ($dependencies as &$dependency) {
      if (is_string($dependency['dependent_field'] ?? NULL)) {
        $dependency['dependent_field'] = FormFieldNameUtil::toProfileFormFieldName($dependency['dependent_field']);
      }
    }

    return $dependencies;
  }

}
