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

const remoteEventFormEditorModule = angular.module('crmRemoteEventFormEditor');

remoteEventFormEditorModule.config(['$routeProvider', function($routeProvider) {
  $routeProvider
    .when('/remote-event/form-editor', {
      controller: 'listController',
      templateUrl: '~/crmRemoteEventFormEditor/list.html',
    })
    .when('/remote-event/form-editor/:id', {
      controller: 'editController',
      templateUrl: '~/crmRemoteEventFormEditor/edit.html',
      resolve: {
        form: ['$route', 'formService', function ($route, formService) {
          if ($route.current.params.id === 'new') {
            return { name: '', fields: [] };
          }

          return formService.get($route.current.params.id);
        }],
      },
    })
  ;
}]);
