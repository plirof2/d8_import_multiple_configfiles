<?php

/**
 * @file
 * Contains \Drupal\config_devel_import_multiple\Event\ConfigDevelEvents.
 */

namespace Drupal\config_devel_import_multiple\Event;

/**
 * Defines events for config devel.
 *
 * @see \Drupal\config_devel_import_multiple\Event\ConfigDevelSaveEvent
 */
final class ConfigDevelEvents {

  /**
   * Name of the event fired when saving a config entity to disk.
   *
   * This event allows other modules to impact the configuration that is being
   * written to disk
   *
   * @Event
   *
   * @see \Drupal\config_devel_import_multiple\Event\ConfigDevelSaveEvent
   *
   * @var string
   */
  const SAVE = 'config_devel_import_multiple.save';

}
