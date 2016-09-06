<?php

class SJB_PayPalPro extends SJB_PaymentGateway
{
	private $httpParsedResponseAr;
	public $errors = array();
	public $amountField = 'amount';

	function SJB_PayPalPro($gateway_info)
	{
		parent::SJB_PaymentGateway($gateway_info);
		$this->details = new SJB_PayPalProDetails($gateway_info);
	}

	public function isValid()
	{
		$this->validate();
		return empty($this->errors);
	}

	public function buildTransactionForm($invoice)
	{
		if (count($invoice->isValid()) == 0) {
			return array(
				'url' => $this->getPaypalProFillPaymentCardUrl($invoice),
				'caption' => 'PayPal Pro',
			);
		}

		return null;
	}

	public function makePayment($data)
	{
		$this->sendPaymentToPaypal($data);
		$this->setPaymentStatus();
		$this->continuePaymentProcess();
	}

	public function getPaymentFromCallbackData($callback_data)
	{
		$invoice_sid = isset($callback_data['item_number']) ? $callback_data['item_number'] : null;
		if (is_null($invoice_sid)) {
			$this->errors['INVOICE_ID_IS_NOT_SET'] = 1;
			return null;
		}
		$invoice = SJB_InvoiceManager::getObjectBySID($invoice_sid);
		if (is_null($invoice)) {
			$this->errors['NONEXISTED_INVOICE_ID_SPECIFIED'] = 1;
			return null;
		}

		$invoice->setCallbackData($callback_data);
		if (!$this->checkPaymentAmount($invoice)) {
			return null;
		}
		$gatewayId = $this->details->getProperty('id');
		$invoice->setPropertyValue('payment_method', $gatewayId->getValue());
		if (isset($callback_data['http_post_response']['TRANSACTIONID'])) {
			$transactionId = $callback_data['http_post_response']['TRANSACTIONID'];
			$transactionInfo = array(
				'transaction_id' => $transactionId,
				'invoice_sid' => $invoice->getSID(),
				'amount' => $invoice->getPropertyValue('total'),
				'payment_method' => $invoice->getPropertyValue('payment_method'),
				'user_sid' => $invoice->getPropertyValue('user_sid')
			);
			$transaction = new SJB_Transaction($transactionInfo);
			SJB_TransactionManager::saveTransaction($transaction);
		}
		return $invoice;
	}

	private function getGatewayUrl()
	{
		$sub_domain = $this->getPropertyValue('use_sandbox') ? 'api-3t.sandbox' : 'api-3t';
		return "https://{$sub_domain}.paypal.com/nvp";
	}

	private function validate()
	{
		$properties = $this->details->getProperties();
		$user_name = $properties['user_name']->getValue();
		$user_password = $properties['user_password']->getValue();
		$user_signature = $properties['user_signature']->getValue();
		if (empty($user_name)) {
			$this->errors['USER_NAME_IS_NOT_SET'] = 1;
		}
		if (empty($user_password)) {
			$this->errors['USER_PASSWORD_IS_NOT_SET'] = 1;
		}
		if (empty($user_signature)) {
			$this->errors['USER_SIGNATURE_IS_NOT_SET'] = 1;
		}
	}

	private function continuePaymentProcess()
	{
		$this->prepareRequest();
		$this->prepareUri();
		$function = new SJB_Payment_Callback(SJB_Acl::getInstance(), array(), null);
		$function->execute();
	}

	private function sendPaymentToPaypal($data)
	{
		$invoiceSid = SJB_Request::getInt('item_number', null);
		if (is_null($invoiceSid)) {
			$this->errors['INVOICE_ID_IS_NOT_SET'] = 1;
			return null;
		}
		$customerDataString = $this->makeRequestForDirectPayment($data);
		$this->httpParsedResponseAr = $this->paypalHttpPost('DoDirectPayment', $customerDataString);
	}

