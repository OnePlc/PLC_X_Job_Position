<?php
/**
 * Module.php - Module Class
 *
 * Module Class File for Job-Position Module
 *
 * @category Config
 * @package Job
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
use OnePlace\Job\Position\Controller\PositionController;

class Module {
    /**
     * Module Version
     *
     * @since 1.0.0
     */
    const VERSION = '1.0.0';

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
//        $application = $e->getApplication();
//        $container    = $application->getServiceManager();
//        $oDbAdapter = $container->get(AdapterInterface::class);
//        $tableGateway = $container->get(\OnePlace\Job\Model\JobTable::class);
//
//        # Register Position Plugin Hook
//        CoreEntityController::addHook('article-index-before-paginator',(object)['sFunction'=>'filterIndexByState','oItem'=>new PositionController($oDbAdapter,$tableGateway,$container)]);
    }


    /**
     * Load Models
     */
    public function getServiceConfig() : array {
        return [
            'factories' => [
                # Position Module - Base Model
                Model\PositionTable::class => function($container) {
                    $tableGateway = $container->get(Model\PositionTableGateway::class);
                    return new Model\PositionTable($tableGateway,$container);
                },
                Model\PositionTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Position($dbAdapter));
                    return new TableGateway('job-position', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }
}
