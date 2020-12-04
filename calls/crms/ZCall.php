<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace asrorz\frame;


use asrorz\actives\Company;
use asrorz\actives\Enum;
use asrorz\actives\Platform;
use asrorz\actives\Result;
use asrorz\actives\Setting;
use asrorz\actives\Subject;
use asrorz\actives\TblCall;
use asrorz\actives\User;
use asrorz\cores\Az;
use asrorz\cores\ZFrame;
use asrorz\enum\TblCall\Direction;
use asrorz\select\ZTblCallSelect;
use asrorz\zitems\ZFirebirdItem;
use Fusonic\Linq\Linq;
use yii\helpers\ArrayHelper;

class ZCall extends ZFrame
{


	/**
	 *
	 * For Stats
	 */

	public $aCallDirection;
	public $bIsOutgoing;

	public $aStatSubject;
	public $aStatEnum;
	public $statCall;
	public $statResult;
	public $statAgent;

	/** @var TblCall $model */
	public $model;


	protected $_aCompanyIDs;
	protected $_sStartDate;
	protected $_sDate;


	/*  @var Company $_company */
	protected $_company;

	/*  @var Platform $_platform */
	protected $_platform;


	/**
	 *
	 * Main Variables
	 */

	protected $_aChoosedCompanies;

	protected $_aCategory;
	protected $_aPlatform;
	protected $_aCompany;
	protected $_aSubject;
	protected $_aField;
	protected $_aEnum;
	protected $_aUser;
	protected $_aResult;
	protected $_aDeparter;


	public function zinit()
	{
		Az::start(__METHOD__);


		/**
		 *
		 *
		 * Fill Arrays
		 */

		$this->_aCategory = $this->zcore->zActive->categories;
		$this->_aPlatform = $this->zcore->zActive->platforms;

		$this->zcore->zActive->bIndex = false;
		$this->_aCompany = $this->zcore->zActive->companiesIsActive();
		$this->zcore->zActive->bIndex = true;

		$this->_aSubject = $this->zcore->zActive->subjects;
		$this->_aField = $this->zcore->zActive->fields;
		$this->_aDeparter = $this->zcore->zActive->departers;


		$this->zcore->zActive->sIndex = ZActive::Index_Guids;

		$this->_aUser = $this->zcore->zActive->agents();
		$this->_aEnum = $this->zcore->zActive->enums;
		$this->_aResult = $this->zcore->zActive->results;

		$this->zcore->zActive->sIndex = ZActive::Index_Id;

		return Az::trace(PHP_EOL . 'All Data For Processing are Ready!', '', null, 2);
	}


	public function clean()
	{

		$db = Az::$app->db;

		$db->createCommand()->truncateTable('tbl_call')->execute();

		$db->createCommand()->delete('setting', ['like', 'Name', 'Call'])->execute();

		$db->createCommand()->truncateTable('stat_agent')->execute();
		$db->createCommand()->truncateTable('stat_call')->execute();
		$db->createCommand()->truncateTable('stat_enum')->execute();
		$db->createCommand()->truncateTable('stat_result')->execute();
		$db->createCommand()->truncateTable('stat_subject')->execute();

		Az::trace('All Tables Are Truncated!');

		if ($this->zcore->zUtils->cacheFlush())
			Az::trace('Data Success Flushed!');
	}


	public function num(int $iCompanyID, string $sCallNumber)
	{

		Az::trace($iCompanyID, 'Company ID');
		Az::trace($sCallNumber, 'TblCall Number');

		$this->_aCompanyIDs[] = $iCompanyID;

		$this->_choose();


		/**
		 *
		 *
		 * Perform Query
		 */
		$this->_company = $this->_aChoosedCompanies[0];


		if (empty($this->_company->PlatformID)) {
			return Az::warning($this->_company->Name, 'Platform Omitted for Company');
		}

		$this->_platform = $this->_aPlatform[$this->_company->PlatformID];

		$dbParam = new ZFirebirdItem();

		$dbParam->sHostName = $this->_platform->CrmIP;
		$dbParam->sDbName = $this->_platform->CrmDB;
		$dbParam->sDbType = ZFirebirdItem::Terrasoft;

		$dbParam->zinit();

		$this->zcore->zFirebird->param($dbParam);

		$aCalls = $this->zcore->zFirebird->sqlFile('CallByID', [
			'CallNumber' => $sCallNumber,
		]);

		$this->_calls($aCalls);

		return true;
	}

