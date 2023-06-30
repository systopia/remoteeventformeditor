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

'use strict';

remoteEventFormEditorModule.factory('formService', ['crmApi4', function(crmApi4) {
  return {
    getAll: () => crmApi4('RemoteEventFormEditorForm', 'get'),
    get: (id) => crmApi4('RemoteEventFormEditorForm', 'get', {where: [['id', '=', id]]}).then(function (result) {
      return result[0] ?? null;
    }),
    getByName: (name) => crmApi4('RemoteEventFormEditorForm', 'get', {where: [['name', '=', name]]}).then(function (result) {
      return result[0] ?? null;
    }),
    save: (form) => crmApi4('RemoteEventFormEditorForm', 'save', {records: [form] }),
    delete: (id) => crmApi4('RemoteEventFormEditorForm', 'delete', {where: [['id', '=', id]]}),
  }
}]);
