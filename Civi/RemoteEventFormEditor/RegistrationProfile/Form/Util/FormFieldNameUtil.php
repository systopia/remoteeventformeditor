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

final class FormFieldNameUtil {

  /**
   * Converts the given field name to a name that can be used in the profile
   * form.
   *
   * @see fromProfileFormFieldName()
   */
  public static function toProfileFormFieldName(string $fieldName): string {
    // Field names with dots do not work.
    return str_replace('.', '::', $fieldName);
  }

  /**
   * Inverse of toProfileFormFieldName().
   *
   * @see toProfileFormFieldName()
   */
  public static function fromProfileFormFieldName(string $fieldName): string {
    return str_replace('::', '.', $fieldName);
  }

}
