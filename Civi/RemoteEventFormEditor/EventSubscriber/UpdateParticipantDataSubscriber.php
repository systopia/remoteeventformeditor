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

use Civi\RemoteEventFormEditor\RegistrationProfile\Form\Util\FormFieldNameUtil;
use Civi\RemoteParticipant\Event\UpdateParticipantEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UpdateParticipantDataSubscriber implements EventSubscriberInterface {

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents(): array {
    return ['civi.remoteparticipant.update.participant' => 'onUpdateParticipant'];
  }

  public function onUpdateParticipant(UpdateParticipantEvent $event): void {
    /*
     * Note: A hidden field mechanism as in
     * FormEditorRegistrationProfile::adjustContactData() would require access
     * to the profile. Though this event doesn't provide it. Currently, there are
     * no hideable participant fields. If required it would be possible to
     * register to an event which provides a RegistrationEvent instance.
     */
    $participantData = $event->get_participant_data();
    foreach ($participantData as $fieldName => $value) {
      $newFieldName = FormFieldNameUtil::fromProfileFormFieldName($fieldName);
      unset($participantData[$fieldName]);

      if (str_starts_with($newFieldName, 'Participant:')) {
        $participantData[substr($newFieldName, 12)] = $value;
        unset($participantData[$fieldName]);
      }
      elseif (!str_starts_with($newFieldName, 'Contact:')) {
        $participantData[$newFieldName] = $value;
      }
    }

    $event->set_participant_data($participantData);
  }

}