	public function betw(int $iCompanyID, string $sStartDate, string $sEndDate)
	{

		Az::trace($iCompanyID, 'Company ID');

		$this->_aCompanyIDs[] = $iCompanyID;
		$this->_choose();


		/**
		 *
		 *
		 * Perform Query
		 */
		$this->_company = $this->_aChoosedCompanies[0];

		if (empty($this->_company->PlatformID)) {
			return Az::warning($this->_company->Name, 'Platform Omitted for Company');
		}

		$this->_platform = $this->_aPlatform[$this->_company->PlatformID];

		$dbParam = new ZFirebirdItem();

		$dbParam->sHostName = $this->_platform->CrmIP;
		$dbParam->sDbName = $this->_platform->CrmDB;
		$dbParam->sDbType = ZFirebirdItem::Terrasoft;

		$dbParam->zinit();

		$this->zcore->zFirebird->param($dbParam);

		$aCalls = $this->zcore->zFirebird->sqlFile('CallBetween', [
			'TypeID' => $this->_company->Guids,
			'StartDate' => $sStartDate,
			'EndDate' => $sEndDate,
		]);

		$this->_calls($aCalls);

		return true;
	}

	protected function _date(string $sPrefix)
	{

		Az::start(__METHOD__);
		switch ($this->_sDate) {
			case ZDate::CurrDay:
				$this->_sStartDate = $this->zcore->zDate->dateTimeFB_Day_Start();
				break;

			case ZDate::CurrMonth:
				$this->_sStartDate = $this->zcore->zDate->dateTimeFB_Month_Start();
				break;

			case ZDate::CurrYear:
				$this->_sStartDate = $this->zcore->zDate->dateTimeFB_Year_Start();
				break;

			case ZDate::PrevDay:
				$this->_sStartDate = $this->zcore->zDate->dateTimeFB_Day_Start('-1 days');
				break;

			case ZDate::PrevMonth:
				$this->_sStartDate = $this->zcore->zDate->dateTimeFB_Month_Start('-1 months');
				break;
			case ZDate::PrevYear:
				$this->_sStartDate = $this->zcore->zDate->dateTimeFB_Year_Start('-1 years');
				break;

			default:
				$this->_sStartDate = null;

		}

		if (!$this->_sStartDate) {
			$sSettingsName = "{$sPrefix}_{$this->_company->Id}";

			$this->_sStartDate = Setting::getAndSet($sSettingsName, $this->zcore->zDate->dateTimeFB(), $this->zcore->zDate->dateTimeFB_Day_Start());

			Az::trace($this->_sStartDate, "| StartDate From Setting Name = {$sSettingsName} | {$this->_company->Name}", null, 2);
		} else
			Az::trace($this->_sStartDate, '| StartDate Given By Command Line', null, 2);

	}


	public function one(int $iCompanyID, string $sDate = null)
	{
		$this->_sDate = $sDate;
		$this->_aCompanyIDs[] = $iCompanyID;

		$this->_choose();
		$this->_looper();

	}


	public function self(string $sDate = null)
	{

		$this->_sDate = $sDate;
		$iServerID = $this->zcore->zProcess->serverID();

		$this->_aCompanyIDs = $this->zcore->zProcess->companyIDs($iServerID, $this->_aCompany);
		$this->_choose();
		$this->_looper();

	}


	public function all(string $sDate = null)
	{
		$this->_sDate = $sDate;

		$this->_aCompanyIDs = ArrayHelper::getColumn($this->_aCompany, 'Id');

		$this->_choose();
		$this->_looper();

	}


	public function sId(int $iServerID, string $sDate = null)
	{
		Az::trace($iServerID, 'ServerID');
		$this->_sDate = $sDate;

		$this->_aCompanyIDs = $this->zcore->zProcess->companyIDs($iServerID, $this->_aCompany);

		$this->_choose();
		$this->_looper();

	}


	protected function _choose()
	{

		Az::start(__METHOD__);
		Az::trace($this->_aCompanyIDs, 'Gived _aCompanyIDs');

		$this->_aChoosedCompanies = Linq::from($this->_aCompany)
			->where(function (Company $company) {
				if (ArrayHelper::isIn($company->Id, $this->_aCompanyIDs))
					return true;

				return false;
			})
			->toArray();

		Az::trace(count($this->_aChoosedCompanies), 'Count Of Choosed Companies');

	}


	protected function _looper()
	{

		switch ($this->_sDate) {
			case ZDate::CurrYear:
			case ZDate::CurrMonth:
			case ZDate::CurrDay:
			case ZDate::PrevYear:
			case ZDate::PrevMonth:
			case ZDate::PrevDay:
				$this->_companies();
				break;

			default:
				for ($i = 1; $i <= 50; $i++) {
					Az::trace($i, 'Looper!');
					$this->_companies();
					sleep(1);
				}
		}
	}


