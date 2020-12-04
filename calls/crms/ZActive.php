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


use asrorz\actives\Category;
use asrorz\actives\Company;
use asrorz\actives\Departer;
use asrorz\actives\Enum;
use asrorz\actives\Field;
use asrorz\actives\Platform;
use asrorz\actives\Result;
use asrorz\actives\Subject;
use asrorz\actives\TblCall;
use asrorz\actives\User;
use asrorz\cores\Az;
use asrorz\cores\ZArrayHelper;
use asrorz\cores\ZFrame;
use asrorz\cores\ZStringHelper;
use asrorz\enum\User\Role;
use asrorz\select\ZTblCallSelect;
use Fusonic\Linq\Linq;

/**
 *
 * @property Category[] $categories
 * @property Platform[] $platforms
 * @property Company[] $companies
 * @property Subject[] $subjects
 * @property Field[] $fields
 * @property Enum[] $enums
 * @property Result[] $results
 * @property User[] $users
 * @property Departer[] $departers
 *
 */
class ZActive extends ZFrame
{


	/**
	 *
	 * Indexing
	 */

	public const Index_Id = 'Id';
	public const Index_Guids = 'Guids';


	/**
	 *
	 * Order By
	 */

	public const Order_Id = ['Id' => SORT_ASC];
	public const Order_Id_Desc = ['Id' => SORT_DESC];

	public const Order_StartDate = ['StartDate' => SORT_ASC];
	public const Order_StartDate_Desc = ['StartDate' => SORT_DESC];


	/**
	 *
	 * Variables
	 */

	public $bIndex = true;
	public $sIndex = self::Index_Id;
	public $aOrder = self::Order_Id;


	/**
	 *
	 * Private Data
	 */

	private $_categories;
	private $_platforms;
	private $_companies;
	private $_subjects;
	private $_fields;
	private $_enums;
	private $_results;
	private $_users;
	private $_agents;
	private $_departers;


	public function getCategories()
	{
		if ($this->_categories === null) {

			$Q = Category::find()
				->orderBy('SortIndex');

			if ($this->bIndex)
				$Q->indexBy($this->sIndex);

			$aReturn = $Q->all();

			Az::count($aReturn, __FUNCTION__);
			$this->_categories = $aReturn;
		}

		return $this->_categories;
	}


	public function getPlatforms()
	{
		if ($this->_platforms === null) {

			$Q = Platform::find()
				->orderBy($this->aOrder);

			if ($this->bIndex)
				$Q->indexBy($this->sIndex);

			$aReturn = $Q->all();

			Az::count($aReturn, __FUNCTION__);
			$this->_platforms = $aReturn;
		}

		return $this->_platforms;
	}


	public function getCompanies()
	{
		if ($this->_companies === null) {

			$Q = Company::find()
				->orderBy($this->aOrder);

			if ($this->bIndex)
				$Q->indexBy($this->sIndex);

			$aReturn = $Q->all();

			Az::count($aReturn, __FUNCTION__);
			$this->_companies = $aReturn;
		}

		return $this->_companies;
	}


	public function getSubjects()
	{
		if ($this->_subjects === null) {

			$Q = Subject::find()
				->orderBy($this->aOrder);

			if ($this->bIndex)
				$Q->indexBy($this->sIndex);

			$aReturn = $Q->all();

			Az::count($aReturn, __FUNCTION__);
			$this->_subjects = $aReturn;
		}

		return $this->_subjects;
	}


	public function getFields()
	{
		if ($this->_fields === null) {

			$Q = Field::find()
				->orderBy($this->aOrder);

			if ($this->bIndex)
				$Q->indexBy($this->sIndex);

			$aReturn = $Q->all();

			Az::count($aReturn, __FUNCTION__);
			$this->_fields = $aReturn;
		}

		return $this->_fields;
	}


	public function getEnums()
	{
		if ($this->_enums === null) {

			$Q = Enum::find()
				->orderBy($this->aOrder);

			if ($this->bIndex)
				$Q->indexBy($this->sIndex);

			$aReturn = $Q->all();

			Az::count($aReturn, __FUNCTION__);
			$this->_enums = $aReturn;
		}

		return $this->_enums;
	}

	public function getResults()
	{
		if ($this->_results === null) {

			$Q = Result::find()
				->orderBy($this->aOrder);

			if ($this->bIndex)
				$Q->indexBy($this->sIndex);

			$aReturn = $Q->all();

			Az::count($aReturn, __FUNCTION__);
			$this->_results = $aReturn;
		}

		return $this->_results;
	}


	public function getUsers()
	{
		if ($this->_users === null) {

			$Q = User::find()
				->orderBy($this->aOrder);

			if ($this->bIndex)
				$Q->indexBy($this->sIndex);

			$aReturn = $Q->all();

			Az::count($aReturn, __FUNCTION__);
			$this->_users = $aReturn;
		}

		return $this->_users;
	}


	public function getDeparters()
	{
		if ($this->_departers === null) {

			$Q = Departer::find()
				->orderBy($this->aOrder);

			if ($this->bIndex)
				$Q->indexBy($this->sIndex);

			$aReturn = $Q->all();

			Az::count($aReturn, __FUNCTION__);
			$this->_departers = $aReturn;
		}

		return $this->_departers;
	}


