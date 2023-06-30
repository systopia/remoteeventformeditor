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

// Nesting tg-dynamic-directive made problem when moving fields.
// That's the reason for this directive.
remoteEventFormEditorModule.directive('feField', function() {
  return {
    restrict: 'E',
    scope: {
      type: '=',
      field: '=item',
      remove: '&',
      showDetails: '=',
    },
    templateUrl: '~/crmRemoteEventFormEditor/field/field.template.html',
    controller: function($scope) {
      $scope.ts = CRM.ts('remoteeventformeditor');

      $scope.getFieldView = function() {
        return '~/crmRemoteEventFormEditor/field-types/' + $scope.type.input + '.template.html';
      };

      $scope.toggleDetails = function() {
        $scope.showDetails = !$scope.showDetails;
      }
    },
  };
});