	protected function _companies()
	{
		/** @var Company $company */

		Az::start(__METHOD__);

		if (!$this->_aChoosedCompanies)
			return Az::error('Array of Companies Are Empty!');


		foreach ($this->_aChoosedCompanies as $company) {

			$this->_company = $company;

			if (empty($this->_company->PlatformID)) {
				Az::warning($this->_company->Name, 'Platform Omitted for Company');
				continue;
			}

			$this->_platform = $this->_aPlatform[$this->_company->PlatformID];

			$this->_company();


		}

		return true;
	}


	protected function _company()
	{

		Az::start(__METHOD__);

		$dbParam = new ZFirebirdItem();

		$dbParam->sHostName = $this->_platform->CrmIP;
		$dbParam->sDbName = $this->_platform->CrmDB;
		$dbParam->sDbType = ZFirebirdItem::Terrasoft;

		$dbParam->zinit();

		$this->zcore->zFirebird->param($dbParam);


		/**
		 *
		 *
		 *  Process StartDate
		 *  Setting
		 */


		$this->_date('Call');


		/**
		 *
		 *
		 * Perform Query
		 */

		$aCalls = $this->zcore->zFirebird->sqlFile('Call', [
			'StartDate' => $this->_sStartDate,
			'TypeID' => $this->_company->Guids,
		]);


		$this->_calls($aCalls);
	}


	public function many(array $aCompanyIDs, string $sDate = null)
	{
		$this->_sDate = $sDate;
		$this->_aCompanyIDs = $aCompanyIDs;

		$this->_choose();
		$this->_looper();

	}

	protected function _calls(array $aCalls)
	{

		$iCallItemsCount = \count($aCalls);
		Az::trace($this->_company->Name, 'Company Name', null, 2);
		Az::trace($iCallItemsCount, 'Call Items Count From Terrasoft', null, 1);


		/**
		 *
		 *
		 * Process All Items
		 */


		for ($iIndex = 1; $iIndex <= $iCallItemsCount; $iIndex++) {

			$aCall = $aCalls[$iIndex];
			$aCall = $this->zcore->zActive->arrayType($aCall, ZTblCallSelect::IntRaw());


			/** @var TblCall $model */
			$model = TblCall::find()
				->where([
					'Guids' => $aCall['ID']
				])
				->limit(1)
				->one();


			$sPrefix = "# {$iIndex}/{$iCallItemsCount}";

			$sSuffix = "{$this->_company->Name}/{$this->_company->Id} |{$this->_platform->CrmIP} | StartDate = {$aCall['StartDate']}";


			if ($model) {
				Az::trace("{$sPrefix} | UPDATE | ID = {$model->Id} | {$sSuffix}");
			} else {
				Az::trace("{$sPrefix} | CREATE | CallNumber = {$aCall['ID']} | {$sSuffix}");

				$model = new TblCall();
			}


			$this->_model($model, $aCall);

		}

	}


