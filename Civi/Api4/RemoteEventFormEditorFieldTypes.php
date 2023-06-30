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

namespace Civi\Api4;

use Civi\Api4\Generic\AbstractEntity;
use Civi\RemoteEventFormEditor\Api4\Action\RemoteEventFormEditorFieldTypes\GetAction;
use Civi\RemoteEventFormEditor\Api4\Action\RemoteEventFormEditorFieldTypes\GetFieldsAction;

final class RemoteEventFormEditorFieldTypes extends AbstractEntity {

  public static function getFields() {
    return new GetFieldsAction(static::getEntityName(), __FUNCTION__);
  }

  public static function get(bool $checkPermissions = TRUE): GetAction {
    // @phpstan-ignore-next-line
    return \Civi::service(GetAction::class)
      ->setCheckPermissions($checkPermissions);
  }

  /**
   * @phpstan-return array<string, array<string|array<string>>>
   */
  public static function permissions(): array {
    return \CRM_Core_Permission::getEntityActionPermissions()['event'];
  }

}
