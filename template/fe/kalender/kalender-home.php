<div class="section full mt-2">
	<div class="col-12 mb-2">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-2"><a href="<?=$prevURL?>"><span class="iconedbox bg-hijau text-white"><ion-icon name="chevron-back-outline"></ion-icon></span></a></div>
					<div class="col text-center"><h2><?=$bulan_teks?></h2></div>
					<div class="col-2 text-right"><a href="<?=$nextURL?>"><span class="iconedbox bg-hijau text-white"><ion-icon name="chevron-forward-outline"></ion-icon></span></a></div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="wide-block pt-2 pb-2">
		<div id="dcal"></div>
	
		<div class="mt-1">
			<b class="text-dark">Legenda</b>:<br/>
			<?php
			foreach($arrC as $key => $val) {
				$judul = ($key=="shift")? $key : 'libur '.$key;
				echo '<div><span class="rounded" style="padding:3px 6px;background:'.$val['b'].';color:'.$val['t'].'">jadwal '.$judul.'</span></div>';
			}
			?>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	var m = <?=($bulan-1)?>;
    var y = <?=$tahun?>;
	
	// calendar
    var calendar = $("#dcal").fullCalendar({
		defaultDate: '<?=$tahun?>-<?=$bulan2?>-01',
		header: {
			left: "",
			center: "",
			right: ""
		},
		height: 'auto',
		selectable: false,
		selectHelper: false,
		editable: false,
		events: [<?=$dataK?>],
		eventClick: function(info) {
			alert(info.desc);
		},
		viewDestroy: function (view, element) {
			var m = view.intervalStart;
			window.location.href = "<?=SITE_HOST?>/kalender?b="+(m.month()+1)+"&t="+m.year();
			view.preventDefault();
        }
	});
	// remove header fullcalender
	$('.fc-toolbar').remove();
});
</script>