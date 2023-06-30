<?php

declare(strict_types = 1);

namespace Civi\Api4;

use Civi\RemoteEventFormEditor\Api4\Action\RemoteEventFormEditorForm\GetAction;

/**
 * RemoteEventFormEditorForm entity.
 *
 * Provided by the Remote Event Form Editor extension.
 *
 * @package Civi\Api4
 */
final class RemoteEventFormEditorForm extends Generic\DAOEntity {

  public static function get($checkPermissions = TRUE) {
    return (new GetAction(static::getEntityName(), __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

  /**
   * @phpstan-return array<string, array<string|array<string>>>
   */
  public static function permissions(): array {
    return \CRM_Core_Permission::getEntityActionPermissions()['event'];
  }

}
