<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceDetailTransaksiTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('f_detail_transaksi', function (Blueprint $table) {
			$table->increments('id');
			$table->string('morph_reference_id')->nullable();
			$table->string('morph_reference_tag')->nullable();
			$table->string('nomor_faktur')->nullable();
			$table->string('tag');
			$table->double('jumlah');
			$table->text('deskripsi');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'nomor_faktur', 'tag']);
			$table->index(['deleted_at', 'morph_reference_id', 'tag']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('f_detail_transaksi');
	}
}
