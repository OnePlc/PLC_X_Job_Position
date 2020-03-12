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
        $oPosTbl = CoreController::$oServiceManager->get(PositionTable::class);

        try {
            $oArtTbl = CoreEntityController::$oServiceManager->get(\OnePlace\Article\Model\ArticleTable::class);
        } catch(\RuntimeException $e) {
            // no variant plugin present
        }

        try {
            $oVarTbl = CoreEntityController::$oServiceManager->get(\OnePlace\Article\Variant\Model\VariantTable::class);
        } catch(\RuntimeException $e) {
            // no variant plugin present
        }

        try {
            $oEvTbl = CoreEntityController::$oServiceManager->get(\OnePlace\Event\Model\EventTable::class);
        } catch(\RuntimeException $e) {
            // no variant plugin present
        }

        $aPositions = [];
        $fSubTotal = 0;
        $oPositionsDB = $oPosTbl->fetchAll(false,['job_idfs'=>$oJob->getID()]);
        if(count($oPositionsDB) > 0) {
            foreach($oPositionsDB as $oPos) {
                switch($oPos->type) {
                    case 'variant':
                        if(isset($oVarTbl)) {
                            $oVar = $oVarTbl->getSingle($oPos->article_idfs);
                            $oBaseArt = $oArtTbl->getSingle($oVar->article_idfs);
                            $oPos->article_idfs = $oBaseArt->getLabel().': '.$oVar->getLabel();
                        }
                        break;
                    case 'event':
                        if(isset($oEvTbl)) {
                            $oVar = $oVarTbl->getSingle($oPos->article_idfs);
                            $oEvent = $oEvTbl->getSingle($oPos->ref_idfs);
                            // Event Rerun Plugin Support
                            if($oEvent->root_event_idfs != 0) {
                                $oRoot = $oEvTbl->getSingle($oEvent->root_event_idfs);
                                $oEvent->label = $oRoot->label;
                                $oEvent->excerpt = $oRoot->excerpt;
                                $oEvent->featured_image = $oRoot->featured_image;
                                $oEvent->description = $oRoot->description;
                            }
                            $oPos->article_idfs = $oEvent->getLabel().' - '.date('d.m.Y',strtotime($oEvent->date_start)).': '.$oVar->getLabel();
                        }
                        if($oPos->description != '') {
                            $oPos->description = 'Widmung: '.$oPos->description;
                        }
                        break;
                    default:
                        break;
                }
                # Calculate Position Total if property exists
                $oPos->total = $oPos->amount*$oPos->price;
                $fSubTotal+=$oPos->total;
                $aPositions[] = $oPos;
            }
        }
        $aFields = [];
        $aUserFields = CoreEntityController::$oSession->oUser->getMyFormFields();
        if(array_key_exists('jobposition-single',$aUserFields)) {
            $aFieldsTmp = $aUserFields['jobposition-single'];
            if(count($aFieldsTmp) > 0) {
                # add all contact-base fields
                foreach($aFieldsTmp as $oField) {
                    if($oField->tab == 'jobposition-base') {
                        $aFields[] = $oField;
                    }
                }
            }
        }
        $aFields[] = (object)[
            'fieldkey' => 'total',
            'type' => 'currency',
            'label' => 'Total',
            'class' => 'col-md-2',
        ];
        $aFieldsByTab = ['jobposition-base'=>$aFields];
        # Pass Data to View - which will pass it to our partial
        return [
            # must be named aPartialExtraData
            'aPartialExtraData' => [
                # must be name of your partial
                'job_position'=> [
                    'aPositions'=>$aPositions,
                    'aFieldsByTab'=>$aFieldsByTab,
                    'fSubTotal'=>$fSubTotal,
                ]
            ]
        ];
    }
}
