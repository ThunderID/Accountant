<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceNotaBayarTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('f_nota_bayar', function (Blueprint $table) {
			$table->increments('id');
			$table->string('morph_reference_id')->nullable();
			$table->string('morph_reference_tag')->nullable();
			$table->string('nomor_rekening')->nullable();
			$table->string('nomor_faktur');
			$table->datetime('tanggal');
			$table->double('jumlah');
			$table->string('jenis');
			$table->text('karyawan')->nullable();
			$table->timestamps();
			$table->softDeletes();
			
            $table->index(['deleted_at', 'morph_reference_id']);
            $table->index(['deleted_at', 'nomor_faktur', 'morph_reference_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('f_nota_bayar');
	}
}
