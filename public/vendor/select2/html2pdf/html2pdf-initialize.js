function addScript(url) {
    var script = document.createElement('script');
    script.type = 'application/javascript';
    script.src = url;
    document.head.appendChild(script);
}
$( document ).ajaxStop(function() {
    addScript('https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js');
    addScript('https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.7/dist/html2canvas.min.js');
});


function idevHtmlToPdf(params) {
    var element = document.getElementById(params);
    var opt = {
        margin:       1,
        filename:     params+'.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
      };
    // New Promise-based usage:
    html2pdf().set(opt).from(element).save();
}

function captureDivAsImage(divId) {
    var element = document.getElementById(divId);
    html2canvas(element).then(function(canvas) {
        var imgData = canvas.toDataURL("image/png");

        // Create an image element
        var imgElement = document.createElement("img");
        imgElement.src = imgData;

        // Append the image to the body or a specific element
        document.body.appendChild(imgElement);

        // Optionally, you can also download the image
        var link = document.createElement('a');
        link.href = imgData;
        link.download = divId + '.png';
        link.click();
    });
}
