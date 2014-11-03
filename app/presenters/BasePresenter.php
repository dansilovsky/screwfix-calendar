<?php
namespace Screwfix;
/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter {
	
	/**
	 * @var \Nette\Security\User
	 */
	protected $user;
	
	/**
	 * @var \Nette\Security\Identity
	 */
	protected $identity;
	
	/**
	 * Acl
	 * @var \Nette\Security\Permission 
	 */
	protected $acl;
	
	/** @var \Nette\Http\Request */
	protected $request;
	
	protected $response;
	
	/**
	 * @var Cookies 
	 */
	protected $cookies;
	
	/**
	 * @var UserFacade 
	 */
	protected $userFacade;
	
	/** @var \Screwfix\UserFacadeFactory @inject */
	public $userFacadeFactory;
	
	/**
	 * @var PatternFacade 
	 */
	protected $patternFacade;

	/**
	 * @var SysPatternFacade 
	 */
	protected $sysPatternFacade;
	
	/**
	 * @var NoteFacade 
	 */
	protected $noteFacade;
	/**
	 * @var SysNoteFacade 
	 */
	protected $sysNoteFacade;
	
	/**
	 * @var HolidayFacade
	 */
	protected $holidayFacade;
	
	/** @var \Screwfix\HolidayFacadeFactory @inject **/
	public $holidayFacadeFactory;
	
	/** @var \Screwfix\PatternFacadeFactory @inject **/
	public $patternFacadeFactory;
	
	/**
	 * @var BankHolidayFacade
	 */
	protected $bankHolidayFacade;
	
	/** @var \Screwfix\BankHolidayFacadeFactory @inject */
	public $bankHolidayFacadeFactory;
	
	/** @var \Screwfix\Settings @inject **/
	public $settings;
	
	/** @var \Screwfix\DateTimeFactory @inject **/
	public $dateTimeFactory;
	
	/** @var \Screwfix\CalendarDateTimeFactory @inject **/
	public $calendarDateFactory;
	
	/** @var \Screwfix\ShiftPatternDateFactory @inject **/
	public $shiftPatternDateFactory;
	
	/** @var \Screwfix\AroundIteratorFactory @inject **/
	public $aroundIteratorFactory;
	
	/** @var \Screwfix\CalendarDayPeriodFactory @inject **/
	public $calendarDayPeriodFactory;
	
	/** @var \Screwfix\CalendarDataFactory @inject **/
	public $calendarDataFactory;
	
	/** @var \Screwfix\CalendarIntervalFactory @inject **/
	public $calendarIntervalFactory;
	
	/** @var \Screwfix\HolidayCredits @inject */
	public $holidayCredits;
	
	/**
	 * Date in format mm-dd from when holiday year starts.
	 * eg. '04-01'
	 * @var array 
	 */
	private $_holidayYearStart;
	
	/** 
	 * Users holiday credits + bank holidays on which is user actually off work
	 * @var integer 
	 */
	private $_holidayTotalCredits;

	protected function startup()
        {		
                parent::startup();
		
		// cookies setup
		$this->cookies = $this->context->cookies;
		$this->registerCookiesValidators();
		
		$this->request = $this->context->httpRequest;
		$this->response = $this->context->httpResponse;
		
		
		
		// use absolute urls
		$this->absoluteUrls = true;
		
		$this->user = $this->getUser();
		$this->identity = $this->user->getIdentity();
		$this->acl = $this->context->authorizator;
		
		// facades
		$this->userFacade = $this->context->userFacade;
		$this->patternFacade = $this->context->patternFacade;
		$this->sysPatternFacade = $this->context->sysPatternFacade;
		$this->noteFacade = $this->context->noteFacade;
		$this->sysNoteFacade = $this->context->sysNoteFacade;
		$this->holidayFacade = $this->context->holidayFacade;
		$this->bankHolidayFacade = $this->context->bankHolidayFacade;
		
		// check if acces is allowed for user
		if (!$this->isAllowed())
		{
			throw new \Screwfix\UnauthorizedAcces();
		}
		
		$this->setHolidayTotalCredits();
		$this->setHolidayYearStart();		
        }
	
	public function beforeRender()
	{
		$this->template->identity = $this->user->getIdentity();
		$this->template->acl = $this->acl;		
		$this->template->userUrl = $this->user->isLoggedIn() ? $this->link(':Front:User:', $this->identity->username) : null;		
		$this->template->isStickyFooter = $this->isStickyFooter();
	}
	
	public function setHolidayYearStart()
	{		
		$this->_holidayYearStart = $this->settings->get('holiday.yearStart');
	}
	
	public function setHolidayTotalCredits()
	{
		$this->_holidayTotalCredits = $this->holidayCredits->getUserCredits($this->user, $this->userFacadeFactory, $this->bankHolidayFacadeFactory);
	}
	
	/**
	 * Gets holiday year boundaries for given year.
	 * 
	 * @param string $year date of format yyyy
	 * @return array eg. array('from' = '2013-04-01', 'to' => '2014-03-31')
	 */
	public function getHolidayYearBoundaries($year)
	{
		$boudaries = array();
		
		$from = $year . '-' . $this->_holidayYearStart;
		
		$dateTime = $this->dateTimeFactory->create($from)->addYear()->subDay();
		
		return array('from' => $from, 'to' => $dateTime->toString());	
	}
	
	public function getHolidayTotalCredits()
	{
		return $this->_holidayTotalCredits;
	}
	/**
	 * Creates acl resourse to be used by $this->isAllowed() (eg. Front:User)
	 * 
	 * @return string
	 */
	protected function getResource()
	{
		return $this->name;
	}
	
	/**
	 * Determines holiday year of given date
	 * 
	 * @param string $date date in format yyyy-mm-dd
	 * @return string date in format yyyy
	 */
	public function determineHolidayYear($date)
	{
		$year = substr($date, 0, 4);
		$monthDay = substr($date, 5);	
		
		if ($monthDay < $this->_holidayYearStart)
		{
			$year--;
		}
		
		return $year;
	}

	/**
	 * Is user allowed to acces this presenter and action.
	 * 
	 * @throws Nette\InvalidStateException
	 * @return bool
	 */
	protected function isAllowed()
	{
		$role = $this->user->isLoggedIn() ? $this->user->getIdentity()->role : $this->user->guestRole;
		$resource = $this->getResource();
		return $this->acl->isAllowed($role, $resource);
	}
	
	public function handleSignOut()
	{
	    $this->getUser()->logout();
	    $this->redirect(':Front:Home:');
	}
	
	public function isStickyFooter()
	{
		return ($this->name !== "Front:Home");
	}
	
	/**
	 * Login an user.
	 * To be used only for unit testing purposes.
	 * 
	 * @param string $id
	 * @param string $password
	 */
	public function login($id, $password)
	{
		$this->getUser()->login($id, $password);
	}
	
	/**
	 * It is automatically launched in BasePresenter::startup. 
	 * If you need to register any cookies validators implement it in a children methods.
	 */
	protected function registerCookiesValidators(){
		
	}

}
