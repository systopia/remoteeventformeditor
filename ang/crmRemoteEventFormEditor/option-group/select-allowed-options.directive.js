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

remoteEventFormEditorModule.directive('selectAllowedOptions', function () {
  return {
    restrict: 'E',
    scope: {
      id: '=',
      options: '=',
      allowed: '=',
    },
    templateUrl: '~/crmRemoteEventFormEditor/option-group/select-allowed-options.template.html',
    controller: ['$scope', function ($scope) {
      $scope.ts = CRM.ts('remoteeventformeditor');
      $scope.toggleAllowed = function (value) {
        const index = $scope.allowed.indexOf(value);
        if (index > -1) {
          $scope.allowed.splice(index, 1);
        } else {
          $scope.allowed.push(value);
        }
      }

      // remove allowed options meanwhile unavailable
      for (let i in $scope.allowed) {
        if (!Object.values($scope.options).includes($scope.allowed[i])) {
          $scope.allowed.splice(i, 1);
        }
      }
    }],
  };
});
