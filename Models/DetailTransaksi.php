<?php

namespace Thunderlabid\Finance\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;

use Validator, Config;
use Carbon\Carbon;
use App\Service\Traits\IDRTrait;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiCreated;
use Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiCreating;
use Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiUpdated;
use Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiUpdating;
use Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiDeleted;
use Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiDeleting;
use Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiRestored;
use Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiRestoring;

class DetailTransaksi extends Model
{
	use IDRTrait;
	
	protected $table 	= 'f_detail_transaksi';
	protected $fillable = ['nomor_faktur', 'tag', 'jumlah', 'deskripsi', 'morph_reference_id', 'morph_reference_tag'];
	protected $hidden 	= [];
	protected $appends	= [];
	protected $rules	= [];
	protected $errors;

	protected $events = [
        'created' 	=> DetailTransaksiCreated::class,
        'creating' 	=> DetailTransaksiCreating::class,
        'updated' 	=> DetailTransaksiUpdated::class,
        'updating' 	=> DetailTransaksiUpdating::class,
        'deleted' 	=> DetailTransaksiDeleted::class,
        'deleting' 	=> DetailTransaksiDeleting::class,
        'restoring' => DetailTransaksiRestoring::class,
        'restored' 	=> DetailTransaksiRestored::class,
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
	public function notabayar(){
		return $this->belongsto(NotaBayar::class, 'nomor_faktur', 'nomor_faktur');
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

	public function setJumlahAttribute($variable)
	{
		$this->attributes['jumlah']		= $this->formatMoneyFrom($variable);
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
		$rules['nomor_faktur']	= ['nullable', 'exists:f_nota_bayar,nomor_faktur'];
		$rules['jumlah'] 		= ['required', 'numeric'];

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

	public function getJumlahAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['jumlah']);
	}
}
