<?php

namespace Thunderlabid\Finance\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

use Validator;

////////////
// EVENTS //
////////////
use Thunderlabid\Finance\Events\COA\COACreated;
use Thunderlabid\Finance\Events\COA\COACreating;
use Thunderlabid\Finance\Events\COA\COAUpdated;
use Thunderlabid\Finance\Events\COA\COAUpdating;
use Thunderlabid\Finance\Events\COA\COADeleted;
use Thunderlabid\Finance\Events\COA\COADeleting;
use Thunderlabid\Finance\Events\COA\COARestored;
use Thunderlabid\Finance\Events\COA\COARestoring;

class COA extends Model
{
	use SoftDeletes;

	protected $table 	= 'f_coa';
	protected $fillable = ['kode_kantor', 'nomor_perkiraan', 'akun', 'coa_id', 'mata_uang'];
	protected $hidden 	= [];
	protected $appends	= [];

	protected $rules	= [];
	protected $errors;

	protected $dispatchesEvents = [
        'created' 	=> COACreated::class,
        'creating' 	=> COACreating::class,
        'updated' 	=> COAUpdated::class,
        'updating' 	=> COAUpdating::class,
        'deleted' 	=> COADeleted::class,
        'deleting' 	=> COADeleting::class,
        'restoring' => COARestoring::class,
        'restored' 	=> COARestored::class,
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
	public function subakun(){
		return $this->hasmany(COA::class, 'coa_id');
	}

	public function coas(){
		return $this->hasMany(COA::class, 'nomor_perkiraan', 'nomor_perkiraan');
	}

	public function detailsin(){
		return $this->belongsToMany(DetailTransaksi::class, 'f_jurnal', 'coa_id', 'detail_transaksi_id')->where('f_jurnal.jumlah', '>=', 0)->selectraw('f_jurnal.jumlah as amount');
	}

	public function detailsout(){
		return $this->belongsToMany(DetailTransaksi::class, 'f_jurnal', 'coa_id', 'detail_transaksi_id')->where('f_jurnal.jumlah', '<=', 0)->selectraw('f_jurnal.jumlah as amount');
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
		$rules['kode_kantor'] 		= ['required', 'string'];
		$rules['akun'] 				= ['required', 'string'];
		$rules['nomor_perkiraan'] 	= ['required', 'string'];

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
}
