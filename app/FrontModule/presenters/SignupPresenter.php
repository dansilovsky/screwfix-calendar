<?php

namespace FrontModule;

use Nette\Application\UI\Form,
    Screwfix\BaseaccountForm;

/**
 * SignupPresenter
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class SignupPresenter extends BaseaccountPresenter {

	protected function createComponentSignupForm()
	{		
		$form = new BaseaccountForm($this, 'signupForm');

		// credentials part		
		$form->addText('username', null, 30, 30)
			->setAttribute('placeholder', 'Username')
			->addRule(Form::MIN_LENGTH, 'Username must contain at least %d characters.', 3)
			->addRule(Form::MAX_LENGTH, 'Username is too long. Use maximum of %d characters.', 60)
			->addRule(Form::PATTERN, 'Username can contain only alphabetical characters or underscore.', '\w{3,60}');
		$form->addText('email', 'Email', 30, 30)
			->setAttribute('placeholder', 'Email')
			->setRequired('Enter an email please.')
			->addRule(Form::MAX_LENGTH, 'Email is too long. Use maximum of %d characters.', 255)
			->addRule(Form::EMAIL, 'Invalid email address.');
		$form->addPassword('password', null, 30)
			->setAttribute('placeholder', 'Password')
			->setRequired('Enter a password please.')
			->addRule(Form::MIN_LENGTH, 'Password must contain at least %d characters.', 6);
		$form->addPassword('verifyPassword', null, 30)
			->setAttribute('placeholder', 'Retype password')
			->setRequired('Reenter a password please.')
			->addRule(Form::EQUAL, 'Passwords do not match.', $form['password']);
		$form->addCheckbox('remember', 'Remember me');
		
		$employmentLengthSelection = $this->holidayCredits->getFormSelection();
		
		$form->addSelect('employmentLength', 'How many years have you been employed?', $employmentLengthSelection)
			->setAttribute('data-border-years-number', $this->holidayCredits->getBorderYearsNumber());
		
		$form['employmentDate'] = $this->employmentDateInputFactory->create();

		// shift pattern part		
		$sysPatternShiftSelection = $this->shiftFacadeFactory->create()->getFormSelection();
		$sysPatternShiftSelectionComplete = [0 => 'Custom'] + $sysPatternShiftSelection;
		
		$form->addPatternSelect('sysPatternShiftSelect', 'Select your shift', $sysPatternShiftSelection, $sysPatternShiftSelectionComplete);
		
		reset($sysPatternShiftSelection);
		
		$sysPatternSubshiftSelection = $this->subshiftFacadeFactory->create()->getFormSelection(key($sysPatternShiftSelection));		
		$sysPatternSubshiftSelectionComplete = $this->subshiftFacadeFactory->create()->getFormSelectionComplete();
		
		$form->addPatternSelect('sysPatternSubshiftSelect', 'Select your subshift', $sysPatternSubshiftSelection, $sysPatternSubshiftSelectionComplete);

		$sysPattern = $this->sysPatternFacadeFactory->create()->getDefaultFormPattern();
		$defaultPattern = $this->buildDefaultInputPattern($sysPattern);

		$form['patternInput'] = $this->patternInputOverviewFactory->create();
		$form['patternInput']->setDefaultValue($defaultPattern);

		$form->addSubmit('createAccount', 'Create account')
			->setAttribute('class', 'button');

		// common part		
		$form->addProtection('Time limit has expired. Please send the form again.', 1800);
		$form->onSuccess[] = $this->signupFormSubmitted;
		
		$form->onError[] = $this->signupFormError;

		return $form;
	}

	/**
	 *
	 * @param  Form $form
	 */
	public function signupFormSubmitted(Form $form)
	{
		$formValues = $form->getValues();

		$userUsernameRow = $this->userFacade->getByUsername($formValues->username);

		$userEmailRow = $this->userFacade->getByEmail($formValues->email);

		if ($userUsernameRow !== false || $userEmailRow !== false)
		{
			if ($userUsernameRow !== false)
			{
				$form['username']->addError('This username is already taken. Please use different one.');
			}

			if ($userEmailRow !== false)
			{
				$form['email']->addError('This email is already taken. Please use different one.');
			}
		}
		else
		{
			$hashedPassword = \Screwfix\Authenticator::calculateHash($formValues->password);
			
			$userArr = array(
				'username' => $formValues->username,
				'role' => 'member',
				'email' => $formValues->email,
				'password' => $hashedPassword,
				'credits' => $this->workOutFormEmployment($formValues)
			);
			
			$patternFacadeFactory = $this->patternFacadeFactory->create();

			try
			{
				$this->userFacade->save($userArr);

				$user = $this->getUser();

				if ($formValues->remember)
				{
					$user->setExpiration('+14 days', false);
				}

				$user->login($formValues->username, $formValues->password);

				if ($formValues->sysPatternShiftSelect === 0)
				{
					$pattern = $this->adjustPattern($formValues->patternInput['pattern'], $formValues->patternInput['firstDay']);
					
					$patternFilter = $this->shiftPatternFilterFactory->create($pattern);
					
					$customPatternRow = $this->customPatternFacadeFactory->create()->save($patternFilter);
					
					$patternFacadeFactory->save($user->getId(), 0, $customPatternRow->id);
				}
				else
				{
					$sysPatternId = $this->sysPatternFacadeFactory->create()
						->getId($formValues->sysPatternShiftSelect, $formValues->sysPatternSubshiftSelect);
					
					$patternFacadeFactory->save($user->getId(), $sysPatternId, 0);
				}
				
				$this->flashMessage('Your account has been successfully created.', 'form:success');
			}
			catch (\Exception $ex)
			{
				$form->addError('Sorry, something went wrong. Please try again.');
			}

			$this->redirect('Home:default');
		}
	}
	
	/**
	 * If we get error while client sent customized pattern 
	 * then we need to add custom option to team select and shift select.
	 * 
	 * @param Form $form
	 */
	public function signupFormError(Form $form)
	{
		$formValues = $form->getValues();		
		
//		\Nette\Diagnostics\Debugger::dump($formValues);
//		exit;
		
		if ($formValues->sysPatternShiftSelect === 0)
		{
			$addItems = [0 => 'Custom'];
			
			$origItems = $form['sysPatternShiftSelect']->getItems();
			
			$newItems = $addItems + $origItems;
			
			$form['sysPatternShiftSelect']->setItems($newItems);
		}
		
		if ($formValues->sysPatternSubshiftSelect === 0)
		{			
			$form['sysPatternSubshiftSelect']->setItems(array());
		}
		else
		{
			$shiftId = (int) $formValues->sysPatternShiftSelect;
			
//			$shiftId = "1 OR 1";
			
			$sysPatternSubshiftSelection = $this->subshiftFacadeFactory->create()->getFormSelection($shiftId);
			
			$form['sysPatternSubshiftSelect']->setItems($sysPatternSubshiftSelection);
		}
	}

}