	/**
	 *
	 * @return  Company[]
	 */
	public function companiesIsActive(): array
	{

		$Q = Linq::from($this->companies)
			->where(function (Company $company) {
				if ($company->IsActive === 1)
					return true;
				return false;
			});


		$callable = null;

		if ($this->bIndex)
			$callable = function (Company $company) {
				return $company->Id;
			};

		$aReturn = $Q->toArray($callable);

		Az::count($aReturn, __FUNCTION__);
		return $aReturn;
	}


	/**
	 *
	 * @param int|null $iCompanyID
	 * @return User[]
	 */
	public function agents()
	{

		$Q = Linq::from($this->users)
			->where(function (User $user) {
				if ($user->CompanyID === 1 && $user->Role === Role::Role_Operator)
					return true;

				return false;
			});


		$callable = null;

		if ($this->bIndex)
			$callable = function (User $user) {
				return $user->attributes[$this->sIndex];
			};

		$aReturn = $Q->toArray($callable);

		Az::count($aReturn, __FUNCTION__);
		return $aReturn;


	}

	/**
	 *
	 * @param int|null $iCompanyID
	 * @return Subject[]
	 */
	public function subjectsByCompanyID(int $iCompanyID = null): array
	{

		Az::trace($iCompanyID, 'Subject | SET CompanyID');

		$Q = Linq::from($this->subjects)
			->where(function (Subject $subject) use ($iCompanyID) {
				if ($subject->CompanyID === $iCompanyID)
					return true;

				return false;
			});


		$callable = null;

		if ($this->bIndex)
			$callable = function (Subject $subject) {
				return $subject->attributes[$this->sIndex];
			};

		$aReturn = $Q->toArray($callable);

		Az::count($aReturn, __FUNCTION__);
		return $aReturn;
	}


	/**
	 *
	 * @param int|null $iCompanyID
	 * @return Field[]
	 */
	public function fieldsByCompanyID(int $iCompanyID = null): array
	{

		Az::trace($iCompanyID, 'Fields | SET CompanyID');

		$Q = Linq::from($this->fields)
			->where(function (Field $field) use ($iCompanyID) {
				if ($field->CompanyID === $iCompanyID)
					return true;

				return false;
			});

		$callable = null;

		if ($this->bIndex)
			$callable = function (Field $field) {
				return $field->attributes[$this->sIndex];
			};

		$aReturn = $Q->toArray($callable);

		Az::count($aReturn, __FUNCTION__);
		return $aReturn;
	}


	/**
	 *
	 * @param int|null $iCompanyID
	 * @return User[]
	 */
	public function usersByCompanyID(int $iCompanyID = null): array
	{

		Az::trace($iCompanyID, 'Users | SET CompanyID');

		$Q = Linq::from($this->users)
			->where(function (User $user) use ($iCompanyID) {
				if ($user->CompanyID === $iCompanyID)
					return true;

				return false;
			});


		$callable = null;

		if ($this->bIndex)
			$callable = function (User $field) {
				return $field->attributes[$this->sIndex];
			};

		$aReturn = $Q->toArray($callable);

		Az::count($aReturn, __FUNCTION__);
		return $aReturn;
	}


	/**
	 *
	 * @param int|null $iCompanyID
	 * @return Departer[]
	 */
	public function departersByCompanyID(int $iCompanyID = null): array
	{

		Az::trace($iCompanyID, 'Users | SET CompanyID');

		$Q = Linq::from($this->departers)
			->where(function (Departer $departer) use ($iCompanyID) {
				if ($departer->CompanyID === $iCompanyID)
					return true;

				return false;
			});


		$callable = null;

		if ($this->bIndex)
			$callable = function (Departer $departer) {
				return $departer->attributes[$this->sIndex];
			};

		$aReturn = $Q->toArray($callable);

		Az::count($aReturn, __FUNCTION__);
		return $aReturn;
	}


	/**
	 *
	 * @param int|null $iFieldID
	 * @return Enum[]
	 */
	public function enumsByFieldID(int $iFieldID = null): array
	{

		Az::trace($iFieldID, 'Enums | SET FieldID');

		$Q = Linq::from($this->enums)
			->where(function (Enum $enum) use ($iFieldID) {
				if ($enum->FieldID === $iFieldID)
					return true;

				return false;
			});


		$callable = null;

		if ($this->bIndex)
			$callable = function (Enum $enum) {
				return $enum->attributes[$this->sIndex];
			};

		$aReturn = $Q->toArray($callable);

		Az::count($aReturn, __FUNCTION__);
		return $aReturn;
	}


	public function arrayType(array $aAttr, array $aInt)
	{

		/** @var TblCall $class */

		$aReturn = [];

		foreach ($aAttr as $sKey => $sValue) {

			if (ZArrayHelper::isIn($sKey, $aInt))
				if ($sValue !== null)
					$aReturn[$sKey] = (int)$sValue;
				else
					$aReturn[$sKey] = $sValue;
			else
				$aReturn[$sKey] = $sValue;
		}

		return $aReturn;
	}


	public function toModel(string $class, array $aAttr)
	{

		/** @var TblCall $class */
		/** @var TblCall $model */

		/** @var ZTblCallSelect $sClassSelect */

		$sClassBasename = ZStringHelper::basename($class);
		$sClassSelect = "asrorz\\select\\Z{$sClassBasename}Select";

		$aInt = $sClassSelect::Int();

		$aAttr = $this->arrayType($aAttr, $aInt);

		$model = new $class();
		$model->isNewRecord = false;
		$model->setOldAttributes($aAttr);
		$model->setAttributes($aAttr);
		return $model;
	}

}