	private function setPaymentStatus()
	{
		$invoiceSid = SJB_Request::getInt('item_number', null);
		if (is_null($invoiceSid)) {
			$this->errors['INVOICE_ID_IS_NOT_SET'] = 1;
			return null;
		}
		$invoice = SJB_InvoiceManager::getObjectBySID($invoiceSid);
		$status = false;
		if (is_null($invoice)) {
			$this->errors['NONEXISTED_INVOICE_ID_SPECIFIED'] = 1;
			return null;
		}

		$invoice->setCallbackData($this->httpParsedResponseAr);
		if (in_array(strtoupper($this->httpParsedResponseAr['ACK']), array('SUCCESS', 'SUCCESSWITHWARNING'))) {
			$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_VERIFIED);
		} else {
			$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
		}
		SJB_InvoiceManager::saveInvoice($invoice);
	}

	private function prepareRequest()
	{
		if (!empty($this->httpParsedResponseAr)) {
			$_REQUEST['http_post_response'] = $this->httpParsedResponseAr;
		}
		unset(
			$_REQUEST['user_name'],
			$_REQUEST['user_password'],
			$_REQUEST['user_signature'],
			$_REQUEST['gateway_url'],
			$_REQUEST['card_type'],
			$_REQUEST['card_number'],
			$_REQUEST['exp_date_mm'],
			$_REQUEST['exp_date_yy'],
			$_REQUEST['csc_value'],
			$_REQUEST['first_name'],
			$_REQUEST['last_name'],
			$_REQUEST['address'],
			$_REQUEST['zip'],
			$_REQUEST['country'],
			$_REQUEST['city'],
			$_REQUEST['state'],
			$_REQUEST['email'],
			$_REQUEST['phone']
		);
	}

	private function prepareUri()
	{
		$_SERVER['REQUEST_URI'] = SJB_System::getSystemSettings('SITE_URL') . "/system/payment/callback/" . $_REQUEST['item_number'] . '/paypal_pro/';
	}

	private function getPaypalProFillPaymentCardUrl($invoice)
	{
		return SJB_System::getSystemSettings('SITE_URL') . "/paypal-pro-fill-payment-card/?payment_id={$invoice->getSID()}&gateway_id={$this->getPropertyValue('id')}";
	}

	private function paypalHttpPost($methodName_, $customer_data_string)
	{
		$gatewayProperties = $this->getGatewayProperties();
		$query_data = "METHOD={$methodName_}&" . http_build_query($gatewayProperties) . $customer_data_string;
		try {
			$httpResponse = $this->doPostRequest($gatewayProperties['API_Endpoint'], $query_data);
			$httpParsedResponseAr = $this->parseHttpResponse($httpResponse);
		} catch (HttpException $e) {
			exit("{$gatewayProperties['API_Endpoint']} $methodName_ failed: " . $e->getMessage());
		}
		return $httpParsedResponseAr;
	}

	private function getGatewayProperties()
	{
		return array(
			'VERSION' => urlencode('74.0'),
			'PWD' => urlencode($this->getPropertyValue('user_password')),
			'USER' => urlencode($this->getPropertyValue('user_name')),
			'SIGNATURE' => urlencode($this->getPropertyValue('user_signature')),
			'API_Endpoint' => $this->getGatewayUrl(),
		);
	}

	private function doPostRequest($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$httpResponse = curl_exec($ch);
		if (!$httpResponse) {
			echo ("CURL error " . curl_error($ch) . '(' . curl_errno($ch) . ')');
		}
		return $httpResponse;
	}

	private function parseHttpResponse($httpResponse)
	{
		$httpResponseAr = explode("&", $httpResponse);
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $value) {
			$tmpAr = explode("=", $value);
			if (sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}
		if (!array_key_exists('ACK', $httpParsedResponseAr)) {
			echo ("ACK not found in Response data.");
		}
		return $httpParsedResponseAr;
	}

	private function makeRequestForDirectPayment($getData)
	{
		$expDateMonth = $getData['exp_date_mm'];
		$padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
		$customer = array(
			'PAYMENTACTION' => urlencode('Sale'),
			'AMT' => urlencode($getData['amount']),
			'CREDITCARDTYPE' => '',
			'ACCT' => urlencode($getData['card_number']),
			'EXPDATE' => $padDateMonth . $expDateYear = urlencode($getData['exp_date_yy']),
			'CVV2' => urlencode($getData['csc_value']),
			'FIRSTNAME' => urlencode($getData['first_name']),
			'LASTNAME' => urlencode($getData['last_name']),
			'STREET' => urlencode($getData['address']),
			'CITY' => urlencode($getData['city']),
			'STATE' => urlencode($getData['state']),
			'ZIP' => urlencode($getData['zip']),
			'COUNTRYCODE' => urlencode($getData['country']),
			'CURRENCYCODE' => urlencode($getData['currency_code']),
			'BUTTONSOURCE' => urlencode($getData['bn'])
		);
		return '&' . http_build_query($customer);
	}

	function getPaymentStatusFromCallbackData($callback_data)
	{
		$isCalledFromIpnListener = isset($callback_data['txn_type']) && $callback_data['txn_type'] == 'subscr_payment';
		if ($isCalledFromIpnListener) {
			return $this->parseIpnStatus($callback_data);
		} else {
			return $this->parseNvpApiStatus($callback_data);
		}
	}

	public function parseIpnStatus($callback_data)
	{
		// https://www.x.com/developers/paypal/documentation-tools/ipn/integration-guide/IPNandPDTVariables
		// payment_status values:
		//   Pending
		//   Completed
		//   Denied
		$payment_status = isset($callback_data['payment_status']) ? strtolower($callback_data['payment_status']) : '';
		switch ($payment_status) {
			case 'pending':
				return 'Pending';
			case 'completed':
				return 'Successful';
			case 'denied':
				return 'Error';
		}
		return 'Notification';
	}

	public function parseNvpApiStatus($callback_data)
	{
		// https://www.x.com/developers/paypal/documentation-tools/api/NVPAPIOverview
		// Acknowledgement status, which is one of the following values:
		//   Success
		//   SuccessWithWarning
		//   Failure
		//   FailureWithWarning
		$ack = isset($callback_data['http_post_response']) && isset($callback_data['http_post_response']['ACK']) ?
			strtolower($callback_data['http_post_response']['ACK']) :
			'';
		switch ($ack) {
			case 'success':
			case 'successwithwarning':
				return 'Successful';
			case 'failure':
			case 'failurewithwarning':
				return 'Error';
		}
		return 'Notification';
	}
}
