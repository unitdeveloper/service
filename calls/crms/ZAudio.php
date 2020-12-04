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
use asrorz\actives\Platform;
use asrorz\actives\Setting;
use asrorz\actives\TblCall;
use asrorz\cores\Az;
use asrorz\cores\ZFrame;
use asrorz\cores\ZFileHelper;
use asrorz\zitems\ZAudioItem;
use asrorz\zitems\ZFirebirdItem;
use Underscore\Types\Arrays;
use yii\helpers\FileHelper;

class ZAudio extends ZFrame
{


	/**
	 *
	 *  Private Fields
	 */

	private $_aRecordItem = [];
	private $_sRecordsURL;

	private $_sResultFileName;

	private $_sFileResult;
	private $_sFolderResult;

	private $_sRecordNameA;
	private $_sRecordNameB;

	private $_sFileA;
	private $_sFileB;

	private $_sFileAUrl;
	private $_sFileBUrl;


	/** @var Company $_company */
	protected $_company;

	/** @var Platform $_platform */
	protected $_platform;

	/** @var TblCall $_model */
	protected $_model;

	protected $_aCompany;
	protected $_aPlatform;

	protected $_sDate;
	protected $_sStartDate;

	protected $_sPrefix;
	protected $_sSuffix;


	public function zinit()
	{
		$this->_aCompany = $this->zcore->zActive->companies;
		$this->_aPlatform = $this->zcore->zActive->platforms;
	}


	public function play(int $iId, bool $bInline = true)
	{

		$zItem = $this->_path($iId);

		if (null !== $zItem) {

			if (empty($zItem->sPath))
				return Az::warning("Cannot Play Audio for Call ID = {$iId} | Record Path is Empty!", Cat_CannotPlay);

			if ($this->zcore->boot->isCLI())
				return Az::trace("Response SendFile Called | {$zItem->sPath}");

			$response = Az::$app->response;
			$response->sendFile($zItem->sPath, $zItem->sName, [
				'inline' => $bInline
			])
				->send();

			return true;
		}


		return false;

	}


	public function _path(int $iId)
	{

		$zItem = new ZAudioItem();
		$zItem->sName = "{$iId}.wav";

		if ($this->zcore->identity->IsDemo) {
			$zItem->sPath = $this->_scan();
			return $zItem;
		}

		$model = TblCall::findOne($iId);

		if (!file_exists($model->RecordFile)) {
			$this->_model($model);
			$model = $this->_model;
		}

		if (file_exists($model->RecordFile))
			$zItem->sPath = $model->RecordFile;

		return $zItem;

	}

	private function _scan(string $sUserName = null)
	{
		if ($sUserName === null)
			$sUserName = $this->zcore->identity->Username;

		$sFolder = Az::getAlias("@app/asrorw/audios/{$sUserName}");

		if (!file_exists($sFolder))
			return Az::error($sFolder, 'Audio Demo Folder Does not Exist!');

		$aAudio = ZFileHelper::findFiles($sFolder, []);

		if (empty($aAudio))
			return Az::error($sFolder, 'There are No Audio Files in Demo Directory!');

		$aAudio = Arrays::invoke($aAudio, [ZFileHelper::class, 'normalizePath']);

		return Arrays::random($aAudio);
	}


	public function all(string $sDate = null): void
	{
		$this->_sDate = $sDate;

		$this->_date('Audio');

		$aCalls = $this->zcore->zActiveCall->dataAudio($this->_sStartDate);

		$this->_calls($aCalls);

	}

	protected function _date(string $sPrefix)
	{

		Az::eol();
		Az::start(__METHOD__);
		switch ($this->_sDate) {
			case ZDate::CurrDay:
				$this->_sStartDate = $this->zcore->zDate->dateTime_Day_Start();
				break;

			case ZDate::CurrMonth:
				$this->_sStartDate = $this->zcore->zDate->dateTime_Month_Start();
				break;

			case ZDate::CurrYear:
				$this->_sStartDate = $this->zcore->zDate->dateTime_Year_Start();
				break;

			case ZDate::PrevDay:
				$this->_sStartDate = $this->zcore->zDate->dateTime_Day_Start('-1 days');
				break;

			case ZDate::PrevMonth:
				$this->_sStartDate = $this->zcore->zDate->dateTime_Month_Start('-1 months');
				break;
			case ZDate::PrevYear:
				$this->_sStartDate = $this->zcore->zDate->dateTime_Year_Start('-1 years');
				break;

			default:
				$this->_sStartDate = null;
		}


		if (!$this->_sStartDate) {
			$sSettingsName = $sPrefix;

			$this->_sStartDate = Setting::getAndSet($sSettingsName, $this->zcore->zDate->dateTime(), $this->zcore->zDate->dateTime_Day_Start());

			Az::trace($this->_sStartDate, "| StartDate From Setting Name = {$sSettingsName}", null, 2);
		} else
			Az::trace($this->_sStartDate, '| StartDate Given By Command Line', null, 2);

	}


	public function one(int $iId)
	{
		$model = TblCall::findOne($iId);

		$this->_model($model);
	}


	protected function _calls(array $aCalls)
	{
		$iCallItemsCount = \count($aCalls);

		Az::trace($iCallItemsCount, 'Call Items Count From CRM DB', null, 1);

		/**
		 *
		 *
		 * Process All Items
		 */

		foreach ($aCalls as $iKey => $aAttr) {

			$this->_sPrefix = "{$iKey}/{$iCallItemsCount} |";

			$model = $this->zcore->zActive->toModel(TblCall::class, $aAttr);
			$this->_model($model);


		}

	}