	protected function _model($model, array $aItem)
	{
		/** @var Subject $subject */
		/** @var Enum $enum */


		$this->model = $model;

		$this->aStatSubject = null;
		$this->aStatEnum = null;
		$this->statResult = null;
		$this->statAgent = null;

		/**
		 *
		 *
		 * Dates
		 */

		$this->model->StartDate = $aItem['StartDate'];
		$this->model->EndDate = $aItem['EndDate'];

		$this->model->CreatedOn = $aItem['CreatedOn'];
		$this->model->ModifiedOn = $aItem['ModifiedOn'];

		$this->model->Date_1 = $aItem['Date_1'];
		$this->model->Date_2 = $aItem['Date_2'];


		/**
		 *
		 *
		 * Times
		 */

		$this->model->TalkTime = $aItem['TalkTime'];
		$this->model->HoldTime = $aItem['HoldTime'];
		$this->model->DIDNumber = $aItem['NUMERKUDO'];
		$this->model->ProcessingTime = $aItem['ProcessingTime'];


		/**
		 *
		 *
		 * Numbers
		 */

		$this->model->FIO = $aItem['FIO'];
		$this->model->PhoneNumber = $aItem['PhoneNumber'];
		$this->model->CallNumber = $aItem['CallNumber'];
		$this->model->SwitchToPhoneNumber = $aItem['SwitchToPhoneNumber'];


		/**
		 *
		 *
		 * Guids
		 */

		$this->model->Guids = $aItem['ID'];
		$this->model->RecordGuids = $aItem['IntegrationID'];


		/**
		 *
		 *
		 * Direction
		 */

		if ($aItem['DirectionID']) {
			$this->model->Direction = Direction::Direct_Out;
			$this->aCallDirection = [
				'CallsOut' => 1
			];
			$this->bIsOutgoing = true;
		} else {
			$this->model->Direction = Direction::Direct_In;
			$this->aCallDirection = [
				'CallsIn' => 1
			];
			$this->bIsOutgoing = false;
		}


		Az::trace(Direction::$core[$this->model->Direction], '#    Added Direction', Cat_InsertField);


		/**
		 *
		 *
		 * Extra Variables
		 */


		$this->model->CompanyID = $this->_company->Id;


		/**
		 *
		 *
		 * Result
		 */

		$sItemResult = $aItem['ResultID'];

		/** @var Result $result */

		if (!empty($sItemResult)) {
			if (ArrayHelper::keyExists($sItemResult, $this->_aResult))
				$result = $this->_aResult[$sItemResult];
			else
				Az::warning($sItemResult, 'Undefined index in Results');

			if ($result) {
				$this->model->ResultID = $result->Id;
				Az::trace($result->Name, '#    Added Result', Cat_InsertField);
				$this->statResult = $result;
			}
		}  else {
			$this->model->ResultID = null;
		}


		/**
		 *
		 *
		 * Get Enums
		 */

		$aEnums = [
			'Enum_1',
			'Enum_2',
			'Enum_3',
			'Enum_4',
			'Enum_5',
			'Enum_6',
			'Enum_7',
			'Enum_8',
			'WHO',
		];


		foreach ($aEnums as $enumID) {
			if (!empty($aItem[$enumID])) {

				if (ArrayHelper::keyExists($aItem[$enumID], $this->_aEnum))
					$enum = $this->_aEnum[$aItem[$enumID]];
				else {
					Az::warning("Undefined index in Enum!  Company = {$this->_company->Name} Enum ID = {$aItem[$enumID]} | ID = {$this->model->Id} | CallNumber = {$this->model->CallNumber}");
					continue;
				}

				if ($enum) {
					$this->model->setAttribute($enumID, $enum->Id);
					Az::trace($enum->Name, '#    Added Enum', Cat_InsertField);

					$this->aStatEnum[] = $enum;
				}
			} else {
				$this->model->setAttribute($enumID, null);
			}
		}


		/**
		 *
		 *
		 * Subjects
		 */

		$aSubjects = Linq::from($this->_aSubject)
			->where(function (Subject $subject) {
				if ($subject->CompanyID === $this->_company->Id)
					return true;
				return false;
			})
			->toArray();

		$callSubjectString = null;


		foreach ($aSubjects as $subject) {

			if ($aItem[$subject->Name] === 1) {
				$callSubjectString .= '- ' . $subject->Title . PHP_EOL . '<br/>';
				Az::trace($subject->Title, '#    Added Subject', Cat_InsertField);

				$this->aStatSubject[] = $subject;
			}
		}

		$this->model->Subjects = $callSubjectString;


		/**
		 *
		 *
		 * User
		 */

		/** @var User $users */

		$users = Linq::from($this->_aUser)
			->where(function (User $user) use ($aItem) {
				if ($user->Guids === $aItem['CreatedByID'])
					return true;

				return false;
			})->toArray();


		if (!empty($users)) {

			/** @var User $user */
			$user = $users[0];
			$this->model->Operator = $user->Id;

			Az::trace($user->Username, '#    Added Agent', Cat_InsertField);

			$this->statAgent = $user;

		}


		/**
		 *
		 *
		 * Bools
		 */

		for ($i = 1; $i <= 30; $i++) {
			$sFieldName = "Bool_$i";

			$this->model->setAttribute($sFieldName, $aItem[$sFieldName]);

			if ($aItem[$sFieldName])
				Az::trace($sFieldName, '#    Added Bool', Cat_InsertField);
		}


		/**
		 *
		 *
		 * Texts
		 */

		for ($i = 1; $i <= 14; $i++) {
			$sFieldName = "Text_$i";

			$this->model->setAttribute($sFieldName, $aItem[$sFieldName]);

			if ($aItem[$sFieldName])
				Az::trace($sFieldName, '#    Added Text', Cat_InsertField);
		}
		

		/**
		 *
		 *
		 * Dates
		 */

		for ($i = 1; $i <= 2; $i++) {
			$sFieldName = "Date_$i";

			$this->model->setAttribute($sFieldName, $aItem[$sFieldName]);

			if ($aItem[$sFieldName])
				Az::trace($sFieldName, '#    Added Date', Cat_InsertField);
		}


		/**
		 *
		 *
		 * Set Is Counted
		 */

		if (!$this->model->IsCounted)
			if ($this->zcore->zStat->run($this))
				$this->model->IsCounted = 1;


		/**
		 *
		 *
		 * Save TblCall
		 */

		if ($this->model->isNewRecord) {
			if ($this->model->save())
				Az::trace($this->model->Id, 'TblCall Success Saved! ID', Cat_ModelSave);

		} else {
			$aChangedAttributes = $this->model->getDirtyAttributes();

			if (!empty($aChangedAttributes))
				if ($this->model->save())
					Az::trace($aChangedAttributes, 'TblCall Changes Success Saved!', Cat_ModelSave);

		}

	}


}
