<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace asrorz\frame;


use asrorz\actives\Enum;
use asrorz\actives\Field;
use asrorz\actives\Message;
use asrorz\actives\Subject;
use asrorz\actives\TblCall;
use asrorz\actives\User;
use asrorz\cores\Az;
use asrorz\enum\Field\Role;
use asrorz\enum\Field\Type;
use asrorz\enum\Message\SendState;
use asrorz\select\ZTblCallSelect;
use asrorz\zitems\ZMailerItem;
use Fusonic\Linq\Linq;
use Underscore\Types\Arrays;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

class ZMailer extends ZAudio
{


	/**
	 *
	 * Charsets
	 */

	public const Charset_UTF8 = 'UTF-8';


	/**
	 *
	 *   Views
	 */

	public const View_Bank = 'mailer/Bank';


	/**
	 *
	 * Adresses
	 */

	public const Adress_AsrorZ_Gmail = ['zcore.zk@gmail.com'];
	public const Adress_AsrorZ_Yandex = ['zcore.z@nasvyazi.uz'];
	public const Adress_Ns_Info_Yandex = ['info@nasvyazi.uz' => 'Nasvyazi Feedback'];
	public const Adress_Ns_Archive_Yandex = ['archive@nasvyazi.uz'];


	/**
	 *
	 *
	 * Configurations
	 */

	public $bTesting = false;

	public $bSendFile = false;
	public $bLogger = false;
	public $bFileTransport = false;


	/**
	 *
	 * Variables
	 */

	public $aFile;
	public $aData;
	public $sCharset = self::Charset_UTF8;
	public $sView = self::View_Bank;


	/**
	 *
	 * Core Fields For Mail
	 */

	public $aBcc;
	public $aReplyTo = self::Adress_Ns_Info_Yandex;


	/**
	 *
	 * Current Items
	 */


	/* @var Message[] $_aMessage */
	private $_aMessage;

	private $_sMiddFix;
	private $_iCount;


	/**
	 *
	 * Mailer Arrays
	 */

	private $_aSubject;
	private $_aField;
	private $_aUser;

	/*  @var Enum[] $_aEnum */
	private $_aEnum;


	public function zinit()
	{
		$this->_aCompany = $this->zcore->zActive->companies;
		$this->_aField = $this->zcore->zActive->fields;
		$this->_aSubject = $this->zcore->zActive->subjects;
		$this->_aEnum = $this->zcore->zActive->enums;
		$this->_aUser = $this->zcore->zActive->users;

	}

	private function _subject()
	{

		/** @var Subject[] $subjects */

		$subjects = Linq::from($this->_aSubject)
			->where(function (Subject $subject) {
				if ($subject->IsMailer === 1 && $subject->CompanyID === (int)$this->_model->CompanyID)
					return true;

				return false;
			})
			->toArray();


		/**
		 *
		 * Process Subjects
		 */


		$this->_iCount = 0;

		foreach ($subjects as $subject) {
			if ((int)$this->_model->attributes[$subject->Name] === 1) {

				$this->_iCount++;
				$sSubject = "Выбрана тематика звонка {$subject->Title}";
				$this->_sMiddFix = "SubjectID={$subject->Id}";

				$this->_make($sSubject, 'SubjectID', $subject->Id);

			}
		}

		if ($this->_iCount === 0)
			Az::trace("There Are No Subjects | {$this->_sSuffix}");

	}

	private function _field()
	{


		/** @var Field[] $fields */

		$fields = Linq::from($this->_aField)
			->where(function (Field $field) {
				if ($field->IsMailer === 1 && $field->CompanyID === (int)$this->_model->CompanyID)
					return true;

				return false;
			})
			->toArray();


		/**
		 *
		 * Process Data
		 */

		$this->_iCount = 0;

		foreach ($fields as $field) {
			if (!empty($this->_model->attributes[$field->Name])) {

				$this->_iCount++;

				$sSubject = "Заполнено поле {$field->Title}";
				$this->_sMiddFix = "FieldID={$field->Id}";

				$this->_make($sSubject, 'FieldID', $field->Id);

			}
		}

		if ($this->_iCount === 0)
			Az::trace("There Are No Fields | {$this->_sSuffix}");
	}


