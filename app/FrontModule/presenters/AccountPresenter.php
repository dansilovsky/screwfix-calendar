<?php
namespace FrontModule;

use Nette\Application\UI\Form,
	Screwfix\BaseaccountForm;

/**
 * AccountPresenter
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class AccountPresenter extends BaseaccountPresenter {
	
	protected function createComponentCredentialsForm() 
	{
		$form = new BaseaccountForm($this, 'credentialsForm');
		
		$form->addText('username', null, 30, 30)
			->setAttribute('placeholder', 'Username')
			->setDefaultValue($this->identity->username)
			->addRule(Form::MIN_LENGTH, 'Username must contain at least %d characters.', 3)
			->addRule(Form::MAX_LENGTH, 'Username is too long. Use maximum of %d characters.', 60)
			->addRule(Form::PATTERN, 'Username can contain only alphabetical characters or underscore.', '\w{3,60}');
		$form->addText('email', 'Email', 30, 30)
			->setAttribute('placeholder', 'Email')
			->setDefaultValue($this->identity->email)
			->setRequired('Enter an email please.')
			->addRule(Form::MAX_LENGTH, 'Email is too long. Use maximum of %d characters.', 255)
			->addRule(Form::EMAIL, 'Invalid email address.');
		$form->addProtection('Time limit has expired. Please send the form again.', 1800);
		$form->onSuccess[] = $this->credentialsFormSubmitted;
		
		$form->addSubmit('edit', 'Edit')
			->setAttribute('class', 'button');
		
		return $form;
	}
	
	public function credentialsFormSubmitted(Form $form)
	{
		$formValues = $form->getValues();
		
		$userArr = array();
		
		$identity = $this->identity;
		
		if ($formValues->username !== $identity->username)
		{
			$userArr['username'] = $formValues->username;
		}
		
		if ($formValues->email !== $identity->email)
		{
			$userArr['email'] = $formValues->email;
		}
		
		if ($userArr)
		{
			try
			{
				$this->userFacade->update($identity->id, $userArr);
				
				if (isset($userArr['username']))
				{
					$identity->username = $userArr['username'];
				}
				
				if (isset($userArr['email']))
				{
					$identity->email = $userArr['email'];
				}
			} 
			catch (Exception $ex) 
			{
				$form->addError('Sorry, something went wrong. Please try again.');
			}
		}
		
		if (isset($userArr['username']))
		{
			$identity->username = $userArr['username'];
		}
		
		if (isset($userArr['email'])) 
		{
			$identity->email = $userArr['email'];
		}		
	}
	
	protected function createComponentPasswordForm()
	{
		$form = new BaseaccountForm($this, 'passwordForm');
		
		$form->addPassword('password', null, 30)
			->setAttribute('placeholder', 'Current password')
			->setRequired('Enter the current password please.')
			->addRule(Form::MIN_LENGTH, 'Password must contain at least %d characters.', 6)	;
		$form->addPassword('newPassword', null, 30)
			->setAttribute('placeholder', 'New password')
			->setRequired('Enter a new password please.')
			->addRule(Form::NOT_EQUAL, 'New password must be different from the old one.', $form['password'])
			->addRule(Form::MIN_LENGTH, 'Password must contain at least %d characters.', 6);
		$form->addPassword('verifyPassword', null, 30)
			->setAttribute('placeholder', 'Retype new password')
			->setRequired('Reenter a new password please.')
			->addRule(Form::EQUAL, 'Passwords do not match.', $form['newPassword']);
		$form->addProtection('Time limit has expired. Please send the form again.', 1800);
		$form->onSuccess[] = $this->passwordFormSubmitted;
		
		$form->addSubmit('edit', 'Edit')
			->setAttribute('class', 'button');
		
		return $form;
	}
	
	public function passwordFormSubmitted(Form $form)
	{
		$formValues = $form->getValues();
		
		$hashedPassword = \Screwfix\Authenticator::calculateHash($formValues->password, $this->identity->password);
		
		if ($hashedPassword !== $this->identity->password)
		{			
			$form['password']->addError('Your password was incorrect.');
		}
		else
		{
			try
			{				
				$hashedNewPassword = \Screwfix\Authenticator::calculateHash($formValues->newPassword);

				$this->userFacade->update($this->identity->id, array('password' => $hashedNewPassword));
			} 
			catch (Exception $ex) 
			{
				$form->addError('Sorry, something went wrong. Please try again.');
			}
			
			$this->identity->password = $hashedNewPassword;
		}
	}
	
	protected function createComponentEmploymentLengthForm()
	{
		$form = new BaseaccountForm($this, 'employmentLengthForm');
		
		$employmentLengthSelection = $this->holidayCredits->getFormSelection();
		
		$employmentLengthVal = $this->getEmploymentLengthValue();
		
		$form->addSelect('employmentLength', 'How many years have you been employed?', $employmentLengthSelection)
			->setDefaultValue($employmentLengthVal)
			->setAttribute('data-border-years-number', $this->holidayCredits->getBorderYearsNumber());
		
		$employmentDateValue = $this->getEmploymentDateValue();
		
		$form['employmentDate'] = $this->employmentDateInputFactory->create();
		$form['employmentDate']->setDefaultValue($employmentDateValue);
		
		$form->addSubmit('edit', 'Edit')
			->setAttribute('class', 'button');
		
		$form->onSuccess[] = $this->employmentLengthFormSubmitted;
		
		return $form;
	}
	
	public function employmentLengthFormSubmitted(Form $form)
	{
		$formValues = $form->getValues();
		
		try
		{
			$data = ['credits' => $this->workOutFormEmployment($formValues)];

			$this->userFacade->update($this->user->getId(), $data);
			
		}
		catch (\Exception $ex)
		{
			$form->addError('Sorry, something went wrong. Please try again.');
		}
		
		$this->identity->credits = $data['credits'];
	}
	
	protected function createComponentSetupForm() 
	{
		$userPatternRow = $this->patternFacadeFactory->create()->getUserPattern($this->identity->id);
		
		$userSysPatternRow = $userPatternRow->ref('sys_pattern', 'sys_pattern_id');
		
		$form = new BaseaccountForm($this, 'setupForm');
		
		
		// Team select
		$sysPatternTeamSelection = $this->teamFacadeFactory->create()->getFormSelection();
		
		$defaultVal = $userSysPatternRow !== null ? $userSysPatternRow->team_id : 0;
		
		if ($defaultVal === 0)
		{
			$defaultValArr = [0 => 'Custom'];
			
			$sysPatternTeamSelection = $defaultValArr + $sysPatternTeamSelection;
		}
		
		$form->addPatternSelect('sysPatternTeamSelect', 'Select your team', $sysPatternTeamSelection)
			->setDefaultValue($defaultVal);
		
		
		// Shift select
		$sysPatternShiftSelection = $this->shiftFacadeFactory->create()->getFormSelection();
		
		$defaultVal = $userSysPatternRow !== null ? $userSysPatternRow->shift_id : 0;
		
		if ($defaultVal === 0)
		{
			$defaultValArr = [0 => 'Custom'];
			
			$sysPatternShiftSelection = $defaultValArr + $sysPatternShiftSelection;
		}
		
		$form->addPatternSelect('sysPatternShiftSelect', 'Select type of your shift', $sysPatternShiftSelection)
			->setDefaultValue($defaultVal);
		
		
		// Pattern input
		if ($userPatternRow->sys_pattern_id > 0)
		{
			$patternArr = $this->sysPatternFacadeFactory->create()->getFormPattern($userPatternRow->sys_pattern_id);
		}
		else
		{
			$patternArr = $this->customPatternFacadeFactory->create()->getFormPattern($userPatternRow->custom_pattern_id);
		}
		
		$defaultPattern = $this->buildDefaultInputPattern($patternArr);
		
		$form['patternInput'] = $this->patternInputOverviewFactory->create();
		$form['patternInput']->setDefaultValue($defaultPattern);
		
		$form->addSubmit('send', 'Send')
			->setAttribute('class', 'button');
		
		$form->onSuccess[] = $this->setupFormSubmitted;
		
		return $form;
	}
	
	public function setupFormSubmitted(Form $form) 
	{
		$formValues = $form->getValues();
		
		$userId = $this->user->getId();
		
		try
		{
			$patternFacade = $this->patternFacadeFactory->create();
			
			$originalPatternRow = $patternFacade->getByUserId($userId);
			
			if ($formValues->sysPatternTeamSelect === 0 && $formValues->sysPatternShiftSelect === 0)
			{
				// custom
				$pattern = $this->adjustPattern($formValues->patternInput['pattern'], $formValues->patternInput['firstDay']);
					
				$patternFilter = $this->shiftPatternFilterFactory->create($pattern);
				
				if ($originalPatternRow->custom_pattern_id > 0)
				{
					$customPatternId = $originalPatternRow->custom_pattern_id;
					
					$this->customPatternFacadeFactory->create()->update($customPatternId, $patternFilter);
				}
				else
				{
					
					$customPatternRow = $this->customPatternFacadeFactory->create()->save($patternFilter);
					
					$customPatternId = $customPatternRow->id;
				}
				
				$sysPatternId = 0;
				
				// add custom options to team and shift select boxes				
				$customOption = [0 => 'Custom'];
				$teamOptions = $customOption + $form['sysPatternTeamSelect']->getItems();
				$shiftOptions = $customOption + $form['sysPatternShiftSelect']->getItems();
				
				$form['sysPatternTeamSelect']->setItems($teamOptions);
				$form['sysPatternShiftSelect']->setItems($shiftOptions);
			}
			else
			{
				// system
				if ($originalPatternRow->custom_pattern_id > 0)
				{
					$this->customPatternFacadeFactory->create()->delete($originalPatternRow->custom_pattern_id);
				}
				
				$sysPatternId = $this->sysPatternFacadeFactory->create()->getId($formValues->sysPatternTeamSelect, $formValues->sysPatternShiftSelect);
				
				$customPatternId = 0;
			}
			
			$this->patternFacadeFactory->create()->update($userId, $sysPatternId, $customPatternId);
		}
		catch (\Exception $ex)
		{
			$form->addError('Sorry, something went wrong. Please try again.');
		}
	}
	
	public function getEmploymentLengthValue()
	{
		$type = substr($this->identity->credits, 0, 1);
		
		return $type === 'd' ? 'date' : 'full';
	}
	
	public function getEmploymentDateValue()
	{
		list($type, $val) = explode(':', $this->identity->credits);
		
		return $type === 'd' ? $val : null;	
	}
}
