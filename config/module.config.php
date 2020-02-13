<?php
/**
 * module.config.php - Position Config
 *
 * Main Config File for Job Position Plugin
 *
 * @category Config
 * @package Job\Position
 * @author Verein onePlace
 * @copyright (C) 2020  Verein onePlace <admin@1plc.ch>
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 1.0.0
 * @since 1.0.0
 */

namespace OnePlace\Job\Position;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    # Position Module - Routes
    'router' => [
        'routes' => [
            'job-position-setup' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/job/position/setup',
                    'defaults' => [
                        'controller' => Controller\InstallController::class,
                        'action'     => 'checkdb',
                    ],
                ],
            ],
        ],
    ], # Routes

    # View Settings
    'view_manager' => [
        'template_path_stack' => [
            'job-position' => __DIR__ . '/../view',
        ],
    ],
];
