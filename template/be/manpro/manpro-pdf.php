<ul class="breadcrumb">
	<li class="breadcrumb-item">
		<a href="#">Home</a>
	</li>
	<li class="breadcrumb-item">
		<a href="#">Manajemen Proyek</a>
	</li>
	<li class="breadcrumb-item">
		<span><?=$this->pageTitle?></span>
	</li>
</ul>

<div class="content-i">
	<div class="content-box">
		<div class="element-wrapper">
			<h5 class="element-header"><?=$this->pageTitle?></h5>
			
			<div id="pdf_content"></div>
		</div>
	</div>
</div>

<script src="<?=SITE_HOST?>/third_party/pdfjs/build/pdf.js"></script>
<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = '<?=SITE_HOST?>/third_party/pdfjs/build/pdf.worker.js';

function extractText(pdfUrl) {
	var pdf = pdfjsLib.getDocument(pdfUrl);
	return pdf.promise.then(function (pdf) {
		// var totalPageCount = pdf.numPages;
		var currentPage = 2;
		var countPromises = [];
		var page = pdf.getPage(currentPage);
		countPromises.push(
			page.then(function (page) {
				var textContent = page.getTextContent();
				return textContent.then(function (text) {
					return text.items
						.map(function (s) {
							return s.str;
						})
						.join('');
				});
			}),
		);
		
		return Promise.all(countPromises).then(function (texts) {
			return texts.join('<br/>');
		});
	});
}

const url = '<?=SITE_HOST?>/media/kegiatan/2/BOP2043.pdf';

extractText(url).then(
	function (text) {
		$("#pdf_content").html(text);
	},
	function (reason) {
		$("#pdf_content").html("Err: "+reason);
	},
);
</script>