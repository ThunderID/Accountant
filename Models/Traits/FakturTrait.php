<?php

namespace Thunderlabid\Finance\Models\Traits;

use Thunderlabid\Finance\Models\NotaBayar;

/**
 * Trait Link list
 *
 * Digunakan untuk initizialize link list mode
 *
 * @package    TTagihan
 * @author     C Mooy <chelsymooy1108@gmail.com>
 */
trait FakturTrait {
 	 	
	/**
	 * Add Event_list to queue
	 * @param [IEvent_list] $event_list 
	 */
	public function generatenomorfaktur($morph_reference_id)
	{
		$get_paid 		= NotaBayar::where('nomor_faktur', 'like', $morph_reference_id.'%')->count();

		$last_letter	= str_pad(($get_paid + 1), 3, '0', STR_PAD_LEFT);

		return $morph_reference_id.'-'.$last_letter;
	}
}