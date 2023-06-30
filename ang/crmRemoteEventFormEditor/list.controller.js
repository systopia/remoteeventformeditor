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

remoteEventFormEditorModule.controller('listController', ['$scope', '$routeParams', 'formService',
  function ($scope, $routeParams, formService) {
    const ts = $scope.ts = CRM.ts('remoteeventformeditor');

    function loadForms() {
      formService.getAll().then((result) => $scope.forms = result);
    }

    $scope.remove = function (form) {
      // TODO: Once delete is only possible if form is unused, the note regarding usage can be removed (see list.html).
      if (confirm(ts(
        'Do you really want to delete form "%name"? It must not be used in any event.',
        { name: form.name }
      ))) {
        formService.delete(form.id).then(loadForms());
      }
    }

    $scope.forms = [];
    loadForms();
  }
]);