	public function _model(TblCall $model)
	{

		$this->_model = $model;


		/**
		 *
		 * Init Core
		 */

		$this->_company = $this->_aCompany[$this->_model->CompanyID];
		$this->_platform = $this->_aPlatform[$this->_company->PlatformID];


		$this->_sSuffix = "ID={$this->_model->Id} | {$this->_company->Name}/{$this->_company->Id} | {$this->_platform->VoipIP} | StartDate={$this->_model->StartDate} | Guids={$this->_model->RecordGuids}";

		if (file_exists($this->_model->RecordFile))
			return Az::trace("{$this->_sPrefix} RECORD ALREADY PROCESSED | {$this->_sSuffix}", null, null, 2);


		if (!$this->_fbdata())
			return false;
		else {
			$this->_result();

			if (file_exists($this->_sFileResult))
				Az::trace("{$this->_sPrefix} AUDIO FILE EXISTS | {$this->_sSuffix}", null, null, 2);
			else {
				Az::trace("{$this->_sPrefix} CREATE AUDIO | {$this->_sSuffix}", null, null, 2);

				if (!$this->_dload())
					return false;

				$this->_merge();
			}

			$this->_model->RecordFile = $this->_sFileResult;

		}

		if ($this->_model->save())
			Az::trace($this->_model->RecordFile, 'TblCall Audio Success Saved! RecordFile', Cat_ModelSave);

		return true;
	}

	public function clean()
	{

		$db = Az::$app->db;

		$db->createCommand()->delete('setting', ['like', 'Name', 'Audio'])->execute();

		Az::trace('All Tables Are Truncated!');

		if ($this->zcore->zUtils->cacheFlush())
			Az::trace('Data Success Flushed!');
	}

	private function _fbdata()
	{

		/**
		 *
		 * Infinity Db Connect
		 */

		$dbParam = new ZFirebirdItem();

		$dbParam->sHostName = $this->_platform->VoipIP;
		$dbParam->sDbName = $this->_platform->VoipDB;
		$dbParam->sDbType = ZFirebirdItem::Infinity;

		$dbParam->zinit();

		$this->zcore->zFirebird->param($dbParam);

		$this->_sRecordsURL = "http://{$dbParam->sHostName}:8429/";

		$aRecord = $this->zcore->zFirebird->sqlFile('Audio', [
			'RecordGuids' => $this->_model->RecordGuids,
		]);

		Az::trace($aRecord, 'aRecord', Cat_AudioInfo);

		foreach ($aRecord as $key => $value) {
			if ($value['ISRECORDED'] === 1) {
				$this->_aRecordItem = $value;
				break;
			}
		}

		if (empty($this->_aRecordItem)) {
			Az::info("RecordItem is Empty | {$this->_sSuffix}");
			return false;
		}


		$this->_sRecordNameA = $this->_aRecordItem['RECORDNAMEA'];
		$this->_sRecordNameB = $this->_aRecordItem['RECORDNAMEB'];

		return Az::trace($this->_aRecordItem, 'RECORDITEM', Cat_AudioInfo);
	}


	private function _result()
	{
		/**
		 *
		 * Folder Result
		 */

		$sDateExtract = $this->zcore->zDate->extractDate($this->_sRecordNameA);

		$this->_sFolderResult = "{$this->zcore->boot->sFolder_Records}/{$sDateExtract}";

		Az::trace($this->_sFolderResult, 'Folder Result');

		if (!file_exists($this->_sFolderResult))
			FileHelper::createDirectory($this->_sFolderResult);


		$sRecordGuids = str_replace(['{', '}'], '', $this->_model->RecordGuids);

		$this->_sResultFileName = "ID-{$this->_model->Id}-PL-{$this->_platform->Id}.wav";

		$this->_sFileA = $this->zcore->boot->sFolder_Agents . DS . $this->_sResultFileName;
		$this->_sFileB = $this->zcore->boot->sFolder_Clients . DS . $this->_sResultFileName;

		$this->_sFileResult = "{$this->_sFolderResult}/{$this->_sResultFileName}";
		$this->_sFileResult = FileHelper::normalizePath($this->_sFileResult);
	}

	private function _dload()
	{
		/**
		 * Download and put conversation audio to source directory
		 */


		$this->_sFileAUrl = $this->_sRecordsURL . $this->_sRecordNameA;
		Az::trace($this->_sFileAUrl, 'sFileAUrl');

		$dataA = null;

		try {
			$dataA = file_get_contents($this->_sFileAUrl);
		} catch (\Exception $e) {
			return Az::warning($this->_sFileAUrl, 'Problem with File Get Contents', Cat_GetContent);
		}

		if (file_put_contents($this->_sFileA, $dataA) === false)
			return Az::warning($this->_sFileAUrl, 'Problem with File Put Contents');

		Az::trace($this->_sFileA, 'FileA Success Downloaded');

		$this->_sFileBUrl = $this->_sRecordsURL . $this->_sRecordNameB;
		Az::trace($this->_sFileBUrl, '$this->_sFileBUrl');

		$dataB = null;

		try {
			$dataB = file_get_contents($this->_sFileBUrl);
		} catch (\Exception $e) {
			return Az::warning($this->_sFileBUrl, 'Problem with File Get Contents', Cat_GetContent);
		}

		if (file_put_contents($this->_sFileB, $dataB) === false)
			return Az::warning($this->_sFileBUrl, 'Problem with File Put Contents');


		Az::trace($this->_sFileB, 'FileB Success Downloaded');

		return true;
	}

	private function _merge()
	{


		if (!$this->zcore->zExec->sox($this->_sFileA, $this->_sFileB, $this->_sFileResult))
			return Az::error($this->_sSuffix, 'Cannot Process and Merge AudioFiles with Sox');

		@unlink($this->_sFileA);
		@unlink($this->_sFileB);

		return true;
	}


}