	private function _enum()
	{

		/** @var Field[] $fields */

		$fields = Linq::from($this->_aField)
			->where(function (Field $field) {
				if ($field->CompanyID === (int)$this->_model->CompanyID)
					return true;

				return false;
			})
			->toArray();


		/**
		 *
		 * Process Data
		 */

		$this->_iCount = 0;

		foreach ($fields as $field) {


			/** @var Enum[] $enums */
			$enums = Linq::from($this->_aEnum)
				->where(function (Enum $enum) use ($field) {
					if ($enum->FieldID === $field->Id && $enum->IsMailer === 1)
						return true;

					return false;
				})
				->toArray();


			/**
			 *
			 * Process Data
			 */

			foreach ($enums as $enum) {

				$sFieldName = $field->Name;
				$sModelValue = (int)$this->_model->attributes[$sFieldName];

				$isNotEmpty = !empty($sModelValue);
				$isEqual = $sModelValue === $enum->Id;

				if ($isNotEmpty && $isEqual) {

					$this->_iCount++;
					$sSubject = "Выбран элемент {$enum->Name} из списка {$field->Title}";

					$this->_sMiddFix = "EnumID = {$enum->Id} | FieldID = {$field->Id} ";

					$this->_make($sSubject, 'EnumID', $enum->Id);

				}

			}

		}

		Az::trace("There Are No Enums | {$this->_sSuffix}");


	}


	public function _answer()
	{


		/** @var Field[] $fields */

		$fields = Linq::from($this->_aField)
			->where(function (Field $field) {
				if ($field->Role === Role::Role_NotAnswered && $field->Type === Type::Type_Text && $field->CompanyID === (int)$this->_model->CompanyID)
					return true;

				return false;
			})
			->toArray();


		if (empty($fields))
			return Az::trace("There Are No NotAnswered Field | {$this->_sSuffix}");

		/**
		 *
		 * Process Data
		 */

		$field = $fields[0];

		$sSubject = 'Сотрудник не отвечает';

		$this->_sMiddFix = "FieldID={$field->Id}";

		$aInPhone = $this->_inPhone($field);

		if (empty($aInPhone))
			return Az::trace('InPhone is Empty!');

		$aTo = $this->_inPhoneEmail($aInPhone);

		if (empty($aTo))
			return Az::trace('aTo Emails Are Empty!');

		$this->_make($sSubject, 'FieldID', $field->Id, $aTo);

	}

	public function clean()
	{
		$db = Az::$app->db;


		$db->createCommand()->delete('setting', ['like', 'Name', 'Mailer'])->execute();
		$db->createCommand()->truncateTable('message')->execute();

		Az::trace('All Tables Are Truncated!');

		if ($this->zcore->zUtils->cacheFlush())
			Az::trace('Data Success Flushed!');
	}


	public function cleanOne(int $iID)
	{
		$model = TblCall::findOne($iID);

		$model->IsMailed = null;
		$model->save();

		$iInt = Message::deleteAll([
			'TblCallID' => $iID
		]);

		Az::trace($iInt, 'STATUS');

	}

	public function _model(TblCall $model)
	{

		Az::start(__METHOD__);


		/**
		 *
		 * Init Data
		 */

		$this->_model = $model;

		$aREt = [];

		foreach ($model->attributes as $key => $value) {
			$aREt[$key] = (int)$value;
		}


		$this->_company = $this->_aCompany[$this->_model->CompanyID];


		$this->_sSuffix = "ID={$this->_model->Id} | {$this->_company->Name}/{$this->_company->Id} | StartDate={$this->_model->StartDate} ";


		if ($this->_model->IsMailed !== null)
			return Az::trace("{$this->_sPrefix} ALREADY PROCESSED | Sent Messages Count = {$this->_model->IsMailed} | {$this->_sSuffix}", null, null, 2);


		Az::trace("{$this->_sPrefix} PROCESSING | {$this->_sSuffix}", null, null, 2);


		/**
		 *
		 *
		 * Core Variables
		 */

		$this->aFile = [
			$this->_model->RecordFile,
		];

		$this->aData = $this->_data($this->_model);


		/**
		 *
		 *
		 * Start Items Processing
		 */

		$this->_subject();
		$this->_field();
		$this->_enum();
		$this->_answer();

		$iCount = \count($this->_aMessage);
		$this->_sendAll();

		$this->_model->IsMailed = $iCount;

		if ($this->_model->save())
			Az::trace("{$this->_sPrefix} TblCall Success Saved | iCount = {$iCount} | {$this->_sSuffix}", null, Cat_ModelSave, 2);


		return true;

	}


	public function _data(TblCall $model)
	{
		return $this->zcore->zActiveCall->labelValue($model, ZTblCallSelect::Mailer);
	}


	private function _make(string $sSubject, string $sKey, string $sValue, array $aTo = null)
	{

		Az::start(__METHOD__);

		$message = Message::find()
			->where([
				'CompanyID' => $this->_company->Id,
				'TblCallID' => $this->_model->Id,
				'SendState' => 1,
				$sKey => $sValue,
			])
			->limit(1)
			->one();

		if ($message !== null) {
			return Az::trace("ALREADY SENT | {$this->_sMiddFix}");
		}

		Az::trace("NEW MESSAGE | {$this->_sMiddFix}");


		/**
		 *
		 * Prepare Message
		 */

		$message = new Message();


		if ($this->bTesting)
		{
			$message->To = $this->_set(self::Adress_AsrorZ_Gmail);
		}
		else {

			$aZTo = [];
			if (!empty($this->_company->Email))
				$aZTo[] = $this->_company->Email;

			if (!empty($this->_company->ReserveEmail))
				$aZTo[] = $this->_company->ReserveEmail;

			if (!empty($aTo))
				$aZTo = ArrayHelper::merge($aZTo, $aTo);

			$message->To = $this->_set($aZTo);
			$message->Cc = $this->_set(self::Adress_Ns_Archive_Yandex);
		}


		$message->Subject = $sSubject;

		$message->TblCallID = $this->_model->Id;
		$message->CompanyID = $this->_company->Id;

		$message->setAttribute($sKey, $sValue);


		/**
		 *
		 * Fill Main Array
		 */

		$this->_aMessage[] = $message;


		return true;
	}


