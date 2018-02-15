<?php

namespace Thunderlabid\Finance;

use Illuminate\Support\ServiceProvider;
use Event, Config;

class FinanceServiceProvider extends ServiceProvider
{
	public function boot()
	{
		////////////////
		// Validation //
		////////////////
		Event::listen('Thunderlabid\Finance\Events\COA\COACreating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\COA\COAUpdating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\COA\COADeleting', 'Thunderlabid\Finance\Listeners\Deleting');

		Event::listen('Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiCreating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiUpdating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\DetailTransaksi\DetailTransaksiDeleting', 'Thunderlabid\Finance\Listeners\Deleting');

		Event::listen('Thunderlabid\Finance\Events\Jurnal\JurnalCreating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\Jurnal\JurnalUpdating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\Jurnal\JurnalDeleting', 'Thunderlabid\Finance\Listeners\Deleting');

		Event::listen('Thunderlabid\Finance\Events\NotaBayar\NotaBayarCreating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\NotaBayar\NotaBayarUpdating', 'Thunderlabid\Finance\Listeners\Saving');
		Event::listen('Thunderlabid\Finance\Events\NotaBayar\NotaBayarDeleting', 'Thunderlabid\Finance\Listeners\Deleting');

		////////////////////////////////////////////////////
		// Tidak boleh melakukan pembayaran setelah jam 3 //
		////////////////////////////////////////////////////
		Event::listen('Thunderlabid\Finance\Events\NotaBayar\NotaBayarCreating', 'Thunderlabid\Finance\Listeners\NoPaymentAfter3PM');
		Event::listen('Thunderlabid\Finance\Events\NotaBayar\NotaBayarUpdating', 'Thunderlabid\Finance\Listeners\NoPaymentAfter3PM');
	}

	public function register()
	{
		Config::set('finance.nomor_rekening_aktif', ['100.110', '100.120', '110.000']);
		Config::set('finance.nomor_perkiraan_denda', '140.600');
		Config::set('finance.nomor_perkiraan_titipan', '100.300');
		Config::set('finance.nomor_perkiraan_titipan_kolektor', '100.310');
	}
}