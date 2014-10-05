<?php

namespace ApiModule;

/**
 * DaysPresenter
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class DaysPresenter extends BasePresenter {

	public function actionDefault() 
	{		
		if ($this->user->isLoggedIn())
		{
			$shiftPatternFilter = $this->getShiftPatternFilter();
			$bankHolidayFilter = $this->getBankHolidayFilter();
			$holidayFilter = $this->getHolidayFilter();
			$noteFilter = $this->getNoteFilter();
			$sysNoteFilter = $this->getSysNoteFilter();

			$calendarPeriod = $this->calendarDayPeriodFactory->create($this->from, $this->to);

			$this->calendarData = $this->calendarDataFactory->create($calendarPeriod);
			$this->calendarData->addFilter($shiftPatternFilter)
				->addFilter($bankHolidayFilter)
				->addFilter($holidayFilter)
				->addFilter($noteFilter)
				->addFilter($sysNoteFilter)
				->build();
			$this->calendarData->rewind();
		}
		else
		{
			$sysShiftPatternFilter = $this->getSysShiftPatternFilter();
			$bankHolidayFilter = $this->getBankHolidayFilter();
			$sysNoteFilter = $this->getSysNoteFilter();

			$calendarPeriod = $this->calendarDayPeriodFactory->create($this->from, $this->to);

			$this->calendarData = $this->calendarDataFactory->create($calendarPeriod);
			$this->calendarData->addFilter($sysShiftPatternFilter)
				->addFilter($bankHolidayFilter)
				->addFilter($sysNoteFilter)
				->build();
			$this->calendarData->rewind();
		}
		
		$responseArr = $this->calendarDataToResponseArray();	
		
		$this->sendResponse(new \Nette\Application\Responses\JsonResponse($responseArr));
	}
	
	/**
	 * New days in calendar can not be actually created therefore this function will never be used.
	 */
	public function actionCreate()
	{
		
	}
	
	/**
	 * Updates a day
	 * 
	 * @param string $id date in format yyyy-mm-dd
	 */
	public function actionUpdate($id)
	{	
		$user_id = $this->user->getId();
		$day_id = $id;
		
		$response = null;
//		sleep(60);
//		$this->response->setCode(\Nette\Http\Response::S400_BAD_REQUEST);
		
		$data = $this->getJson();		
		
		$failed = false;
		$errorMessage = '';
		
		if ($data === null)
		{
			$failed = true;
			$errorMessage = 'No json data received.';
		}		
		else if ($day_id !== null)
		{
			// update notes or sys notes
			if (array_key_exists('note', $data))
			{
				$this->noteFacade->updateNotes($user_id, $day_id, $data['note']);
				
				$response = $this->noteFacade->getAjaxNote($user_id, $day_id);
			}
			else if (array_key_exists('sysNote', $data))
			{
				$this->sysNoteFacade->updateNotes($day_id, $data['sysNote']);
				
				$response = $this->sysNoteFacade->getAjaxNote($day_id);
			}
			else
			{
				$this->response->setCode(\Nette\Http\Response::S400_BAD_REQUEST);
				$failed = true;
				$errorMessage = 'Invalid request.';
			}
		}
		else
		{
			// update holidays

			// validation
			$count = count($data);
			$countNewDebits = 0;
			
			foreach($data as $holiday)
			{
				if (
					!preg_match('/^\d{4}-\d{2}-\d{2}$/', $holiday['id']) ||
					!($holiday['holiday'] == null || $holiday['holiday'] == 0 || $holiday['holiday'] == 1)					
				) 
				{
					$failed = true;
					$errorMessage = 'Invalid data.';
					break;
				}
				
				if ($holiday['holiday'] === 0)
				{
					$countNewDebits++;
				}
				else if ($holiday['holiday'] === 1)
				{
					$countNewDebits += 0.5;
				}					
			}			
			
			if (!$failed)
			{
				// make sure all holidays are in the same holiday year
				$holidayYear = $this->determineHolidayYear($data[0]['id']);
				$holidayYearBoudaries = $this->getHolidayYearBoundaries($holidayYear);
				
				foreach ($data as $holiday)
				{
					if (!($holiday['id'] >= $holidayYearBoudaries['from'] && $holiday['id'] <= $holidayYearBoudaries['to']))
					{
						$failed = true;
						$errorMessage = 'Attempt to save holidays from different holiday years.';
						break;
					}
				}
			}
			
			if (!$failed)
			{
				// make sure client does not try to use more holidays than allowed
				$debits = $this->holidayFacadeFactory->create()
					->getDebits($user_id, $holidayYearBoudaries['from'], $holidayYearBoudaries['to']);
				
				$totalCredits = $this->getHolidayTotalCredits();
				
				if ($totalCredits - $debits - $countNewDebits < 0)
				{					
					$failed = true;
					$errorMessage = 'Too many holidays.';
				}
			}
			
			if (!$failed)
			{
				try
				{
					$this->holidayFacade->updateHolidays($user_id, $data);
					
					$response = $data;
				} 
				catch (\PDOException $ex) 
				{					
					if ($ex->errorInfo[1] === 1062)
					{
						// all given holidays could not be added because of duplicate entry (a holiday already in db)
						$failed = true;
						$errorMessage = 'One of sent holidays is already in database.';
					}
					else 
					{
						throw $ex;
					}
				}
			}			
		}
			
		if ($failed)
		{
			$this->response->setCode(\Nette\Http\Response::S400_BAD_REQUEST);
			$response = array('error' => 'Failed. ' . $errorMessage);
		}
		
		$this->sendResponse(new \Nette\Application\Responses\JsonResponse($response));
	}
	
	/**
	 * Days in calendar can not be actually deleted therefore this function will never be used.
	 */
	public function actionDelete()
	{
		
	}
	
	protected function fromTo() 
	{
		$from = $this->request->getQuery('from');
		$this->from = $this->calendarDateFactory->create($from);
		
		$to = $this->request->getQuery('to');
		$this->to = $this->calendarDateFactory->create($to);
	}	
}
