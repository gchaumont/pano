<?php

namespace Pano;

/**
 * Base class for all Pano objects
 * Handles identification and routing to the various models
 * Sub Classes
 * - Application (link, name)
 * - Dashboard  (link, name)
 * - Resource (link, name)
 * - Fields (name, link to containing resource)
 * - Metrics (name, link to containing resource)
 * - Menu (path, name)
 *  - MenuItem, MenuGroup (if landing on menu group, redirect to app home).
 */
abstract class PanoObject
{
}
