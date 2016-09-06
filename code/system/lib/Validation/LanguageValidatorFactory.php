<?php


class SJB_LanguageValidatorFactory
{
	var $generalValidationFactory;

	/**
	 * @var HashtableReflector
	 */
	var $dataReflector;
	/**
	 * @var ReflectionFactory
	 */
	protected $reflectionFactory;

	/**
	 * @var I18NDatasource
	 */
	protected $langDataSource;

	function &createUpdateLanguageValidator($lang_data)
	{
		$dataReflector = $this->reflectionFactory->createHashtableReflector($lang_data);
		$this->setDataReflector($dataReflector);

		$factoryReflector = $this->reflectionFactory->createFactoryReflector($this);

		$batch = $this->generalValidationFactory->createValidatorBatch($dataReflector, $factoryReflector);

		$batch->add('active', 'DefaultLanguageMustBeActiveValidator', 'Default language cannot be deactivated');

		return $batch;
	}

	/**
	 * @param I18NDatasource $langDataSource
	 */
	function setLanguageDataSource($langDataSource)
	{
		$this->langDataSource = $langDataSource;
	}

	/**
	 * @param ReflectionFactory $reflectionFactory
	 */
	function setReflectionFactory($reflectionFactory)
	{
		$this->reflectionFactory = $reflectionFactory;
	}
	
	function setContext(&$context)
	{
		$this->context =& $context;
	}
	function setGeneralValidationFactory(&$generalValidationFactory)
	{
		$this->generalValidationFactory =& $generalValidationFactory;
	}

	/**
	 * @param HashtableReflector $dataReflector
	 */
	function setDataReflector(HashtableReflector $dataReflector)
	{
		$this->dataReflector = $dataReflector;
	}

	
	function createThousandsSeparatorValidator()
	{
		$formatValidator = $this->generalValidationFactory->createFormatValidator("/(.)/", $this->context->getValidThousandsSeparators());
		$lengthValidator = $this->generalValidationFactory->createMaxLengthValidator(1);		
		return $this->generalValidationFactory->createAndValidator($formatValidator, $lengthValidator);
	}
	
	function createDecimalsSeparatorValidator()
	{
		$formatValidator = $this->generalValidationFactory->createFormatValidator("/(.)/", $this->context->getValidDecimalsSeparators());
		$lengthValidator = $this->generalValidationFactory->createMaxLengthValidator(1);		
		return $this->generalValidationFactory->createAndValidator($formatValidator, $lengthValidator);
	}
	
	function createDateFormatValidator()
	{
		return $this->generalValidationFactory->createFormatValidator("/%(.?)/", $this->context->getDateFormatValidSymbols());
	}
	
	function createDateFormatLengthValidator()
	{
		return $this->generalValidationFactory->createMaxLengthValidator($this->context->getDateFormatMaxLength());
	}
	
	function createDecimalsValidator()
	{
		return $this->generalValidationFactory->createRegexValidator("/^\d?$/");
	}
	
	function createLanguageCaptionLengthValidator()
	{
		return $this->generalValidationFactory->createMaxLengthValidator($this->context->getLanguageCaptionMaxLength());
	}
	
	function createLanguageNotExistsValidator()
	{
		$source_validator = $this->createLanguageExistsValidator();
		return $this->generalValidationFactory->createNotValidator($source_validator);
	}
	
	function createLanguageExistsValidator()
	{
		$validator = new SJB_LanguageExistsValidator();
		$validator->setLanguageDataSource($this->langDataSource);
		return $validator;
	}
	
	function createLanguageIDLengthValidator()
	{
		return $this->generalValidationFactory->createMaxLengthValidator($this->context->getLanguageIDMaxLength());
	}

	function createNotEmptyValidator()
	{
		return $this->generalValidationFactory->createNotEmptyValidator();
	}
	
	function createIDSymbolsValidator()
	{
		return $this->generalValidationFactory->createRegexValidator('/^[0-9a-zA-Z_]+$/');
	}
	
	function createLanguageIsDefaultValidator()
	{
		$validator = new SJB_LanguageIsDefaultValidator();
		$validator->setContext($this->context);
		return $validator;	
	}
	
	function createLanguageIsNotDefaultValidator()
	{
		$source_validator = $this->createLanguageIsDefaultValidator();
		return $this->generalValidationFactory->createNotValidator($source_validator);
	}
	
	function createLanguageIsActiveValidator()
	{
		$validator = new SJB_LanguageIsActiveValidator();
		$validator->setLanguageDataSource($this->langDataSource);
		$validator->setLanguageExistsValidator($this->createLanguageExistsValidator());
		return $validator;
	}
	
	function createDefaultLanguageMustBeActiveValidator()
	{
		$validator = new SJB_DefaultLanguageMustBeActiveValidator();
		$validator->setDataReflector($this->dataReflector);
		$validator->setLanguageIsNotDefaultValidator($this->createLanguageIsNotDefaultValidator());
		return $validator;
	}
	
	function createDifferentThousandsAndDecimalSeparatorsValidator()
	{
		$equalToValidator = $this->generalValidationFactory->createEqualToValidator($this->dataReflector->get('decimal_separator'));
		return $this->generalValidationFactory->createNotValidator($equalToValidator);
	}
}

