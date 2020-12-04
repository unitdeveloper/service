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

namespace zetsoft\service\calls;


use asrorz\actives\Enum;
use asrorz\actives\TblCall;
use asrorz\cores\Az;
use asrorz\cores\ZActiveQuery;
use asrorz\cores\ZFrame;
use asrorz\select\ZTblCallSelect;
use yii\helpers\ArrayHelper;

class ZActiveCall extends ZFrame
{


	/* @var Enum[] $_aEnum */
	private $_aEnum;


	public function zinit()
	{
		$this->_aEnum = $this->zcore->zActive->enums;
	}


	public function zcore($bSelect = true)
	{

		$qSelect = TblCall::find()
			->where(['or', ['and', ['<>', 'ResultID', 17], ['<>', 'ResultID', 8]], ['ResultID' => null]])
			->andWhere(['IsDeleted' => null])
			->orderBy(['StartDate' => SORT_ASC])
			->asArray();

		$qCount = TblCall::find()
			->where(['or', ['and', ['<>', 'ResultID', 17], ['<>', 'ResultID', 8]], ['ResultID' => null]])
			->andWhere(['IsDeleted' => null]);

		if ($bSelect)
			return $qSelect;
		else
			return $qCount;
	}


	public function sampleApi(int $iCompanyID, int $iID = null)
	{
		$Q = TblCall::find()
			->where([
				'CompanyID' => $iCompanyID
			])
			->limit(1);

		if ($iID === null)
			return $Q->one();

		return $Q
			->andWhere(['Id' => $iID])
			->one();

	}


	public function sampleAudio(int $iID)
	{

		$Q = TblCall::find()
			->select(ZTblCallSelect::Audio)
			->andWhere(['>', 'TalkTime', 0,])
			->andWhere(['CompanyID' => $iID])
			->andWhere(['>=', 'StartDate', '2018-04-30'])
			->limit(1);


		return $Q->one();
	}


	public function isOut($model)
	{
		if (\is_object($model))
			$iDirection = (int)$model->Direction;
		else
			$iDirection = (int)$model['Direction'];

		return $iDirection === 1;
	}


    /**
     *
     * Function  dataAPI
     * @param int $iCompanyID
     * @param string $sStartDate
     * @param string $sEndDate
     * @return  array|ZActiveQuery[]
     * @throws \Exception
     */
    public function dataAPI(int $iCompanyID, string $sStartDate, string $sEndDate)
	{

		$Q = $this->zcore();

		$Q
			->select(ZTblCallSelect::Api())
			->andWhere([
				'CompanyID' => $iCompanyID,
			])
			->andWhere(['between', 'StartDate', $sStartDate, $sEndDate]);

		$aReturn = $Q->all();
		Az::count($aReturn, __FUNCTION__);

		return $aReturn;
	}


	public function dataAudio(string $sStartDate): array
	{
		$Q = $this->zcore();

		$Q
			->select(ZTblCallSelect::Audio)
			->andWhere(['>=', 'StartDate', $sStartDate])
			->andWhere(['>', 'TalkTime', 0,])
			->andWhere(['RecordFile' => null]);

		$aReturn = $Q->all();
		Az::count($aReturn, __FUNCTION__);

		return $aReturn;
	}


	public function dataStat(string $sStartDate, string $sEndDate, int $iCompanyID = 0): array
	{
		$Q = $this->zcore();

		$Q
			->select(ZTblCallSelect::Stat)
			->andWhere([
				'between', 'StartDate', $sStartDate, $sEndDate
			]);

		if ($iCompanyID !== 0)
			$Q->andWhere(['CompanyID' => $iCompanyID]);

		Az::sql($Q, __METHOD__);

		$aReturn = $Q->all();

		Az::count($aReturn, __FUNCTION__);

		return $aReturn;
	}


	public function dataMailer(string $sStartDate)
	{
		$Q = $this->zcore();

		$Q
			->select(ZTblCallSelect::Mailer())
			->andWhere(['>=', 'StartDate', $sStartDate])
			->andWhere(['IsMailed' => null]);

		$aReturn = $Q->all();
		Az::count($aReturn, __FUNCTION__);

		return $aReturn;

	}


	public function countStat(string $sStartDate, string $sEndDate, int $iCompanyID = 0, string $sWhere = null)
	{
		$Q = $this->zcore(false);

		$Q
			->andWhere([
				'between', 'StartDate', $sStartDate, $sEndDate
			]);

		if ($iCompanyID !== 0)
			$Q->andWhere(['CompanyID' => $iCompanyID]);

		if (!empty($sWhere))
			$Q->andWhere($sWhere);

		$iReturn = $Q->count('Id');

		Az::sql($Q, __METHOD__);

		return $iReturn;
	}


