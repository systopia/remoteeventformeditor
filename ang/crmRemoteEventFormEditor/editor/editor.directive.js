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

(function(angular, $, _) {

  remoteEventFormEditorModule.directive('feEditor', function () {
    return {
      restrict: 'E',
      scope: {
        fields: '=',
      },
      templateUrl: '~/crmRemoteEventFormEditor/editor/editor.template.html',
      controller: ['$scope', 'fieldTypesService', function ($scope, fieldTypesService) {
        $scope.ts = CRM.ts('remoteeventformeditor');

        function initFieldIds(fields) {
          let fieldsQueue = [];
          fieldsQueue.push.apply(fieldsQueue, fields);
          let field;
          while ((field = fieldsQueue.shift()) !== undefined) {
            field._id = id++;
            if (field.items) {
              fieldsQueue.push.apply(fieldsQueue, field.items);
            }
          }
        }

        $scope.getView = function (field) {
          return '~/crmRemoteEventFormEditor/editor/field-type-group.template.html';
        };

        $scope.fieldTypeGroups = [];
        fieldTypesService.getAll().then(function (results) {
          $scope.fieldTypeGroups = results;
          let fieldTypes = {}
          results.forEach(function (fieldTypeGroup) {
            fieldTypeGroup.types.forEach(function (fieldType) {
              fieldTypes[fieldType.identifier] = fieldType;
            });
          });
          $scope.fieldTypes = fieldTypes;
        });

        let id = 1;
        initFieldIds($scope.fields);

        let sourceModelClone;
        $scope.sortableOptions = $scope.sortableOptions = {
          connectWith: '.field-container',
          handle: '.handle',
          start: function (event, ui) {
            if ($(event.target).hasClass('type-group')) {
              // clone the original model to restore it in stop()
              sourceModelClone = ui.item.sortable.sourceModel.slice();
            }
          },
          stop: function (event, ui) {
            // if the element is removed from the field types list
            if ($(event.target).hasClass('type-group')) {
              ui.item.sortable.sourceModel.length = 0;
              // restore the removed item
              Array.prototype.push.apply(
                ui.item.sortable.sourceModel,
                sourceModelClone
              );
              sourceModelClone = null;
            }
          },
          receive: function (event, ui) {
            if (!ui.item.sortable.moved._id && $(event.target).hasClass('field-container')) {
              ui.item.sortable.moved = {
                // add increasing id to added field
                _id: id++,
                type: ui.item.sortable.moved.identifier,
                ..._.cloneDeep(ui.item.sortable.moved.initialData)
              };
            }
          },
        };
      }],
    };
  });

})(angular, CRM.$, CRM._);
