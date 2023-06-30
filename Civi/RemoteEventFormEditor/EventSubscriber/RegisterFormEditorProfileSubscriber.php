<?php
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

declare(strict_types = 1);

namespace Civi\RemoteEventFormEditor\EventSubscriber;

use Civi\Api4\RemoteEventFormEditorForm;
use Civi\RemoteEvent\Event\RegistrationProfileListEvent;
use Civi\RemoteEventFormEditor\RegistrationProfile\FormEditorRegistrationProfile;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RegisterFormEditorProfileSubscriber implements EventSubscriberInterface {

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents(): array {
    return ['civi.remoteevent.registration.profile.list' => 'registerProfiles'];
  }

  /**
   * Add Form editor profiles to the registration profile list event.
   */
  public function registerProfiles(RegistrationProfileListEvent $event): void {
    $newInstanceCallback = function (int $id) {
      // @phpstan-ignore-next-line
      return new FormEditorRegistrationProfile(RemoteEventFormEditorForm::get(FALSE)
        ->addWhere('id', '=', $id)
        ->execute()
        ->single()
      );
    };
    foreach ($this->getForms() as $form) {
      $newInstanceCallback = function () use ($form) {
        return $this->newProfileInstance($form['id']);
      };

      $event->addProfile(
        FormEditorRegistrationProfile::class,
        $form['identifier'],
        $form['name'],
        $newInstanceCallback,
      );
    }
  }

  /**
   * @phpstan-return iterable<array{id: int, identifier: string, name: string}>
   */
  private function getForms(): iterable {
    return RemoteEventFormEditorForm::get(FALSE)
      ->addSelect('id', 'identifer', 'name')
      ->execute();
  }

  private function newProfileInstance(int $id): FormEditorRegistrationProfile {
    // @phpstan-ignore-next-line
    return new FormEditorRegistrationProfile(RemoteEventFormEditorForm::get(FALSE)
      ->addWhere('id', '=', $id)
      ->execute()
      ->single()
    );
  }

}
