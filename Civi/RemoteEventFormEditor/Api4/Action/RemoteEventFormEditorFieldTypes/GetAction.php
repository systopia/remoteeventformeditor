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

namespace Civi\RemoteEventFormEditor\Api4\Action\RemoteEventFormEditorFieldTypes;

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\RemoteEventFormEditor\FieldType\FieldTypeGroupContainer;

final class GetAction extends AbstractAction {

  private FieldTypeGroupContainer $fieldTypeGroupContainer;

  public function __construct(FieldTypeGroupContainer $fieldTypeGroupContainer) {
    parent::__construct('RemoteEventFormEditorFieldTypes', 'get');
    $this->fieldTypeGroupContainer = $fieldTypeGroupContainer;
  }

  public function _run(Result $result): void {
    $result->exchangeArray(iterator_to_array($this->fieldTypeGroupContainer->getFieldTypeGroups()));
  }

}
