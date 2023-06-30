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

(function(_) {

  remoteEventFormEditorModule.controller('editController', ['$scope', '$location', 'formService', 'form',
    function ($scope, $location, formService, form) {
      $scope.ts = CRM.ts('remoteeventformeditor');
      $scope.form = form;

      function omitPrivateFieldProperties(fields) {
        fields = _.cloneDeep(fields);
        let fieldsQueue = [];
        fieldsQueue.push.apply(fieldsQueue, fields);
        let field;
        while ((field = fieldsQueue.shift()) !== undefined) {
          for (let key in field) {
            if (key.startsWith('_')) {
              delete field[key];
            }
          }

          if (field.items) {
            fieldsQueue.push.apply(fieldsQueue, field.items);
          }
        }

        return fields;
      }

      function doSave(form) {
        formService.save(form).then(function (result) {
          $location.path('/remote-event/form-editor');
        });
      }

      $scope.save = function () {
        let form = {
          name: $scope.form.name,
          fields: omitPrivateFieldProperties($scope.form.fields),
        }
        if ($scope.form.id) {
          form.id = $scope.form.id;
        }

        formService.getByName(form.name).then(function (result) {
          if (result === null || result.id === form.id) {
            doSave(form);
          } else {
            // TODO: Show error because of duplicate name
          }
        });
      };
    }
  ]);

})(CRM._);
