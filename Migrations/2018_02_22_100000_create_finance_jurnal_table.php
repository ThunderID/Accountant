<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceJurnalTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('f_jurnal', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('detail_transaksi_id');
			$table->datetime('tanggal');
			$table->string('coa_id');
			$table->double('jumlah');
			$table->string('morph_reference_id')->nullable();
			$table->string('morph_reference_tag')->nullable();
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'tanggal', 'morph_reference_id']);
			$table->index(['deleted_at', 'tanggal', 'coa_id']);
			$table->index(['deleted_at', 'tanggal', 'detail_transaksi_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('f_jurnal');
	}
}