	public function all(string $sDate = null): void
	{
		$this->_sDate = $sDate;

		$this->_date('Mailer');
		$aCalls = $this->zcore->zActiveCall->dataMailer($this->_sStartDate);

		$this->_calls($aCalls);
	}
	public function one(int $iId)
	{
		$this->cleanOne($iId);
		parent::one($iId);
	}

	private function _sendAll()
	{
		Az::start(__METHOD__);

		if (empty($this->_aMessage))
			return Az::trace($this->_model->Id, 'There is Nothing To Send! Call ID');

		foreach ($this->_aMessage as $message) {
			$this->_send($message);
		}

		$this->_aMessage = null;
		return true;
	}

	private function _send(Message $message )
	{

		Az::start(__METHOD__);

		$sSendData = "To: {$message->To} | Subject: {$message->Subject}";

		if (empty($message->To))
			return Az::warning("EMAIL RECIPIENTS ARE ABSENT! | {$sSendData} | {$this->_sSuffix}");

		$service = Az::$app->mailer;

		if ($this->bLogger) {
			$logger = new \Swift_Plugins_Loggers_ArrayLogger();
			$service->transport->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));
		}

		if ($this->bFileTransport)
			$service->useFileTransport = true;


		/**
		 *
		 * Create Item
		 */

		$item = new ZMailerItem();

		$item->sSubject = $message->Subject;
		$item->aData = $this->aData;


		/**
		 *
		 *
		 * Process Message
		 */

		$compose = $service->compose($this->sView, ['item' => $item]);

		$compose
			->setCharset($this->sCharset)
			->setReplyTo($this->aReplyTo)
			->setBcc($this->_get($this->aBcc));


		if ($this->bSendFile)
			if (!empty($this->aFile))
				foreach ($this->aFile as $sFile)
					if (file_exists($sFile))
						$compose->attach($sFile);
		$compose
			->setTo($this->_get($message->To))
			->setCc($this->_get($message->Cc))
			->setSubject(       $message->Subject );

		try {
			$service->send($compose);
			$message->SendState = SendState::Success;
			$message->save();

			return Az::trace("SUCCESS MAIL SEND | ID = {$message->Id} | {$sSendData}");

		} catch (\Exception $exception) {
			$message->Problems = StringHelper::truncate($exception->getMessage(), 990);
			$message->SendState = SendState::Problem;
			$message->save();

			return Az::error("MAIL SEND ERROR | ID = {$message->Id} | Exception = {$exception->getMessage()} | {$sSendData}");
		}
	}


	public function test()
	{

		$this->bSendFile = true;

		$message = new Message();

		$message->To = $this->_set(self::Adress_AsrorZ_Gmail);
		$message->Cc = $this->_set(self::Adress_AsrorZ_Yandex);

		$message->Subject = 'Test Mail';

		$this->aFile = [
			'C:\Books.doc'
		];

		$this->aData = [
			'adad' => 1313131,
			'adad1313' => 1313131,
		];

		$this->_send($message);
	}

	public function _inPhone(Field $field)
	{

		$sInPhones = $this->_model->attributes[$field->Name];

		if (empty($sInPhones))
			return null;

		$sInPhones = trim($sInPhones);
		$sInPhones = str_replace('-', '', $sInPhones);
		$sInPhones = str_replace(array(', ', ',', ' ,'), ' ', $sInPhones);

		$aInPhones = explode(' ', $sInPhones);
		$aInPhones = Arrays::clean($aInPhones);

		return $aInPhones;
	}

	private function _inPhoneEmail(array $aInPhones)
	{
		$aEmail = [];

		Linq::from($this->_aUser)
			->each(function (User $user) use (&$aInPhones, &$aEmail) {
				if (ArrayHelper::isIn($user->InPhone, $aInPhones)) {
					if (!empty($user->Email))
						$aEmail[] = $user->Email;

					if (!empty($user->ReserveEmail))
						$aEmail[] = $user->ReserveEmail;
				}
			});


		return $aEmail;
	}


	private function _set($aData)
	{
		if (!empty($aData))
			return implode('|', $aData);

		return null;
	}

	private function _get($data)
	{
		if (!empty($data))
			return explode('|', $data);

		return null;
	}


}