	/**
	 *
	 * Function  labels
	 * @param TblCall $model
	 * @param array $aColumn
	 * @param bool $bSubject
	 * @return  array
	 *
	 *        $label = $this->zcore->zActiveCall->labels($model, ZTblCallSelect::Api, true);
	 * $label2 = $this->zcore->zActiveCall->labels($model, ZTblCallSelect::Audio, true);
	 * $labelNoSub = $this->zcore->zActiveCall->labels($model, ZTblCallSelect::Api, false);
	 * $labelNoSub2 = $this->zcore->zActiveCall->labels($model, ZTblCallSelect::Audio, false);
	 *
	 */

	public function labels(TblCall $model, array $aColumn, bool $bSubject = true): array
	{

		$aLabel = $model->labels;
		$aReturn = [];

		foreach ($aLabel as $sKey => $sValue) {

			if (ArrayHelper::isIn($sKey, $aColumn))
				$aReturn[$sKey] = $sValue;

		}


		if ($model->CompanyID) {


			$aField = $this->zcore->zActive->fieldsByCompanyID($model->CompanyID);

			foreach ($aField as $field) {
				$aReturn[$field->Name] = $field->Title;
			}


			if ($bSubject) {

				$aSubject = $this->zcore->zActive->subjectsByCompanyID($model->CompanyID);

				foreach ($aSubject as $subject) {
					$aReturn[$subject->Name] = "{$subject->Title} (Тематика)";
				}

			}

		}

		return $aReturn;
	}


	/**
	 *
	 * Function  values
	 * @param TblCall $model
	 * @param array $aColumn
	 * @param bool $bAllowEmpty
	 * @return  array
	 *
	 * $value = $this->zcore->zActiveCall->values($model, ZTblCallSelect::Api, true);
	 * $value2 = $this->zcore->zActiveCall->values($model, ZTblCallSelect::Audio, true);
	 * $valueNoEmp = $this->zcore->zActiveCall->values($model, ZTblCallSelect::Api, false);
	 * $valueNoEmp2 = $this->zcore->zActiveCall->values($model, ZTblCallSelect::Audio, false);
	 *
	 */

	public function values(TblCall $model, array $aColumn, bool $bAllowEmpty = false): array
	{
		Az::start(__METHOD__);

		$aLabel = $this->labels($model, $aColumn, false);
		$aModel = ArrayHelper::toArray($model);

		$aReturn = [];

		foreach ($aLabel as $sKey => $sValue) {
			$sModelValue = $aModel[$sKey];

			if ($sModelValue === null)
				if (!$bAllowEmpty)
					continue;
				else {
					$aReturn[$sKey] = null;
					continue;
				}

			switch (true) {
				case        $this->zcore->zUtils->checkName($sKey, 'Enum_'):
				case        $this->zcore->zUtils->checkName($sKey, 'WHO'):
					$aReturn[$sKey] = $this->_aEnum[$sModelValue]->Name;
					break;

				default:
					$aReturn[$sKey] = $sModelValue;
			}
		}

		Az::count($aReturn, __FUNCTION__);

		return $aReturn;
	}


	public function detailView(TblCall $model, array $aColumn = [], bool $bAllowEmpty = false): array
	{

		Az::start(__METHOD__);

		$aLabel = $this->zcore->zActiveCall->labels($model, $aColumn, false);

		$aValue = $this->zcore->zActiveCall->values($model, $aColumn, $bAllowEmpty);

		$aReturn = [];

		foreach ($aValue as $sKey => $sValue) {
			$aReturn[] = [
				'attribute' => $sKey,
				'label' => $aLabel[$sKey],
				'value' => $aValue[$sKey],
			];
		}

		Az::count($aReturn, __FUNCTION__);

		return $aReturn;

	}

	public function labelValue(TblCall $model, array $aColumn = [], bool $bAllowEmpty = false): array
	{

		Az::start(__METHOD__);

		$aLabel = $this->zcore->zActiveCall->labels($model, $aColumn, false);

		$aValue = $this->zcore->zActiveCall->values($model, $aColumn, $bAllowEmpty);

		$aReturn = [];

		foreach ($aValue as $sKey => $sValue) {
			$aReturn[$aLabel[$sKey]] = $aValue[$sKey];
		}

		Az::count($aReturn, __FUNCTION__);

		return $aReturn;

	}


}
