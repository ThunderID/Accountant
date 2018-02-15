<?php

namespace Thunderlabid\Finance\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;

use Config;
use Validator;
use Carbon\Carbon;
use App\Service\Traits\WaktuTrait;
use App\Service\Traits\TanggalTrait;
use App\Service\Traits\IDRTrait;

///////////////
// Exception //
///////////////
use App\Exceptions\AppException;

////////////
// EVENTS //
////////////
use Thunderlabid\Finance\Events\NotaBayar\NotaBayarCreated;
use Thunderlabid\Finance\Events\NotaBayar\NotaBayarCreating;
use Thunderlabid\Finance\Events\NotaBayar\NotaBayarUpdated;
use Thunderlabid\Finance\Events\NotaBayar\NotaBayarUpdating;
use Thunderlabid\Finance\Events\NotaBayar\NotaBayarDeleted;
use Thunderlabid\Finance\Events\NotaBayar\NotaBayarDeleting;
use Thunderlabid\Finance\Events\NotaBayar\NotaBayarRestored;
use Thunderlabid\Finance\Events\NotaBayar\NotaBayarRestoring;

use Thunderlabid\Finance\Models\Traits\FakturTrait;

class NotaBayar extends Model
{
	use WaktuTrait;
	use TanggalTrait;
	use IDRTrait;
	use FakturTrait;
	
	protected $table 	= 'f_nota_bayar';
	protected $fillable = ['nomor_faktur', 'tanggal', 'jenis', 'karyawan', 'jumlah', 'nomor_rekening', 'morph_reference_id', 'morph_reference_tag'];
	protected $hidden 	= [];
	protected $appends	= ['jatuh_tempo', 'hari'];

	protected $rules	= [];
	protected $errors;

	protected $events = [
		'created' 	=> NotaBayarCreated::class,
		'creating' 	=> NotaBayarCreating::class,
		'updated' 	=> NotaBayarUpdated::class,
		'updating' 	=> NotaBayarUpdating::class,
		'deleted' 	=> NotaBayarDeleted::class,
		'deleting' 	=> NotaBayarDeleting::class,
		'restoring' => NotaBayarRestoring::class,
		'restored' 	=> NotaBayarRestored::class,
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
	public function details(){
		return $this->hasMany(DetailTransaksi::class, 'nomor_faktur', 'nomor_faktur');
	}

	// ------------------------------------------------------------------------------------------------------------
	// FUNCTION
	// ------------------------------------------------------------------------------------------------------------

	// ------------------------------------------------------------------------------------------------------------
	// SCOPE
	// ------------------------------------------------------------------------------------------------------------
	public function scopeDisplaying($query){
		return $query->select('k_nota_bayar.*')
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.tag = 'pokok' and td.deleted_at is null) as pokok"))
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.tag = 'bunga' and td.deleted_at is null) as bunga"))
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.tag = 'denda' and td.deleted_at is null) as denda"))
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.tag = 'titipan' and td.deleted_at is null) as titipan"))
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.tag = 'potongan' and td.deleted_at is null) as potongan"))
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.tag = 'restitusi_denda' and td.deleted_at is null) as restitusi_denda"))
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.deleted_at is null) as subtotal"))
			->where('jumlah', '>', 0);
	}

	public function scopeCountAmount($query){
		return $query->select('k_nota_bayar.*')
			->selectraw(\DB::raw("(select sum(td.amount) from k_angsuran_detail as td where k_nota_bayar.id = td.nota_bayar_id and td.deleted_at is null) as amount"));
	}

	// ------------------------------------------------------------------------------------------------------------
	// MUTATOR
	// ------------------------------------------------------------------------------------------------------------
	public function setTanggalAttribute($variable)
	{
		$this->attributes['tanggal']		= $this->formatDateTimeFrom($variable);
	}

	public function setJumlahAttribute($variable)
	{
		$this->attributes['jumlah']			= $this->formatMoneyFrom($variable);
	}

	public function setKaryawanAttribute($variable)
	{
		$this->attributes['karyawan']		= json_encode($variable);
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
		$rules['nomor_faktur'] 		= ['required', 'string'];
		$rules['tanggal'] 			= ['required', 'date_format:"Y-m-d H:i:s"'];

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

	public function getTanggalAttribute($variable)
	{
		return $this->formatDateTimeTo($this->attributes['tanggal']);
	}

	public function getHariAttribute($variable)
	{
		return $this->formatDateTo($this->attributes['tanggal']);
	}

	public function getJumlahAttribute($variable)
	{
		return $this->formatMoneyTo($this->attributes['jumlah']);
	}

	public function getKaryawanAttribute($variable)
	{
		return json_decode($this->attributes['karyawan'], true);
	}

	public function getJatuhTempoAttribute($variable){
		return Carbon::parse($this->attributes['tanggal'])->addDays(Config::get('kredit.batas_pembayaran_angsuran_hari'))->format('d/m/Y H:i');
	}

	public function getDeskripsiAttribute($variable){
		$deskripsi 	= 'Bukti Transaksi';
		switch ($this->attributes['jenis']) {
			case 'pencairan':
				$deskripsi 	= 'Bukti Pencairan Kredit';
				break;
			case 'setoran_pencairan':
				$deskripsi 	= 'Bukti Setoran Pencairan Kredit';
				break;
			case 'angsuran':
				$deskripsi 	= 'Bukti Angsuran Kredit';
				break;	
			case 'angsuran_sementara':
				$deskripsi 	= 'Bukti Angsuran Sementara';
				break;	
			case 'restitusi_denda':
				$deskripsi 	= 'Bukti Restitusi Denda';
				break;	
			case 'denda':
				$deskripsi 	= 'Bukti Pembayaran Denda';
				break;	
			case 'memorial_titipan':
				$deskripsi 	= 'Bukti Memorial Pengambilan Titipan';
				break;	
		}

		return $deskripsi;
	}
}
