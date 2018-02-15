<?php

namespace Thunderlabid\Finance\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Validator;

////////////
// EVENTS //
////////////
use Thunderlabid\Finance\Events\Jurnal\JurnalCreated;
use Thunderlabid\Finance\Events\Jurnal\JurnalCreating;
use Thunderlabid\Finance\Events\Jurnal\JurnalUpdated;
use Thunderlabid\Finance\Events\Jurnal\JurnalUpdating;
use Thunderlabid\Finance\Events\Jurnal\JurnalDeleted;
use Thunderlabid\Finance\Events\Jurnal\JurnalDeleting;
use Thunderlabid\Finance\Events\Jurnal\JurnalRestored;
use Thunderlabid\Finance\Events\Jurnal\JurnalRestoring;

use App\Service\Traits\IDRTrait;
use App\Service\Traits\WaktuTrait;

use Config;

class Jurnal extends Model
{
	use SoftDeletes, IDRTrait, WaktuTrait;

	protected $table 	= 'f_jurnal';
	protected $fillable = ['detail_transaksi_id', 'tanggal', 'coa_id', 'jumlah'];
	protected $hidden 	= [];
	protected $appends	= ['debit', 'kredit'];

	protected $rules	= [];
	protected $errors;

	protected $dispatchesEvents = [
        'created' 	=> JurnalCreated::class,
        'creating' 	=> JurnalCreating::class,
        'updated' 	=> JurnalUpdated::class,
        'updating' 	=> JurnalUpdating::class,
        'deleted' 	=> JurnalDeleted::class,
        'deleting' 	=> JurnalDeleting::class,
        'restoring' => JurnalRestoring::class,
        'restored' 	=> JurnalRestored::class,
    ];

	// ------------------------------------------------------------------------------------------------------------
	// CONSTRUCT
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// BOOT
	// ------------------------------------------------------------------------------------------------------------
	
	// ------------------------------------------------------------------------------------------------------------
	// RELATION
	// ------------------------------------------------------------------------------------------------------------
	public function detail(){
		return $this->belongsto(DetailTransaksi::class, 'detail_transaksi_id');
	}

	public function coa(){
		return $this->belongsto(COA::class, 'coa_id');
	}

	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setJumlahAttribute($variable){
		$this->attributes['jumlah']  	= $this->formatMoneyFrom($variable);
	}

	public function setTanggalAttribute($variable){
		$this->attributes['tanggal']  	= $this->formatDateTimeFrom($variable);
	}

	// ------------------------------------------------------------------------------------------------------------
	// ACCESSOR
	// ------------------------------------------------------------------------------------------------------------
	public function getIsDeletableAttribute()
	{
		return true;
	}

	public function getIsSavableAttribute()
	{
		//////////////////
		// Create Rules //
		//////////////////

		//////////////
		// Validate //
		//////////////
		$validator = Validator::make($this->attributes, $rules);
		if ($validator->fails())
		{
			$this->errors = $validator->messages();
			return false;
		}
		return true;
	}

	public function getErrorsAttribute()
	{
		return $this->errors;
	}

	public function getJumlahAttribute($variable){
		return $this->formatMoneyTo($this->attributes['jumlah']);
	}

	public function getTanggalAttribute($variable){
		return $this->formatDateTimeTo($this->attributes['tanggal']);
	}

	public function getDebitAttribute($variable){
		if($this->attributes['jumlah'] > 0){
			return $this->formatMoneyTo($this->attributes['jumlah']);
		}
		return null;
	}

	public function getKreditAttribute($variable){
		if($this->attributes['jumlah'] <= 0){
			return $this->formatMoneyTo(abs($this->attributes['jumlah']));
		}
		return null;
	}
}