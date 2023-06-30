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

remoteEventFormEditorModule.directive('feEditorCanvas', function () {
  return {
    restrict: 'E',
    scope: {
      sortableOptions: '=',
      fields: '=',
      fieldTypes: '=',
    },
    templateUrl: '~/crmRemoteEventFormEditor/editor-canvas/editor-canvas.template.html',
    controller: function EditorCanvasController($scope) {
      $scope.ts = CRM.ts('remoteeventformeditor');
      $scope.getView = function (field) {
        if (field.type === 'fieldset') {
          return '~/crmRemoteEventFormEditor/editor-canvas/fieldset.template.html';
        }

        return '~/crmRemoteEventFormEditor/editor-canvas/field.template.html';
      };

      const removeWhenFound = function (items, _id) {
        for (let i = 0; i < items.length; ++i) {
          if (items[i]._id === _id) {
            items.splice(i, 1);

            return true;
          }

          if (items[i].items && removeWhenFound(items[i].items, _id)) {
            return true;
          }
        }

        return false;
      }

      $scope.removeItem = function (_id) {
        removeWhenFound($scope.fields, _id);
      }

      // When moving fields the scope of the field directive seems to be bound
      // on the position, so we store if details of a fields are shown here.
      $scope.showDetailsById = [];
    }
  }
});

