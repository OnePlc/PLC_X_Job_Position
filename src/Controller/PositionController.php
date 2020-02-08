<?php
/**
 * PositionController.php - Main Controller
 *
 * Main Controller for Job Position Plugin
 *
 * @category Controller
 * @package Job\Position
 * @author Verein onePlace
 * @copyright (C) 2020  Verein onePlace <admin@1plc.ch>
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 1.0.0
 * @since 1.0.0
 */

declare(strict_types=1);

namespace OnePlace\Job\Position\Controller;

use Application\Controller\CoreEntityController;
use Application\Model\CoreEntityModel;
use OnePlace\Job\Position\Model\PositionTable;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\AdapterInterface;
use Application\Controller\CoreController;


class PositionController extends CoreEntityController {
    /**
     * Job Table Object
     *
     * @since 1.0.0
     */
    protected $oTableGateway;

    /**
     * JobController constructor.
     *
     * @param AdapterInterface $oDbAdapter
     * @param JobTable $oTableGateway
     * @since 1.0.0
     */
    public function __construct(AdapterInterface $oDbAdapter,PositionTable $oTableGateway,$oServiceManager) {
        $this->oTableGateway = $oTableGateway;
        $this->sSingleForm = 'jobposition-single';
        parent::__construct($oDbAdapter,$oTableGateway,$oServiceManager);

        if($oTableGateway) {
            # Attach TableGateway to Entity Models
            if(!isset(CoreEntityModel::$aEntityTables[$this->sSingleForm])) {
                CoreEntityModel::$aEntityTables[$this->sSingleForm] = $oTableGateway;
            }
        }
    }

    /**
     * Save Position and attach it to job
     *
     * @param $oJob saved Job
     * @param $aFormData raw Form Data
     * @param $sState state of saving
     * @return bool true or false
     * @since 1.0.0
     */
    public function attachPositionToJob($oJob,$aRawFormData,$sState) {
        $aFormData = $this->parseFormData($aRawFormData);

        # Parse Raw Form Data for Position Fields
        $aPositionFields = $this->getFormFields($this->sSingleForm);
        $aPositionData = [];

        foreach($aPositionFields as $oField) {
            if(array_key_exists($oField->fieldkey,$aFormData)) {
                $aPositionData[$oField->fieldkey] = $aFormData[$oField->fieldkey];
            }
        }

        # Link Job to Position
        $aPositionData['job_idfs'] = $oJob->getID();
        /**
        if(isset($aRawFormData[$this->sSingleForm.'_position_primary_id'])) {
            $aPositionData['Position_ID'] = $aRawFormData[$this->sSingleForm.'_position_primary_id'];
        }
         * **/

        # Generate New Position
        $oPosition = $this->oTableGateway->generateNew();

        # Attach Data
        $oPosition->exchangeArray($aPositionData);

        # Save to Database
        $iPositionID = $this->oTableGateway->saveSingle($oPosition);

        return true;
    }

    public function attachPositionForm($oJob) {
        # Try go get Position Table
        try {
            $oTable = CoreController::$oServiceManager->get(PositionTable::class);
        } catch (\RuntimeException $e) {
            echo '<div class="alert alert-danger"><b>Error:</b> Could not load address table</div>';
        }

        # Get all positions for current job
        $oPositionsDB = $oTable->fetchAll(false,['job_idfs'=>$oJob->getID()]); //

        # save them to array
        $aPositions = [];
        if(count($oPositionsDB) > 0) {
            foreach($oPositionsDB as $oPos) {
                $aPositions[] = $oPos;
            }
        }

        # get position form
        $oForm = CoreController::$aCoreTables['core-form']->select(['form_key'=>'jobposition-single']);

        $aAllMyFields =  CoreController::$oSession->oUser->getMyFormFields();
        $aMyFields = (array_key_exists('jobposition-single',$aAllMyFields)) ? $aAllMyFields['jobposition-single'] : [];

        # get position form fields
        $aFields = [
            'position-base' => $aMyFields,
        ];

        # Pass Data to View - which will pass it to our partial
        return [
            # must be named aPartialExtraData
            'aPartialExtraData' => [
                # must be name of your partial
                'job-position'=> [
                    'oPositionsDB'=>$aPositions,
                    'oForm'=>$oForm,
                    'aFormFields'=>$aFields,
                ]
            ]
        ];
    }
}
