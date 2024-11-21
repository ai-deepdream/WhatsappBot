<?php

/**
 * @method sendCount(int $param)
 */
class Admit extends core
{
    public $ID;
    public $systemID;
    public $rn;
    public $mobile;
    public $message;
    public $mode;
    public $stateID;
    public $state;
    public $debt;
    public $billID;
    public $link;
    public $sendCount;
    public $read;
    public $log;
    public $pdfCode;
    public $smsLog;
    public $incomTs;
    public $sendTs;
    public $smsSendTs;
    public $surveyLink;
    public $surveySendTs;

    public function make_pdf_code(): string
    {
        if (!$this->pdfCode){
            $lastAdmit = new self();
            do{
                $code = rand_string(6, 'letter');
                $lastAdmit->set(pdfCode: $code);
            }while ($lastAdmit->check());
            $this->pdfCode ($code);
        }
        return (string)$this->pdfCode;
    }
}
