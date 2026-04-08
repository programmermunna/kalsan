<?php

namespace App\Traits;

use App\Models\CustomerCreditNotes;
use App\Models\CustomerDebitNotes;

trait updateNotesStatus
{
    public function updateCreditNoteStatus($customerCreditNote , $status = null)
    {
        if($customerCreditNote != null)
        {
            $creditNote = CustomerCreditNotes::find($customerCreditNote->credit_note);
            if($status == 'delete')
            {
                $usedCreditNote = $creditNote->usedCreditNote() - $customerCreditNote->amount;
            }
            else
            {
                $usedCreditNote = $creditNote->usedCreditNote();
            }
            
            if($usedCreditNote == $creditNote->amount)
            {
                $creditNote->status = 2;
                $creditNote->save();
            }
            else if($usedCreditNote == 0)
            {
                $creditNote->status = 0;
                $creditNote->save();
            }
            else
            {
                $creditNote->status = 1;
                $creditNote->save();
            }
        }
    }

    public function updateDebitNoteStatus($customerDebitNote , $status = null)
    {
        if($customerDebitNote != null)
        {
            $debitNote = CustomerDebitNotes::find($customerDebitNote->debit_note);
            if($status == 'delete')
            {
                $usedDebitNote = $debitNote->usedDebitNote($debitNote) - $customerDebitNote->amount;
            }
            else
            {
                $usedDebitNote = $debitNote->usedDebitNote($debitNote);
            }
            
            if($usedDebitNote == $debitNote->amount)
            {
                $debitNote->status = 2;
                $debitNote->save();
            }
            else if($usedDebitNote == 0)
            {
                $debitNote->status = 0;
                $debitNote->save();
            }
            else
            {
                $debitNote->status = 1;
                $debitNote->save();
            }
        }
    }

}