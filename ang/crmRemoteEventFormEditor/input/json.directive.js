/*
 * Copyright (C) 2023 SYSTOPIA GmbH
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

remoteEventFormEditorModule.directive('feJson', function() {
  return {
    require: 'ngModel',
    link: function(scope, elem, attrs, ctrl) {
      const ts = CRM.ts('remoteeventformeditor');

      function isJsonString(str) {
        try {
          JSON.parse(str);
        } catch (e) {
          return false;
        }
        return true;
      }

      ctrl.$formatters.unshift(function (a) {
        if (ctrl.$isEmpty(ctrl.$modelValue)) {
          return null;
        }

        return JSON.stringify(ctrl.$modelValue, null, 4);
      });

      ctrl.$parsers.unshift(function (a) {
        if (ctrl.$isEmpty(ctrl.$viewValue)) {
          return null;
        }

        try {
          return JSON.parse(ctrl.$viewValue);
        } catch (e) {
          return ctrl.$modelValue;
        }
      });

      ctrl.$validators.feJson = function(modelValue, viewValue) {
        const textarea = elem[0];
        if (ctrl.$isEmpty(modelValue) || isJsonString(viewValue)) {
          textarea.setCustomValidity('');

          return true;
        }

        textarea.setCustomValidity(ts('Invalid JSON'));

        return false;
      };
    }
  };
});
