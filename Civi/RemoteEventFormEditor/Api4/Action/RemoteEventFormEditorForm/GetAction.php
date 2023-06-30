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

namespace Civi\RemoteEventFormEditor\Api4\Action\RemoteEventFormEditorForm;

use Civi\Api4\Generic\DAOGetAction;
use Civi\Api4\Generic\Result;
use Civi\RemoteEventFormEditor\RegistrationProfile\FormEditorRegistrationProfile;

final class GetAction extends DAOGetAction {

  public function _run(Result $result): void {
    parent::_run($result);

    $records = [];
    /** @var array<string|mixed>&array{id: int} $record */
    foreach ($result as $record) {
      $record['identifier'] = FormEditorRegistrationProfile::FULL_ID_PREFIX . $record['id'];
      $record['activeUsageCount'] = $this->getActiveUsageCount($record['id']);
      $record['usageCount'] = $this->getUsageCount($record['id']);
      $records[] = $record;
    }

    $result->exchangeArray($records);
  }

  private function getUsageCount(int $id): int {
    // @todo
    return 0;
  }

  private function getActiveUsageCount(int $id): int {
    // @todo
    return 0;
  }

}
