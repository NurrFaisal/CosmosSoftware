<?php

namespace App;

use App\model\BankBook;
use App\model\CashBook;
use App\model\ReceivedLoan;
use Illuminate\Database\Eloquent\Model;

class RLInstallment extends Model
{
    protected $guarded = [];
    public function cashs(){
        return $this->hasMany(CashBook::class, 'rl_installment_id', 'id');
    }
    public function cheques(){
        return $this->hasMany(BankBook::class, 'rl_installment_id', 'id');
    }
    public function head(){
        return $this->belongsTo(ReceivedLoan::class, 'loan_id');
    }
}
