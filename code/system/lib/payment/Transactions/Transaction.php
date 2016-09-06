<?php

class SJB_Transaction extends SJB_Object
{
    function SJB_Transaction($transaction_info = array())
    {
        $this->details = new SJB_TransactionDetails($transaction_info);
    }

    function setTransactionID($transaction_id)
    {
        $this->setPropertyValue('transaction_id', $transaction_id);
    }

    function getTransactionID()
    {
        return $this->getPropertyValue('transaction_id');
    }
}
