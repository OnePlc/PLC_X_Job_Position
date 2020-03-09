<?php
/**
 * Module.php - Module Class
 *
 * Module Class File for Job Position Plugin
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

use Application\Controller\CoreEntityController;
use Laminas\Mvc\MvcEvent;
use Laminas\EventManager\EventInterface as Event;
use Laminas\ModuleManager\ModuleManager;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use OnePlace\Job\Position\Controller\PositionController;
use OnePlace\Job\Position\Model\PositionTable;

class Module {
    /**
     * Module Version
     *
     * @since 1.0.0
     */
    const VERSION = '1.0.2';

    /**
     * Load module config file
     *
     * @since 1.0.0
     * @return array
     */
    public function getConfig() : array {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(Event $e)
    {
        // This method is called once the MVC bootstrapping is complete
        $application = $e->getApplication();
        $container    = $application->getServiceManager();
        $oDbAdapter = $container->get(AdapterInterface::class);
        $tableGateway = $container->get(PositionTable::class);

        # Register Filter Plugin Hook
        CoreEntityController::addHook('job-view-before',(object)['sFunction'=>'attachPositionForm','oItem'=>new PositionController($oDbAdapter,$tableGateway,$container)]);
        CoreEntityController::addHook('job-edit-before',(object)['sFunction'=>'attachPositionForm','oItem'=>new PositionController($oDbAdapter,$tableGateway,$container)]);
        //CoreEntityController::addHook('job-add-after-save',(object)['sFunction'=>'attachPositionToJob','oItem'=>new PositionController($oDbAdapter,$tableGateway,$container)]);
        //CoreEntityController::addHook('job-edit-after-save',(object)['sFunction'=>'attachPositionToJob','oItem'=>new PositionController($oDbAdapter,$tableGateway,$container)]);
    }

    /**
     * Load Models
     */
    public function getServiceConfig() : array {
        return [
            'factories' => [
                # Position Plugin - Base Model
                Model\PositionTable::class => function($container) {
                    $tableGateway = $container->get(Model\PositionTableGateway::class);
                    return new Model\PositionTable($tableGateway,$container);
                },
                Model\PositionTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Position($dbAdapter));
                    return new TableGateway('job_position', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    } # getServiceConfig()

    /**
     * Load Controllers
     */
    public function getControllerConfig() : array {
        return [
            'factories' => [
                Controller\PositionController::class => function($container) {
                    $oDbAdapter = $container->get(AdapterInterface::class);
                    $tableGateway = $container->get(PositionTable::class);

                    return new Controller\PositionController(
                        $oDbAdapter,
                        $tableGateway,
                        $container
                    );
                },
                # Installer
                Controller\InstallController::class => function($container) {
                    $oDbAdapter = $container->get(AdapterInterface::class);
                    return new Controller\InstallController(
                        $oDbAdapter,
                        $container->get(Model\PositionTable::class),
                        $container
                    );
                },
            ],
        ];
    } # getControllerConfig()
}
